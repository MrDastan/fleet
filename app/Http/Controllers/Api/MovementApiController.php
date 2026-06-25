<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovementLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MovementApiController extends Controller
{
    public function index(Request $request)
    {
        $query = MovementLog::with(['vehicle:id,plat,model', 'driver:id,name']);

        if ($date = $request->input('date')) {
            $query->whereDate('checkout_time', $date);
        }

        return response()->json($query->latest('checkout_time')->paginate(20));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'purpose' => 'required|string',
            'destination' => 'nullable|string',
            'km_out' => 'nullable|integer',
        ]);

        $log = MovementLog::create([
            'vehicle_id' => $validated['vehicle_id'],
            'driver_user_id' => $request->user()->id,
            'department' => $request->user()->department,
            'purpose' => $validated['purpose'],
            'destination' => $validated['destination'],
            'checkout_time' => now(),
            'km_out' => $validated['km_out'],
            'status' => 'di_luar',
        ]);

        return response()->json($log, 201);
    }

    public function checkin(Request $request, MovementLog $movement)
    {
        $movement->update([
            'checkin_time' => now(),
            'km_in' => $request->input('km_in'),
            'status' => 'kembali',
        ]);

        if ($request->input('km_in')) {
            $movement->vehicle->update(['odometer_km' => max($movement->vehicle->odometer_km, $request->input('km_in'))]);
        }

        return response()->json($movement->fresh());
    }
}
