<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ApprovalApiController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleRequest::with(['requester:id,name,department', 'vehicle:id,plat,model']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'use_date' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'purpose' => 'required|string',
            'destination' => 'required|string',
        ]);

        $count = VehicleRequest::whereYear('created_at', now()->year)->count() + 1;
        $validated['request_no'] = 'MOH-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['requester_user_id'] = $request->user()->id;
        $validated['status'] = 'pending_guard';
        $validated['stage'] = 1;

        $req = VehicleRequest::create($validated);

        return response()->json($req->load(['requester', 'vehicle']), 201);
    }

    public function show(VehicleRequest $approval)
    {
        return response()->json($approval->load(['requester', 'vehicle']));
    }
}
