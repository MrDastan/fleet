<?php

namespace App\Http\Controllers;

class AnomalyController extends Controller
{
    public function index()
    {
        return view('anomalies.index');
    }
}
