<?php

namespace App\Http\Controllers;

use App\Models\SamanRecord;
use App\Models\Vehicle;

class SamanController extends Controller
{
    public function index()
    {
        $records = SamanRecord::with(['vehicle', 'driver'])->latest('date')->get();
        $vehicles = Vehicle::orderBy('plat')->get();

        $unpaid = SamanRecord::where('status', 'belum_bayar');
        $unpaidCount = $unpaid->count();
        $unpaidTotal = $unpaid->sum('amount');

        $appealCount = SamanRecord::where('status', 'dalam_rayuan')->count();
        $paidCount = SamanRecord::where('status', 'telah_bayar')->count();

        return view('saman.index', compact('records', 'vehicles', 'unpaidCount', 'unpaidTotal', 'appealCount', 'paidCount'));
    }
}
