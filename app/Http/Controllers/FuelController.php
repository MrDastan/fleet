<?php

namespace App\Http\Controllers;

use App\Models\FuelRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    public function index()
    {
        $records = FuelRecord::with(['vehicle', 'driver'])->latest('datetime')->get();
        $vehicles = Vehicle::orderBy('plat')->get();
        return view('fuel.index', compact('records', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'datetime' => 'required|date',
            'station' => 'nullable|string',
            'fuel_type' => 'required|in:RON95,RON97,Diesel',
            'liters' => 'required|numeric',
            'odometer_km' => 'required|integer',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['price_per_liter'] = 3.35;
        $validated['total_cost'] = $validated['liters'] * $validated['price_per_liter'];

        FuelRecord::create($validated);

        return redirect()->route('fuel.index')->with('success', 'Log bahan api disimpan.');
    }
}
