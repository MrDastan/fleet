<?php

namespace App\Services;

use App\Models\AnomalyRecord;
use App\Models\FuelRecord;
use App\Models\MovementLog;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Illuminate\Support\Collection;

class AnomalyEngine
{
    public function scan(): Collection
    {
        $detected = collect();

        $detected = $detected->merge($this->checkHighFuelConsumption());
        $detected = $detected->merge($this->checkMileageJumps());
        $detected = $detected->merge($this->checkFrequentRefueling());
        $detected = $detected->merge($this->checkServiceOverdue());
        $detected = $detected->merge($this->checkMovementWithoutApproval());
        $detected = $detected->merge($this->checkAfterHoursUsage());
        $detected = $detected->merge($this->checkWeekendUsage());

        foreach ($detected as $item) {
            $exists = AnomalyRecord::where('rule_code', $item['rule_code'])
                ->where('vehicle_id', $item['vehicle_id'])
                ->where('status', '!=', 'resolved')
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if (!$exists) {
                AnomalyRecord::create($item);
            }
        }

        return $detected;
    }

    private function checkHighFuelConsumption(): Collection
    {
        $records = FuelRecord::with('vehicle')
            ->where('consumption_l100km', '>', 12)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return $records->map(fn($r) => [
            'vehicle_id' => $r->vehicle_id,
            'user_id' => $r->user_id,
            'rule_code' => 'FUEL_HIGH',
            'severity' => $r->consumption_l100km > 15 ? 'critical' : 'warning',
            'title' => "Penggunaan bahan api tinggi — {$r->vehicle->plat}",
            'description' => "Penggunaan {$r->consumption_l100km} L/100km pada " . $r->datetime->format('d M Y') . " di {$r->station}. Melebihi had biasa.",
            'detected_data' => ['consumption' => $r->consumption_l100km, 'liters' => $r->liters, 'station' => $r->station, 'date' => $r->datetime->toDateString()],
        ]);
    }

    private function checkMileageJumps(): Collection
    {
        $results = collect();
        $vehicles = Vehicle::all();

        foreach ($vehicles as $v) {
            $records = FuelRecord::where('vehicle_id', $v->id)
                ->orderBy('odometer_km')->get();

            for ($i = 1; $i < $records->count(); $i++) {
                $diff = $records[$i]->odometer_km - $records[$i - 1]->odometer_km;
                if ($diff > 500) {
                    $results->push([
                        'vehicle_id' => $v->id,
                        'user_id' => $records[$i]->user_id,
                        'rule_code' => 'MILEAGE_JUMP',
                        'severity' => $diff > 1000 ? 'critical' : 'warning',
                        'title' => "Lonjakan jarak tinggi — {$v->plat}",
                        'description' => "Perbezaan {$diff} km antara dua pengisian bahan api. Mungkin penggunaan tidak direkod.",
                        'detected_data' => ['km_diff' => $diff, 'from' => $records[$i - 1]->odometer_km, 'to' => $records[$i]->odometer_km],
                    ]);
                }
            }
        }

        return $results;
    }

    private function checkFrequentRefueling(): Collection
    {
        $results = collect();
        $vehicles = Vehicle::all();

        foreach ($vehicles as $v) {
            $count = FuelRecord::where('vehicle_id', $v->id)
                ->where('datetime', '>=', now()->subDays(7))
                ->count();

            if ($count >= 3) {
                $results->push([
                    'vehicle_id' => $v->id,
                    'rule_code' => 'FREQ_REFUEL',
                    'severity' => 'warning',
                    'title' => "Pengisian berulang — {$v->plat}",
                    'description' => "{$count} kali pengisian dalam 7 hari lepas. Sila semak jika ada kebocoran atau penggunaan tidak wajar.",
                    'detected_data' => ['count_7days' => $count],
                ]);
            }
        }

        return $results;
    }

    private function checkServiceOverdue(): Collection
    {
        $vehicles = Vehicle::whereNotNull('next_service_date')
            ->where('next_service_date', '<', now())
            ->where('status', '!=', 'servis')
            ->get();

        return $vehicles->map(fn($v) => [
            'vehicle_id' => $v->id,
            'rule_code' => 'SERVICE_OVERDUE',
            'severity' => 'warning',
            'title' => "Servis tertangguh — {$v->plat}",
            'description' => "Servis berkala dijadualkan {$v->next_service_date->format('d M Y')} tetapi belum dilaksanakan.",
            'detected_data' => ['scheduled' => $v->next_service_date->toDateString(), 'days_overdue' => (int) $v->next_service_date->diffInDays(now())],
        ]);
    }

