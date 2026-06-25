<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ServiceRecord;

class ReminderController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();

        $reminders = collect();

        foreach ($vehicles as $v) {
            if ($v->roadtax_expiry) {
                $days = $v->roadtax_days;
                if ($days <= 90) {
                    $reminders->push([
                        'type' => 'roadtax', 'icon' => '📄', 'label' => 'Road Tax',
                        'vehicle' => $v, 'days' => $days,
                        'date' => $v->roadtax_expiry,
                        'severity' => $days <= 7 ? 'critical' : ($days <= 30 ? 'warning' : 'info'),
                    ]);
                }
            }
            if ($v->insurance_expiry) {
                $days = $v->insurance_days;
                if ($days <= 90) {
                    $reminders->push([
                        'type' => 'insuran', 'icon' => '🛡️', 'label' => 'Insuran',
                        'vehicle' => $v, 'days' => $days,
                        'date' => $v->insurance_expiry,
                        'severity' => $days <= 7 ? 'critical' : ($days <= 30 ? 'warning' : 'info'),
                    ]);
                }
            }
            if ($v->puspakom_expiry) {
                $days = (int) now()->diffInDays($v->puspakom_expiry, false);
                if ($days <= 60) {
                    $reminders->push([
                        'type' => 'puspakom', 'icon' => '📋', 'label' => 'Puspakom',
                        'vehicle' => $v, 'days' => $days,
                        'date' => $v->puspakom_expiry,
                        'severity' => $days <= 7 ? 'critical' : ($days <= 30 ? 'warning' : 'info'),
                    ]);
                }
            }
            if ($v->next_service_date) {
                $days = (int) now()->diffInDays($v->next_service_date, false);
                if ($days <= 30) {
                    $reminders->push([
                        'type' => 'servis', 'icon' => '🔧', 'label' => 'Servis Berkala',
                        'vehicle' => $v, 'days' => $days,
                        'date' => $v->next_service_date,
                        'severity' => $days <= 7 ? 'critical' : ($days <= 14 ? 'warning' : 'info'),
                    ]);
                }
            }
        }

        $reminders = $reminders->sortBy('days');

        $critical = $reminders->where('severity', 'critical');
        $attention = $reminders->where('severity', 'warning');
        $upcoming = $reminders->where('severity', 'info');

        $overdueServices = ServiceRecord::where('status', 'dijadual')
            ->where('date', '<', now())->with('vehicle')->get();

        return view('reminders.index', compact('critical', 'attention', 'upcoming', 'overdueServices'));
    }
}
