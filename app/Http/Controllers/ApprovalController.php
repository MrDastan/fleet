<?php

namespace App\Http\Controllers;

use App\Models\VehicleRequest;
use App\Models\Vehicle;

class ApprovalController extends Controller
{
    public function index()
    {
        $requests = VehicleRequest::with(['requester', 'vehicle'])->latest()->get();
        $vehicles = Vehicle::where('status', 'aktif')->orderBy('plat')->get();
        return view('approvals.index', compact('requests', 'vehicles'));
    }
}
