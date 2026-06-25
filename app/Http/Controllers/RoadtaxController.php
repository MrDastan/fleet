<?php

namespace App\Http\Controllers;

use App\Models\RoadtaxRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RoadtaxController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::orderBy('roadtax_expiry')->get();
        $tab = $request->input('tab', 'roadtax');

        $rtExpiring = $vehicles->filter(fn($v) => $v->roadtax_days <= 30)->count();
        $rtOk = $vehicles->filter(fn($v) => $v->roadtax_days > 30)->count();
        $insExpiring = $vehicles->filter(fn($v) => $v->insurance_days <= 30)->count();
        $insOk = $vehicles->filter(fn($v) => $v->insurance_days > 30)->count();

        $records = RoadtaxRecord::with('vehicle')->latest('expiry_date')->get();

        return view('roadtax.index', compact('vehicles', 'tab', 'rtExpiring', 'rtOk', 'insExpiring', 'insOk', 'records'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'doc_type' => 'required|in:roadtax,insuran,puspakom',
            'start_date' => 'nullable|date',
            'expiry_date' => 'required|date',
            'amount' => 'nullable|numeric',
            'policy_no' => 'nullable|string',
        ]);

        RoadtaxRecord::create($validated);

        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($validated['doc_type'] === 'roadtax') {
            $vehicle->update(['roadtax_expiry' => $validated['expiry_date']]);
        } elseif ($validated['doc_type'] === 'insuran') {
            $vehicle->update(['insurance_expiry' => $validated['expiry_date']]);
        } elseif ($validated['doc_type'] === 'puspakom') {
            $vehicle->update(['puspakom_expiry' => $validated['expiry_date']]);
        }

        return redirect()->route('roadtax.index')->with('success', 'Rekod dikemaskini.');
    }
}
