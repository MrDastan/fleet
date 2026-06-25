<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ServiceRecord;
use App\Models\FuelRecord;
use App\Models\SamanRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();

        $fuelByMonth = FuelRecord::selectRaw("DATE_FORMAT(datetime, '%Y-%m') as month, SUM(total_cost) as cost, SUM(liters) as liters")
            ->whereYear('datetime', now()->year)
            ->groupBy('month')->orderBy('month')->get();

        $serviceByMonth = ServiceRecord::selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(cost) as cost")
            ->whereYear('date', now()->year)
            ->groupBy('month')->orderBy('month')->get();

        $samanByMonth = SamanRecord::selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as amount, COUNT(*) as cnt")
            ->whereYear('date', now()->year)
            ->groupBy('month')->orderBy('month')->get();

        $totalFuel = FuelRecord::whereYear('datetime', now()->year)->sum('total_cost');
        $totalService = ServiceRecord::whereYear('date', now()->year)->sum('cost');
        $totalSaman = SamanRecord::whereYear('date', now()->year)->sum('amount');

        return view('reports.index', compact('vehicles', 'fuelByMonth', 'serviceByMonth', 'samanByMonth', 'totalFuel', 'totalService', 'totalSaman'));
    }

    public function monthly()
    {
        $vehicles = Vehicle::all();
        $month = now()->format('M Y');

        $services = ServiceRecord::with('vehicle')
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)->get();
        $fuel = FuelRecord::with('vehicle')
            ->whereMonth('datetime', now()->month)->whereYear('datetime', now()->year)->get();
        $saman = SamanRecord::with('vehicle')
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)->get();

        $pdf = Pdf::loadView('reports.pdf.monthly', compact('vehicles', 'month', 'services', 'fuel', 'saman'));
        return $pdf->download("laporan-bulanan-{$month}.pdf");
    }

    public function cost()
    {
        $vehicles = Vehicle::all();
        $year = now()->year;

        $data = $vehicles->map(function ($v) use ($year) {
            return [
                'vehicle' => $v,
                'fuel' => FuelRecord::where('vehicle_id', $v->id)->whereYear('datetime', $year)->sum('total_cost'),
                'service' => ServiceRecord::where('vehicle_id', $v->id)->whereYear('date', $year)->sum('cost'),
                'saman' => SamanRecord::where('vehicle_id', $v->id)->whereYear('date', $year)->sum('amount'),
            ];
        });

        $pdf = Pdf::loadView('reports.pdf.cost', compact('data', 'year'));
        return $pdf->download("laporan-kos-{$year}.pdf");
    }

    public function compliance()
    {
        $vehicles = Vehicle::orderBy('roadtax_expiry')->get();

        $pdf = Pdf::loadView('reports.pdf.compliance', compact('vehicles'));
        return $pdf->download('laporan-compliance.pdf');
    }
}
