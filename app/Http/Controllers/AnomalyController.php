<?php

namespace App\Http\Controllers;

use App\Models\AnomalyRecord;
use App\Services\AnomalyEngine;
use Illuminate\Http\Request;

class AnomalyController extends Controller
{
    public function index(Request $request)
    {
        $query = AnomalyRecord::with(['vehicle', 'user']);

        if ($severity = $request->input('severity')) {
            if ($severity === 'resolved') {
                $query->where('status', 'resolved');
            } else {
                $query->where('severity', $severity)->where('status', '!=', 'resolved');
            }
        } else {
            // default: show open ones first
        }

        $records = $query->latest()->get();

        $counts = [
            'critical' => AnomalyRecord::where('severity', 'critical')->where('status', '!=', 'resolved')->count(),
            'warning' => AnomalyRecord::where('severity', 'warning')->where('status', '!=', 'resolved')->count(),
            'info' => AnomalyRecord::where('severity', 'info')->where('status', '!=', 'resolved')->count(),
            'resolved' => AnomalyRecord::where('status', 'resolved')->count(),
        ];

        $rules = AnomalyEngine::rules();

        return view('anomalies.index', compact('records', 'counts', 'rules'));
    }

    public function scan()
    {
        $engine = new AnomalyEngine();
        $detected = $engine->scan();

        return redirect()->route('anomalies.index')->with('success', $detected->count() . ' anomali dikesan.');
    }

    public function resolve(Request $request, AnomalyRecord $anomaly)
    {
        $anomaly->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_notes' => $request->input('resolution_notes', 'Diselesaikan'),
        ]);

        return redirect()->route('anomalies.index')->with('success', 'Anomali ditandakan selesai.');
    }

    public function investigate(AnomalyRecord $anomaly)
    {
        $anomaly->update(['status' => 'investigating']);
        return redirect()->route('anomalies.index')->with('success', 'Status dikemaskini ke "Dalam Siasatan".');
    }
}
