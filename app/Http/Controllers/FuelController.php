<?php

namespace App\Http\Controllers;

use App\Models\FuelRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    public function index(Request $request)
    {
        $query = FuelRecord::with(['vehicle', 'driver', 'files']);

        if ($vehicleId = $request->input('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $records = $query->latest('datetime')->get();
        $vehicles = Vehicle::orderBy('plat')->get();

        $thisMonth = FuelRecord::whereMonth('datetime', now()->month)->whereYear('datetime', now()->year);
        $totalLiters = (clone $thisMonth)->sum('liters');
        $totalCost = (clone $thisMonth)->sum('total_cost');
        $avgConsumption = $thisMonth->whereNotNull('consumption_l100km')->avg('consumption_l100km');
        $highUsage = FuelRecord::whereMonth('datetime', now()->month)
            ->where('consumption_l100km', '>', 10)->count();

        return view('fuel.index', compact('records', 'vehicles', 'totalLiters', 'totalCost', 'avgConsumption', 'highUsage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'datetime' => 'required|date',
            'station' => 'nullable|string',
            'fuel_type' => 'required|in:RON95,RON97,Diesel',
            'liters' => 'required|numeric|min:0.01',
            'odometer_km' => 'required|integer|min:0',
            'driver_name' => 'nullable|string',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        $priceMap = ['RON95' => 2.05, 'RON97' => 3.47, 'Diesel' => 3.35];
        $ppl = $priceMap[$validated['fuel_type']];

        $prevRecord = FuelRecord::where('vehicle_id', $validated['vehicle_id'])
            ->where('odometer_km', '<', $validated['odometer_km'])
            ->orderByDesc('odometer_km')
            ->first();

        $consumption = null;
        if ($prevRecord) {
            $kmDiff = $validated['odometer_km'] - $prevRecord->odometer_km;
            if ($kmDiff > 0) {
                $consumption = round(($validated['liters'] / $kmDiff) * 100, 1);
            }
        }

        FuelRecord::create([
            'vehicle_id' => $validated['vehicle_id'],
            'user_id' => auth()->id(),
            'datetime' => $validated['datetime'],
            'station' => $validated['station'],
            'fuel_type' => $validated['fuel_type'],
            'liters' => $validated['liters'],
            'price_per_liter' => $ppl,
            'total_cost' => $validated['liters'] * $ppl,
            'odometer_km' => $validated['odometer_km'],
            'consumption_l100km' => $consumption,
        ]);

        $vehicle->update(['odometer_km' => max($vehicle->odometer_km, $validated['odometer_km'])]);

        return redirect()->route('fuel.index')->with('success', 'Log bahan api disimpan.' . ($consumption ? " Penggunaan: {$consumption} L/100km" : ''));
    }
}
