<?php

namespace App\Http\Controllers;

use App\Models\MovementLog;
use App\Models\Vehicle;

class MovementController extends Controller
{
    public function index()
    {
        $logs = MovementLog::with(['vehicle', 'driver'])->latest('checkout_time')->get();
        $vehicles = Vehicle::where('status', 'aktif')->orderBy('plat')->get();
        return view('movements.index', compact('logs', 'vehicles'));
    }
}
