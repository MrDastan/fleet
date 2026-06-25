<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelApiController extends Controller
{
    public function index(Request $request)
    {
        $query = FuelRecord::with(['vehicle:id,plat,model', 'driver:id,name']);

        if ($vehicleId = $request->input('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        return response()->json($query->latest('datetime')->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_type' => 'required|in:RON95,RON97,Diesel',
            'liters' => 'required|numeric|min:0.01',
            'odometer_km' => 'required|integer|min:0',
            'station' => 'nullable|string',
        ]);

        $priceMap = ['RON95' => 2.05, 'RON97' => 3.47, 'Diesel' => 3.35];
        $ppl = $priceMap[$validated['fuel_type']];

        $prev = FuelRecord::where('vehicle_id', $validated['vehicle_id'])
            ->where('odometer_km', '<', $validated['odometer_km'])
            ->orderByDesc('odometer_km')->first();

        $consumption = null;
        if ($prev) {
            $diff = $validated['odometer_km'] - $prev->odometer_km;
            if ($diff > 0) $consumption = round(($validated['liters'] / $diff) * 100, 1);
        }

        $record = FuelRecord::create([
            'vehicle_id' => $validated['vehicle_id'],
            'user_id' => $request->user()->id,
            'datetime' => now(),
            'station' => $validated['station'],
            'fuel_type' => $validated['fuel_type'],
            'liters' => $validated['liters'],
            'price_per_liter' => $ppl,
            'total_cost' => $validated['liters'] * $ppl,
            'odometer_km' => $validated['odometer_km'],
            'consumption_l100km' => $consumption,
        ]);

        Vehicle::find($validated['vehicle_id'])->update([
            'odometer_km' => max(Vehicle::find($validated['vehicle_id'])->odometer_km, $validated['odometer_km']),
        ]);

        return response()->json($record, 201);
    }
}
