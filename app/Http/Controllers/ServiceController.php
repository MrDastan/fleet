<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $records = ServiceRecord::with('vehicle')->latest('date')->get();
        $vehicles = Vehicle::orderBy('plat')->get();
        return view('services.index', compact('records', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string',
            'date' => 'required|date',
            'workshop' => 'nullable|string',
            'odometer_km' => 'nullable|integer',
            'cost' => 'nullable|numeric',
            'status' => 'required|in:dijadual,dalam_proses,selesai',
            'notes' => 'nullable|string',
        ]);

        ServiceRecord::create($validated);

        return redirect()->route('services.index')->with('success', 'Rekod servis disimpan.');
    }
}
