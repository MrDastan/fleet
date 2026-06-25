<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRecord::with('vehicle');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('vehicle', fn($q) => $q->where('plat', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"));
        }

        $records = $query->latest('date')->get();
        $vehicles = Vehicle::orderBy('plat')->get();

        $counts = [
            'all' => ServiceRecord::count(),
            'dalam_proses' => ServiceRecord::where('status', 'dalam_proses')->count(),
            'selesai' => ServiceRecord::where('status', 'selesai')->count(),
            'dijadual' => ServiceRecord::where('status', 'dijadual')->count(),
        ];

        return view('services.index', compact('records', 'vehicles', 'counts'));
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

    public function update(Request $request, ServiceRecord $service)
    {
        $validated = $request->validate([
            'service_type' => 'required|string',
            'date' => 'required|date',
            'workshop' => 'nullable|string',
            'odometer_km' => 'nullable|integer',
            'cost' => 'nullable|numeric',
            'status' => 'required|in:dijadual,dalam_proses,selesai',
            'notes' => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Rekod servis dikemaskini.');
    }
}
