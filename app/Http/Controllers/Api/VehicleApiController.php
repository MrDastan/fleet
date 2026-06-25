<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('plat', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->orderBy('plat')->get()->map(fn($v) => [
            'id' => $v->id,
            'plat' => $v->plat,
            'model' => $v->model,
            'type' => $v->type,
            'year' => $v->year,
            'department' => $v->department,
            'odometer_km' => $v->odometer_km,
            'status' => $v->status,
            'emoji' => $v->emoji,
            'roadtax_days' => $v->roadtax_days,
            'insurance_days' => $v->insurance_days,
            'roadtax_expiry' => $v->roadtax_expiry?->toDateString(),
            'insurance_expiry' => $v->insurance_expiry?->toDateString(),
        ]));
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json([
            'id' => $vehicle->id,
            'plat' => $vehicle->plat,
            'model' => $vehicle->model,
            'type' => $vehicle->type,
            'year' => $vehicle->year,
            'color' => $vehicle->color,
            'department' => $vehicle->department,
            'odometer_km' => $vehicle->odometer_km,
            'status' => $vehicle->status,
            'emoji' => $vehicle->emoji,
            'roadtax_days' => $vehicle->roadtax_days,
            'insurance_days' => $vehicle->insurance_days,
            'roadtax_expiry' => $vehicle->roadtax_expiry?->toDateString(),
            'insurance_expiry' => $vehicle->insurance_expiry?->toDateString(),
            'qr_token' => $vehicle->qr_code_token,
            'recent_fuel' => $vehicle->fuelRecords()->latest('datetime')->take(5)->get(),
            'recent_services' => $vehicle->serviceRecords()->latest('date')->take(5)->get(),
        ]);
    }
}
