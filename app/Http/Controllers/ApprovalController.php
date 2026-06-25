<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VehicleRequest;
use App\Models\Vehicle;
use App\Notifications\ApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleRequest::with(['requester', 'vehicle']);
        $role = auth()->user()->roles->first()?->name ?? 'staff';

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('requester', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('vehicle', fn($q2) => $q2->where('plat', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->get();
        $vehicles = Vehicle::where('status', 'aktif')->orderBy('plat')->get();

        $counts = [
            'all' => VehicleRequest::count(),
            'pending_guard' => VehicleRequest::where('status', 'pending_guard')->count(),
            'pending_fleet' => VehicleRequest::where('status', 'pending_fleet')->count(),
            'approved' => VehicleRequest::where('status', 'approved')->count(),
            'rejected' => VehicleRequest::where('status', 'rejected')->count(),
            'completed' => VehicleRequest::where('status', 'completed')->count(),
        ];

        return view('approvals.index', compact('requests', 'vehicles', 'counts', 'role'));
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
            'passengers' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $count = VehicleRequest::whereYear('created_at', now()->year)->count() + 1;
        $validated['request_no'] = 'MOH-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['requester_user_id'] = auth()->id();
        $validated['status'] = 'pending_guard';
        $validated['stage'] = 1;

        $vr = VehicleRequest::create($validated);

        $guards = User::role('guard')->get();
        Notification::send($guards, new ApprovalNotification($vr->load(['requester', 'vehicle']), 'submitted'));

        return redirect()->route('approvals.index')->with('success', 'Permohonan dihantar. Menunggu pengesahan penjaga.');
    }

    public function guardAction(Request $request, VehicleRequest $approval)
    {
        $action = $request->input('action');

        if ($action === 'approve') {
            $approval->update([
                'status' => 'pending_fleet',
                'stage' => 2,
                'guard_user_id' => auth()->id(),
                'guard_note' => $request->input('guard_note'),
                'guard_checklist' => $request->input('guard_checklist', []),
                'guard_odometer' => $request->input('guard_odometer'),
                'guard_action_at' => now(),
            ]);
            $fleetUsers = User::role('fleet')->get();
            Notification::send($fleetUsers, new ApprovalNotification($approval->load(['requester', 'vehicle']), 'guard_approved'));

            return redirect()->route('approvals.index')->with('success', 'Disahkan oleh penjaga. Dihantar ke Fleet untuk kelulusan.');
        }

        $approval->update([
            'status' => 'rejected',
            'stage' => 2,
            'guard_user_id' => auth()->id(),
            'guard_note' => $request->input('guard_note', 'Ditolak oleh penjaga'),
            'guard_action_at' => now(),
        ]);
        return redirect()->route('approvals.index')->with('success', 'Permohonan ditolak oleh penjaga.');
    }

    public function fleetAction(Request $request, VehicleRequest $approval)
    {
        $action = $request->input('action');

        if ($action === 'approve') {
            $approval->update([
                'status' => 'approved',
                'stage' => 3,
                'fleet_user_id' => auth()->id(),
                'fleet_note' => $request->input('fleet_note'),
                'fleet_priority' => $request->input('fleet_priority'),
                'fleet_action_at' => now(),
            ]);
            $approval->requester->notify(new ApprovalNotification($approval->load(['requester', 'vehicle']), 'fleet_approved'));

            return redirect()->route('approvals.index')->with('success', 'Permohonan diluluskan oleh Fleet.');
        }

        $approval->update([
            'status' => 'rejected',
            'stage' => 3,
            'fleet_user_id' => auth()->id(),
            'fleet_note' => $request->input('fleet_note', 'Ditolak oleh Fleet'),
            'fleet_action_at' => now(),
        ]);
        return redirect()->route('approvals.index')->with('success', 'Permohonan ditolak oleh Fleet.');
    }

    public function adminOverride(Request $request, VehicleRequest $approval)
    {
        $action = $request->input('override_action', 'approve');

        $approval->update([
            'status' => $action === 'approve' ? 'approved' : 'rejected',
            'stage' => 3,
            'admin_override_by' => auth()->id(),
            'admin_override_reason' => $request->input('override_reason', 'Admin override'),
            'admin_override_at' => now(),
        ]);

        $label = $action === 'approve' ? 'diluluskan' : 'ditolak';
        return redirect()->route('approvals.index')->with('success', "Permohonan {$label} (Admin Override).");
    }

    public function complete(VehicleRequest $approval)
    {
        $approval->update(['status' => 'completed', 'stage' => 4]);
        return redirect()->route('approvals.index')->with('success', 'Permohonan ditandakan selesai.');
    }
}
