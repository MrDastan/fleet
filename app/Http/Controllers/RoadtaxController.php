<?php

namespace App\Http\Controllers;

use App\Models\RoadtaxRecord;
use App\Models\Vehicle;

class RoadtaxController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('roadtax_expiry')->get();
        return view('roadtax.index', compact('vehicles'));
    }
}
