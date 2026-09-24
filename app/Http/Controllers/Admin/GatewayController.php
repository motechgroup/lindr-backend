<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function index()
    {
        $gatewaySettings = SystemSetting::whereIn('category', ['gateways', 'google'])->get()->keyBy('key');

        foreach ($gatewaySettings as $key => $setting) {
            if (empty($setting->value) || str_contains($setting->value, 'your-google') || str_contains($setting->value, 'your_')) {
                $envKey = strtoupper($key);
                $envValue = env($envKey);
                if (!empty($envValue)) {
                    $setting->value = $envValue;
                }
            }
        }

        return view('admin.gateways.index', compact('gatewaySettings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Payment Gateways & Google Auth Credentials updated successfully.');
    }
}
