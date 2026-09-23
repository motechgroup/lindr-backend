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
