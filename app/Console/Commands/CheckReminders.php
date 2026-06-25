<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckReminders extends Command
{
    protected $signature = 'fleet:check-reminders';
    protected $description = 'Semak dan hantar peringatan untuk road tax, insuran, dan servis yang hampir luput';

    public function handle(): int
    {
        $vehicles = Vehicle::all();
        $sent = 0;

        foreach ($vehicles as $v) {
            if ($v->roadtax_expiry && $v->roadtax_days <= 30 && in_array($v->roadtax_days, [30, 14, 7, 3, 1])) {
                $this->sendReminder(
                    "Road Tax {$v->plat} — {$v->roadtax_days} hari",
                    "Road tax kenderaan {$v->plat} ({$v->model}) akan luput pada {$v->roadtax_expiry->format('d M Y')}. Baki {$v->roadtax_days} hari.",
                    'roadtax', $v->id,
                    ['admin', 'fleet', 'staff']
                );
                $sent++;
            }

            if ($v->insurance_expiry && $v->insurance_days <= 30 && in_array($v->insurance_days, [60, 30, 14, 7, 3, 1])) {
                $this->sendReminder(
                    "Insuran {$v->plat} — {$v->insurance_days} hari",
                    "Insuran kenderaan {$v->plat} ({$v->model}) akan luput pada {$v->insurance_expiry->format('d M Y')}. Baki {$v->insurance_days} hari.",
                    'insuran', $v->id,
                    ['admin', 'fleet']
                );
                $sent++;
            }

            if ($v->next_service_date) {
                $srvDays = (int) now()->diffInDays($v->next_service_date, false);
                if ($srvDays <= 14 && in_array($srvDays, [14, 7, 3, 1])) {
                    $this->sendReminder(
                        "Servis {$v->plat} — {$srvDays} hari",
                        "Servis berkala kenderaan {$v->plat} ({$v->model}) dijadualkan pada {$v->next_service_date->format('d M Y')}.",
                        'servis', $v->id,
                        ['fleet', 'guard']
                    );
                    $sent++;
                }
            }
        }

        $this->info("Selesai. {$sent} peringatan dihantar.");
        return 0;
    }

    private function sendReminder(string $title, string $message, string $type, int $vehicleId, array $roles): void
    {
        $users = User::role($roles)->get();
        Notification::send($users, new ReminderNotification($title, $message, $type, $vehicleId));
    }
}
