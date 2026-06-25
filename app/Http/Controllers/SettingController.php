<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $fields = [
            'company_name', 'company_phone', 'company_email', 'company_address',
            'notif_email', 'notif_system', 'notif_whatsapp', 'notif_recipients',
        ];

        foreach ($fields as $key) {
            $value = $request->input($key);
            if (in_array($key, ['notif_email', 'notif_system', 'notif_whatsapp'])) {
                $value = $request->has($key) ? '1' : '0';
            }
            $group = str_starts_with($key, 'company_') ? 'company' : 'notification';
            Setting::set($key, $value, $group);
        }

        return redirect()->route('settings.index')->with('success', 'Tetapan disimpan.');
    }
}
