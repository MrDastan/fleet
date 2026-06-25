<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\SamanRecord;
use App\Models\FuelRecord;
use App\Models\MovementLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::all();
        $totalVehicles = $vehicles->count();
        $activeVehicles = $vehicles->where('status', 'aktif')->count();
        $inService = $vehicles->where('status', 'servis')->count();

        $needsAttention = $vehicles->filter(function ($v) {
            return $v->roadtax_days <= 30 || $v->insurance_days <= 30;
        })->count();

        $urgentCount = $vehicles->filter(function ($v) {
            return $v->roadtax_days <= 7 || $v->insurance_days <= 7;
        })->count();

        $unpaidSaman = SamanRecord::where('status', 'belum_bayar')->count();
        $unpaidSamanTotal = SamanRecord::where('status', 'belum_bayar')->sum('amount');

        $urgentReminders = $vehicles->filter(function ($v) {
            return $v->roadtax_days <= 7 || $v->insurance_days <= 7;
        });

        $recentMovements = MovementLog::with(['vehicle', 'driver'])
            ->latest('checkout_time')
            ->take(4)
            ->get();

        $fuelThisMonth = FuelRecord::whereMonth('datetime', now()->month)
            ->whereYear('datetime', now()->year);

        $fuelTotal = $fuelThisMonth->sum('total_cost');
        $fuelLiters = $fuelThisMonth->sum('liters');

        return view('dashboard.index', compact(
            'vehicles', 'totalVehicles', 'activeVehicles', 'inService',
            'needsAttention', 'urgentCount', 'unpaidSaman', 'unpaidSamanTotal',
            'urgentReminders', 'recentMovements', 'fuelTotal', 'fuelLiters'
        ));
    }
}
