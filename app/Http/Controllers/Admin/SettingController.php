<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settingsGrouped = SystemSetting::whereNotIn('category', ['gateways', 'google'])
            ->get()
            ->groupBy('category');

        return view('admin.settings.index', compact('settingsGrouped'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Application Operational Settings & Rates updated successfully.');
    }
}