    private function checkMovementWithoutApproval(): Collection
    {
        $movements = MovementLog::with('vehicle')
            ->where('checkout_time', '>=', now()->subDays(7))
            ->get();

        $results = collect();
        foreach ($movements as $m) {
            $hasApproval = VehicleRequest::where('vehicle_id', $m->vehicle_id)
                ->where('use_date', $m->checkout_time->toDateString())
                ->whereIn('status', ['approved', 'completed'])
                ->exists();

            if (!$hasApproval) {
                $results->push([
                    'vehicle_id' => $m->vehicle_id,
                    'user_id' => $m->driver_user_id,
                    'rule_code' => 'NO_APPROVAL',
                    'severity' => 'info',
                    'title' => "Pergerakan tanpa kelulusan — {$m->vehicle->plat}",
                    'description' => "Kenderaan keluar pada " . $m->checkout_time->format('d M H:i') . " tanpa permohonan diluluskan.",
                    'detected_data' => ['checkout' => $m->checkout_time->toDateTimeString(), 'purpose' => $m->purpose],
                ]);
            }
        }

        return $results;
    }

    private function checkAfterHoursUsage(): Collection
    {
        $movements = MovementLog::with('vehicle')
            ->where('checkout_time', '>=', now()->subDays(30))
            ->get();

        return $movements->filter(function ($m) {
            $hour = (int) $m->checkout_time->format('H');
            return $hour >= 20 || $hour < 6;
        })->map(fn($m) => [
            'vehicle_id' => $m->vehicle_id,
            'user_id' => $m->driver_user_id,
            'rule_code' => 'AFTER_HOURS',
            'severity' => 'warning',
            'title' => "Penggunaan luar waktu — {$m->vehicle->plat}",
            'description' => "Kenderaan digunakan pada " . $m->checkout_time->format('d M Y, H:i') . " (luar waktu pejabat 8pm-6am).",
            'detected_data' => ['checkout' => $m->checkout_time->toDateTimeString()],
        ]);
    }

    private function checkWeekendUsage(): Collection
    {
        $movements = MovementLog::with('vehicle')
            ->where('checkout_time', '>=', now()->subDays(30))
            ->get();

        return $movements->filter(fn($m) => $m->checkout_time->isWeekend())
            ->map(fn($m) => [
                'vehicle_id' => $m->vehicle_id,
                'user_id' => $m->driver_user_id,
                'rule_code' => 'WEEKEND_USE',
                'severity' => 'info',
                'title' => "Penggunaan hujung minggu — {$m->vehicle->plat}",
                'description' => "Kenderaan digunakan pada " . $m->checkout_time->format('l, d M Y') . ".",
                'detected_data' => ['checkout' => $m->checkout_time->toDateTimeString(), 'day' => $m->checkout_time->format('l')],
            ]);
    }

    public static function rules(): array
    {
        return [
            ['code' => 'FUEL_HIGH', 'name' => 'Penggunaan Bahan Api Tinggi', 'description' => 'Kesan apabila L/100km melebihi had', 'severity' => 'warning', 'threshold' => '> 12 L/100km', 'icon' => '⛽'],
            ['code' => 'MILEAGE_JUMP', 'name' => 'Lonjakan Jarak', 'description' => 'Perbezaan odometer besar antara pengisian', 'severity' => 'warning', 'threshold' => '> 500 km/pengisian', 'icon' => '📏'],
            ['code' => 'FREQ_REFUEL', 'name' => 'Pengisian Berulang', 'description' => 'Terlalu banyak pengisian dalam tempoh singkat', 'severity' => 'warning', 'threshold' => '≥ 3x / 7 hari', 'icon' => '🔄'],
            ['code' => 'SERVICE_OVERDUE', 'name' => 'Servis Tertangguh', 'description' => 'Tarikh servis sudah lepas tanpa tindakan', 'severity' => 'warning', 'threshold' => 'Melepasi jadual', 'icon' => '🔧'],
            ['code' => 'NO_APPROVAL', 'name' => 'Pergerakan Tanpa Kelulusan', 'description' => 'Keluar tanpa permohonan diluluskan', 'severity' => 'info', 'threshold' => 'Tiada MOH#', 'icon' => '📋'],
            ['code' => 'AFTER_HOURS', 'name' => 'Penggunaan Luar Waktu', 'description' => 'Kenderaan digunakan selepas jam pejabat', 'severity' => 'warning', 'threshold' => '8pm – 6am', 'icon' => '🌙'],
            ['code' => 'WEEKEND_USE', 'name' => 'Penggunaan Hujung Minggu', 'description' => 'Kenderaan digunakan pada Sabtu/Ahad', 'severity' => 'info', 'threshold' => 'Sabtu / Ahad', 'icon' => '📅'],
        ];
    }
}
