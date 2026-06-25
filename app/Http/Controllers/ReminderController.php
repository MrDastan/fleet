<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class ReminderController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();

        $critical = $vehicles->filter(fn($v) => $v->roadtax_days <= 7 || $v->insurance_days <= 7);
        $attention = $vehicles->filter(fn($v) =>
            ($v->roadtax_days > 7 && $v->roadtax_days <= 30) ||
            ($v->insurance_days > 7 && $v->insurance_days <= 30)
        );

        return view('reminders.index', compact('critical', 'attention'));
    }
}
