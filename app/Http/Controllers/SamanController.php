<?php

namespace App\Http\Controllers;

use App\Models\SamanRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class SamanController extends Controller
{
    public function index(Request $request)
    {
        $query = SamanRecord::with(['vehicle', 'driver']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($vehicleId = $request->input('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('saman_no', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', fn($q2) => $q2->where('plat', 'like', "%{$search}%"));
            });
        }

        $records = $query->latest('date')->get();
        $vehicles = Vehicle::orderBy('plat')->get();

        $unpaidCount = SamanRecord::where('status', 'belum_bayar')->count();
        $unpaidTotal = SamanRecord::where('status', 'belum_bayar')->sum('amount');
        $appealCount = SamanRecord::where('status', 'dalam_rayuan')->count();
        $appealTotal = SamanRecord::where('status', 'dalam_rayuan')->sum('amount');
        $paidCount = SamanRecord::where('status', 'telah_bayar')->count();
        $paidTotal = SamanRecord::where('status', 'telah_bayar')->sum('amount');
        $totalAll = SamanRecord::count();
        $totalAmount = SamanRecord::sum('amount');

        $byVehicle = SamanRecord::selectRaw('vehicle_id, count(*) as cnt, sum(amount) as total')
            ->groupBy('vehicle_id')->with('vehicle')->orderByDesc('total')->get();

        return view('saman.index', compact(
            'records', 'vehicles',
            'unpaidCount', 'unpaidTotal', 'appealCount', 'appealTotal',
            'paidCount', 'paidTotal', 'totalAll', 'totalAmount', 'byVehicle'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'saman_no' => 'required|string|unique:saman_records,saman_no',
            'saman_type' => 'required|string',
            'offense' => 'required|string',
            'offense_detail' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable',
            'location' => 'required|string',
            'location_detail' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:belum_bayar,dalam_rayuan,telah_bayar',
            'responsibility' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['driver_user_id'] = auth()->id();
        SamanRecord::create($validated);

        return redirect()->route('saman.index')->with('success', 'Rekod saman disimpan.');
    }

    public function update(Request $request, SamanRecord $saman)
    {
        $validated = $request->validate([
            'status' => 'required|in:belum_bayar,dalam_rayuan,telah_bayar',
            'payment_date' => 'nullable|date',
            'receipt_no' => 'nullable|string',
        ]);

        $saman->update($validated);

        return redirect()->route('saman.index')->with('success', 'Status saman dikemaskini.');
    }
}
