<?php

namespace App\Http\Controllers;

use App\Models\MovementLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $query = MovementLog::with(['vehicle', 'driver']);

        if ($date = $request->input('date')) {
            $query->whereDate('checkout_time', $date);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', fn($q2) => $q2->where('plat', 'like', "%{$search}%"))
                  ->orWhereHas('driver', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $logs = $query->latest('checkout_time')->get();
        $vehicles = Vehicle::where('status', 'aktif')->orderBy('plat')->get();
        $outCount = MovementLog::where('status', 'di_luar')->count();

        return view('movements.index', compact('logs', 'vehicles', 'outCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'purpose' => 'required|string',
            'destination' => 'nullable|string',
            'department' => 'nullable|string',
            'km_out' => 'nullable|integer',
            'checkout_time' => 'required|date',
            'guard_notes' => 'nullable|string',
        ]);

        $validated['driver_user_id'] = auth()->id();
        $validated['status'] = 'di_luar';

        MovementLog::create($validated);

        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($validated['km_out'] && $validated['km_out'] > $vehicle->odometer_km) {
            $vehicle->update(['odometer_km' => $validated['km_out']]);
        }

        return redirect()->route('movements.index')->with('success', 'Log keluar dicatat.');
    }

    public function checkin(Request $request, MovementLog $movement)
    {
        $validated = $request->validate([
            'km_in' => 'nullable|integer',
            'checkin_time' => 'required|date',
            'guard_notes' => 'nullable|string',
        ]);

        $movement->update([
            'checkin_time' => $validated['checkin_time'],
            'km_in' => $validated['km_in'],
            'guard_notes' => $validated['guard_notes'] ?? $movement->guard_notes,
            'status' => 'kembali',
        ]);

        if ($validated['km_in']) {
            $movement->vehicle->update(['odometer_km' => max($movement->vehicle->odometer_km, $validated['km_in'])]);
        }

        return redirect()->route('movements.index')->with('success', 'Log masuk dicatat.');
    }
}
