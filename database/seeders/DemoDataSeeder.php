<?php

namespace Database\Seeders;

use App\Models\FuelRecord;
use App\Models\MovementLog;
use App\Models\SamanRecord;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all()->keyBy('plat');
        $users = User::all()->keyBy('email');

        $this->seedServices($vehicles);
        $this->seedFuel($vehicles, $users);
        $this->seedMovements($vehicles, $users);
        $this->seedSaman($vehicles, $users);
        $this->seedRequests($vehicles, $users);
    }

    private function seedServices($vehicles): void
    {
        $records = [
            ['plat' => 'VBM 3310', 'service_type' => 'Servis Major (60K km)', 'date' => '2026-06-20', 'workshop' => 'Zufri Auto', 'odometer_km' => 61240, 'cost' => 780, 'status' => 'dalam_proses', 'items' => ['Tukar minyak hitam & penapis', 'Periksa sistem brek', 'Tukar timing belt', 'Periksa sistem penyaman udara']],
            ['plat' => 'WXY 5541', 'service_type' => 'Tukar Tayar', 'date' => '2026-06-15', 'workshop' => 'Kedai Tayar Lim', 'odometer_km' => 88400, 'cost' => 420, 'status' => 'selesai'],
            ['plat' => 'ABC 1234', 'service_type' => 'Servis Minor (5K km)', 'date' => '2026-06-10', 'workshop' => 'Honda Service Centre', 'odometer_km' => 45670, 'cost' => 190, 'status' => 'selesai'],
            ['plat' => 'QRS 9981', 'service_type' => 'Servis Minor', 'date' => '2026-06-25', 'workshop' => 'Perodua SC Cheras', 'odometer_km' => 28100, 'cost' => null, 'status' => 'dijadual'],
            ['plat' => 'KLM 7722', 'service_type' => 'Tukar Minyak Hitam', 'date' => '2026-06-05', 'workshop' => 'Kedai Aman Motor', 'odometer_km' => 33900, 'cost' => 95, 'status' => 'selesai'],
            ['plat' => 'DEF 8811', 'service_type' => 'Servis Minor (5K km)', 'date' => '2026-05-20', 'workshop' => 'Proton Service Centre', 'odometer_km' => 51800, 'cost' => 165, 'status' => 'selesai'],
        ];

        foreach ($records as $r) {
            $v = $vehicles[$r['plat']];
            ServiceRecord::firstOrCreate(
                ['vehicle_id' => $v->id, 'date' => $r['date'], 'service_type' => $r['service_type']],
                [
                    'vehicle_id' => $v->id,
                    'workshop' => $r['workshop'],
                    'odometer_km' => $r['odometer_km'],
                    'cost' => $r['cost'],
                    'status' => $r['status'],
                    'items' => $r['items'] ?? null,
                ]
            );
        }
    }

    private function seedFuel($vehicles, $users): void
    {
        $records = [
            ['plat' => 'WXY 5541', 'email' => 'guard@msd.com.my', 'datetime' => '2026-06-22 08:15', 'station' => 'Petronas Kota Damansara', 'fuel_type' => 'Diesel', 'liters' => 65, 'odometer_km' => 88456, 'consumption' => 11.2],
            ['plat' => 'QRS 9981', 'email' => 'staff@msd.com.my', 'datetime' => '2026-06-21 14:30', 'station' => 'Shell Cheras', 'fuel_type' => 'RON95', 'liters' => 32, 'odometer_km' => 28312, 'consumption' => 7.1],
            ['plat' => 'ABC 1234', 'email' => 'staff@msd.com.my', 'datetime' => '2026-06-20 16:45', 'station' => 'BHPetrol Shah Alam', 'fuel_type' => 'RON95', 'liters' => 40, 'odometer_km' => 45780, 'consumption' => 7.8],
            ['plat' => 'KLM 7722', 'email' => 'guard@msd.com.my', 'datetime' => '2026-06-19 09:00', 'station' => 'Petronas Klang', 'fuel_type' => 'RON95', 'liters' => 38, 'odometer_km' => 34120, 'consumption' => 7.5],
            ['plat' => 'DEF 8811', 'email' => 'fleet@msd.com.my', 'datetime' => '2026-06-18 11:00', 'station' => 'Shell Subang', 'fuel_type' => 'RON95', 'liters' => 35, 'odometer_km' => 52200, 'consumption' => 8.0],
            ['plat' => 'WXY 5541', 'email' => 'guard@msd.com.my', 'datetime' => '2026-06-15 07:30', 'station' => 'Petronas Rawang', 'fuel_type' => 'Diesel', 'liters' => 70, 'odometer_km' => 87850, 'consumption' => 12.5],
            ['plat' => 'ABC 1234', 'email' => 'staff@msd.com.my', 'datetime' => '2026-06-12 09:20', 'station' => 'Petronas Shah Alam', 'fuel_type' => 'RON95', 'liters' => 38, 'odometer_km' => 45400, 'consumption' => 7.6],
            ['plat' => 'QRS 9981', 'email' => 'staff@msd.com.my', 'datetime' => '2026-06-10 16:00', 'station' => 'Shell Ampang', 'fuel_type' => 'RON95', 'liters' => 30, 'odometer_km' => 27950, 'consumption' => 6.9],
        ];

        foreach ($records as $r) {
            $v = $vehicles[$r['plat']];
            $u = $users[$r['email']];
            $ppl = $r['fuel_type'] === 'Diesel' ? 3.35 : ($r['fuel_type'] === 'RON97' ? 3.47 : 2.05);
            FuelRecord::firstOrCreate(
                ['vehicle_id' => $v->id, 'datetime' => $r['datetime']],
                [
                    'user_id' => $u->id,
                    'station' => $r['station'],
                    'fuel_type' => $r['fuel_type'],
                    'liters' => $r['liters'],
                    'price_per_liter' => $ppl,
                    'total_cost' => $r['liters'] * $ppl,
                    'odometer_km' => $r['odometer_km'],
                    'consumption_l100km' => $r['consumption'],
                ]
            );
        }
    }

    private function seedMovements($vehicles, $users): void
    {
        $records = [
            ['plat' => 'WXY 5541', 'email' => 'guard@msd.com.my', 'dept' => 'Operasi', 'purpose' => 'Lawatan Klien — Johor', 'dest' => 'Johor Bahru', 'out' => '2026-06-22 07:30', 'in' => null, 'km_out' => 88391, 'km_in' => null, 'status' => 'di_luar'],
            ['plat' => 'QRS 9981', 'email' => 'staff@msd.com.my', 'dept' => 'Pemasaran', 'purpose' => 'Mesyuarat KL', 'dest' => 'Kuala Lumpur', 'out' => '2026-06-22 08:45', 'in' => null, 'km_out' => 28280, 'km_in' => null, 'status' => 'di_luar'],
            ['plat' => 'ABC 1234', 'email' => 'staff@msd.com.my', 'dept' => 'Kewangan', 'purpose' => 'Urusan Bank', 'dest' => 'Maybank Shah Alam', 'out' => '2026-06-22 10:15', 'in' => '2026-06-22 11:30', 'km_out' => 45760, 'km_in' => 45774, 'status' => 'kembali'],
            ['plat' => 'KLM 7722', 'email' => 'guard@msd.com.my', 'dept' => 'IT', 'purpose' => 'Site Visit — Petaling Jaya', 'dest' => 'Petaling Jaya', 'out' => '2026-06-22 09:00', 'in' => '2026-06-22 13:20', 'km_out' => 34090, 'km_in' => 34128, 'status' => 'kembali'],
            ['plat' => 'DEF 8811', 'email' => 'fleet@msd.com.my', 'dept' => 'HR', 'purpose' => 'Temuduga di cawangan', 'dest' => 'Klang', 'out' => '2026-06-21 08:00', 'in' => '2026-06-21 14:00', 'km_out' => 52100, 'km_in' => 52145, 'status' => 'kembali'],
        ];

        foreach ($records as $r) {
            $v = $vehicles[$r['plat']];
            $u = $users[$r['email']];
            MovementLog::firstOrCreate(
                ['vehicle_id' => $v->id, 'checkout_time' => $r['out']],
                [
                    'driver_user_id' => $u->id,
                    'department' => $r['dept'],
                    'purpose' => $r['purpose'],
                    'destination' => $r['dest'],
                    'checkin_time' => $r['in'],
                    'km_out' => $r['km_out'],
                    'km_in' => $r['km_in'],
                    'status' => $r['status'],
                ]
            );
        }
    }

    private function seedSaman($vehicles, $users): void
    {
        $records = [
            ['plat' => 'WXY 5541', 'email' => 'guard@msd.com.my', 'saman_no' => 'JPJ-2026-008821', 'type' => 'JPJ Trafik', 'offense' => 'Melebihi had laju', 'offense_detail' => '120km/h zon 90km/h', 'date' => '2026-06-10', 'time' => '14:23', 'location' => 'Lebuh Raya PLUS', 'location_detail' => 'KM 254, Johor', 'amount' => 300, 'due_date' => '2026-07-10', 'status' => 'belum_bayar', 'responsibility' => 'Pekerja (ditolak gaji)'],
            ['plat' => 'ABC 1234', 'email' => 'staff@msd.com.my', 'saman_no' => 'DBKL-2026-441102', 'type' => 'Parkir DBKL', 'offense' => 'Parkir tanpa tiket', 'offense_detail' => 'Zon berbayar', 'date' => '2026-06-14', 'time' => '11:00', 'location' => 'Jalan Raja Chulan', 'location_detail' => 'Kuala Lumpur', 'amount' => 100, 'due_date' => '2026-07-14', 'status' => 'belum_bayar', 'responsibility' => 'Pekerja (ditolak gaji)'],
            ['plat' => 'QRS 9981', 'email' => 'staff@msd.com.my', 'saman_no' => 'AES-2026-CR0991', 'type' => 'AES Kamera', 'offense' => 'Langgar lampu merah', 'offense_detail' => 'Kamera AES aktif', 'date' => '2026-06-08', 'time' => '09:15', 'location' => 'Persimpangan Jln Ampang', 'location_detail' => 'Kuala Lumpur', 'amount' => 300, 'due_date' => '2026-07-08', 'status' => 'dalam_rayuan', 'responsibility' => 'Dalam siasatan'],
            ['plat' => 'KLM 7722', 'email' => 'guard@msd.com.my', 'saman_no' => 'MBPJ-2026-009341', 'type' => 'Parkir MBPJ', 'offense' => 'Parkir di tempat larangan', 'offense_detail' => 'Talian kuning berganda', 'date' => '2026-06-16', 'time' => '15:30', 'location' => 'Jalan SS2/24', 'location_detail' => 'Petaling Jaya', 'amount' => 100, 'due_date' => '2026-07-16', 'status' => 'belum_bayar', 'responsibility' => 'Pekerja (ditolak gaji)'],
            ['plat' => 'VBM 3310', 'email' => 'admin@msd.com.my', 'saman_no' => 'JPJ-2026-007712', 'type' => 'JPJ Trafik', 'offense' => 'Tidak pakai tali pinggang', 'offense_detail' => 'Penumpang hadapan', 'date' => '2026-06-03', 'time' => '10:45', 'location' => 'Jalan Kuching', 'location_detail' => 'Kuala Lumpur', 'amount' => 300, 'due_date' => '2026-07-03', 'status' => 'dalam_rayuan', 'responsibility' => 'Dalam siasatan'],
            ['plat' => 'ABC 1234', 'email' => 'staff@msd.com.my', 'saman_no' => 'DBKL-2026-388210', 'type' => 'Parkir DBKL', 'offense' => 'Tempoh tiket tamat', 'offense_detail' => 'Lebih 30 minit', 'date' => '2026-05-28', 'time' => '16:00', 'location' => 'Jalan P. Ramlee', 'location_detail' => 'Kuala Lumpur', 'amount' => 50, 'status' => 'telah_bayar', 'responsibility' => 'Pekerja (ditolak gaji)', 'payment_date' => '2026-06-01'],
            ['plat' => 'WXY 5541', 'email' => 'guard@msd.com.my', 'saman_no' => 'JPJ-2026-005541', 'type' => 'JPJ Trafik', 'offense' => 'Penggunaan telefon', 'offense_detail' => 'Semasa memandu', 'date' => '2026-05-15', 'time' => '08:30', 'location' => 'Lebuhraya Shah Alam', 'location_detail' => 'Selangor', 'amount' => 1000, 'status' => 'telah_bayar', 'responsibility' => 'Pekerja (ditolak gaji)', 'payment_date' => '2026-05-25'],
        ];

        foreach ($records as $r) {
            $v = $vehicles[$r['plat']];
            $u = $users[$r['email']];
            SamanRecord::firstOrCreate(
                ['saman_no' => $r['saman_no']],
                [
                    'vehicle_id' => $v->id,
                    'driver_user_id' => $u->id,
                    'saman_type' => $r['type'],
                    'offense' => $r['offense'],
                    'offense_detail' => $r['offense_detail'],
                    'date' => $r['date'],
                    'time' => $r['time'],
                    'location' => $r['location'],
                    'location_detail' => $r['location_detail'],
                    'amount' => $r['amount'],
                    'due_date' => $r['due_date'] ?? null,
                    'status' => $r['status'],
                    'responsibility' => $r['responsibility'],
                    'payment_date' => $r['payment_date'] ?? null,
                ]
            );
        }
    }

    private function seedRequests($vehicles, $users): void
    {
        $admin = $users['admin@msd.com.my'];
        $fleet = $users['fleet@msd.com.my'];
        $staff = $users['staff@msd.com.my'];
        $guard = $users['guard@msd.com.my'];

        $records = [
            ['requester' => $guard, 'plat' => 'ABC 1234', 'no' => 'MOH-2026-001', 'date' => '2026-06-24', 'start' => '08:00', 'end' => '17:00', 'purpose' => 'Lawatan klien', 'dest' => 'Johor Bahru', 'status' => 'pending_guard', 'stage' => 1],
            ['requester' => $staff, 'plat' => 'KLM 7722', 'no' => 'MOH-2026-002', 'date' => '2026-06-24', 'start' => '09:00', 'end' => '13:00', 'purpose' => 'Mesyuarat rasmi', 'dest' => 'PWTC, KL', 'status' => 'pending_guard', 'stage' => 1],
            ['requester' => $guard, 'plat' => 'QRS 9981', 'no' => 'MOH-2026-003', 'date' => '2026-06-24', 'start' => '14:00', 'end' => '17:30', 'purpose' => 'Site visit', 'dest' => 'Cyberjaya', 'status' => 'pending_fleet', 'stage' => 2, 'guard_note' => 'Kondisi baik, odometer 28,320km'],
            ['requester' => $staff, 'plat' => 'KLM 7722', 'no' => 'MOH-2026-004', 'date' => '2026-06-24', 'start' => '10:00', 'end' => '12:00', 'purpose' => 'Urusan bank', 'dest' => 'Maybank HQ, KL', 'status' => 'pending_fleet', 'stage' => 2, 'guard_note' => 'Kenderaan sedia, penuh minyak'],
            ['requester' => $fleet, 'plat' => 'ABC 1234', 'no' => 'MOH-2026-005', 'date' => '2026-06-23', 'start' => '09:00', 'end' => '11:00', 'purpose' => 'Penghantaran dokumen', 'dest' => 'JTK Selangor', 'status' => 'approved', 'stage' => 3, 'guard_note' => 'OK', 'fleet_note' => 'Perjalanan biasa'],
            ['requester' => $admin, 'plat' => 'VBM 3310', 'no' => 'MOH-2026-006', 'date' => '2026-06-23', 'start' => '08:30', 'end' => '18:00', 'purpose' => 'Lawatan klien', 'dest' => 'Singapore', 'status' => 'approved', 'stage' => 3, 'guard_note' => 'Kondisi sempurna', 'fleet_note' => 'Pulang sebelum 6pm'],
            ['requester' => $fleet, 'plat' => 'WXY 5541', 'no' => 'MOH-2026-007', 'date' => '2026-06-22', 'start' => '08:00', 'end' => '12:00', 'purpose' => 'Mesyuarat rasmi', 'dest' => 'Putrajaya', 'status' => 'rejected', 'stage' => 2, 'guard_note' => 'Kenderaan dalam servis'],
            ['requester' => $guard, 'plat' => 'QRS 9981', 'no' => 'MOH-2026-008', 'date' => '2026-06-22', 'start' => '14:00', 'end' => '18:00', 'purpose' => 'Site visit', 'dest' => 'Port Klang', 'status' => 'completed', 'stage' => 4, 'guard_note' => 'OK, odometer 28,280', 'fleet_note' => 'Selesai'],
        ];

        foreach ($records as $r) {
            $v = $vehicles[$r['plat']];
            VehicleRequest::firstOrCreate(
                ['request_no' => $r['no']],
                [
                    'requester_user_id' => $r['requester']->id,
                    'vehicle_id' => $v->id,
                    'use_date' => $r['date'],
                    'time_start' => $r['start'],
                    'time_end' => $r['end'],
                    'purpose' => $r['purpose'],
                    'destination' => $r['dest'],
                    'status' => $r['status'],
                    'stage' => $r['stage'],
                    'guard_note' => $r['guard_note'] ?? null,
                    'fleet_note' => $r['fleet_note'] ?? null,
                ]
            );
        }
    }
}
