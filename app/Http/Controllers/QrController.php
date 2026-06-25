<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\FuelRecord;
use App\Models\MovementLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('plat')->get();
        return view('qr.index', compact('vehicles'));
    }

    public function generate(Vehicle $vehicle)
    {
        $url = route('qr.scan', $vehicle->qr_code_token);
        $qr = QrCode::size(250)->margin(1)->generate($url);
        return response()->json(['qr' => $qr->toHtml(), 'url' => $url, 'plat' => $vehicle->plat, 'model' => $vehicle->model]);
    }

    public function scan(string $token)
    {
        $vehicle = Vehicle::where('qr_code_token', $token)->firstOrFail();

        $latestFuel = FuelRecord::where('vehicle_id', $vehicle->id)->latest('datetime')->first();
        $latestMove = MovementLog::where('vehicle_id', $vehicle->id)->latest('checkout_time')->first();

        return view('qr.scan', compact('vehicle', 'latestFuel', 'latestMove'));
    }

    public function scanAction(Request $request, string $token)
    {
        $vehicle = Vehicle::where('qr_code_token', $token)->firstOrFail();
        $action = $request->input('action');

        if ($action === 'checkout') {
            $validated = $request->validate([
                'purpose' => 'required|string',
                'destination' => 'nullable|string',
                'km_out' => 'nullable|integer',
            ]);

            MovementLog::create([
                'vehicle_id' => $vehicle->id,
                'driver_user_id' => auth()->id(),
                'department' => auth()->user()->department,
                'purpose' => $validated['purpose'],
                'destination' => $validated['destination'],
                'checkout_time' => now(),
                'km_out' => $validated['km_out'],
                'status' => 'di_luar',
            ]);

            return redirect()->route('qr.scan', $token)->with('success', 'Log keluar dicatat.');
        }

        if ($action === 'fuel') {
            $validated = $request->validate([
                'fuel_type' => 'required|in:RON95,RON97,Diesel',
                'liters' => 'required|numeric|min:0.01',
                'odometer_km' => 'required|integer',
                'station' => 'nullable|string',
            ]);

            $priceMap = ['RON95' => 2.05, 'RON97' => 3.47, 'Diesel' => 3.35];
            $ppl = $priceMap[$validated['fuel_type']];

            FuelRecord::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => auth()->id(),
                'datetime' => now(),
                'station' => $validated['station'],
                'fuel_type' => $validated['fuel_type'],
                'liters' => $validated['liters'],
                'price_per_liter' => $ppl,
                'total_cost' => $validated['liters'] * $ppl,
                'odometer_km' => $validated['odometer_km'],
            ]);

            $vehicle->update(['odometer_km' => max($vehicle->odometer_km, $validated['odometer_km'])]);

            return redirect()->route('qr.scan', $token)->with('success', 'Log bahan api disimpan.');
        }

        if ($action === 'checkin') {
            $move = MovementLog::where('vehicle_id', $vehicle->id)
                ->where('status', 'di_luar')->latest()->first();

            if ($move) {
                $move->update([
                    'checkin_time' => now(),
                    'km_in' => $request->input('km_in'),
                    'status' => 'kembali',
                ]);
                if ($request->input('km_in')) {
                    $vehicle->update(['odometer_km' => max($vehicle->odometer_km, $request->input('km_in'))]);
                }
            }

            return redirect()->route('qr.scan', $token)->with('success', 'Log masuk dicatat.');
        }

        return redirect()->route('qr.scan', $token);
    }
}
