<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('plat', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $vehicles = $query->orderBy('plat')->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plat' => 'required|string|unique:vehicles,plat',
            'model' => 'required|string',
            'type' => 'nullable|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'engine_no' => 'nullable|string',
            'chassis_no' => 'nullable|string',
            'department' => 'nullable|string',
            'odometer_km' => 'nullable|integer',
            'roadtax_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Kenderaan baharu disimpan.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plat' => 'required|string|unique:vehicles,plat,' . $vehicle->id,
            'model' => 'required|string',
            'type' => 'nullable|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string',
            'engine_no' => 'nullable|string',
            'chassis_no' => 'nullable|string',
            'department' => 'nullable|string',
            'odometer_km' => 'nullable|integer',
            'roadtax_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'status' => 'nullable|in:aktif,servis,rosak,tidak_aktif',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Kenderaan dikemaskini.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Kenderaan dipadam.');
    }
}
