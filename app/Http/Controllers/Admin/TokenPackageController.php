<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TokenPackage;
use Illuminate\Http\Request;

class TokenPackageController extends Controller
{
    public function index()
    {
        $packages = TokenPackage::orderBy('tokens', 'asc')->get();
        return view('admin.tokens.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'tokens' => 'required|integer|min:1',
            'price_usd' => 'required|numeric|min:0.01',
            'badge' => 'nullable|string',
            'is_popular' => 'nullable|boolean',
        ]);

        TokenPackage::create([
            'name' => $validated['name'],
            'tokens' => $validated['tokens'],
            'price_usd' => $validated['price_usd'],
            'badge' => $validated['badge'],
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Token package created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'tokens' => 'required|integer|min:1',
            'price_usd' => 'required|numeric|min:0.01',
            'badge' => 'nullable|string',
        ]);

        $pkg = TokenPackage::findOrFail($id);
        $pkg->update([
            'name' => $validated['name'],
            'tokens' => $validated['tokens'],
            'price_usd' => $validated['price_usd'],
            'badge' => $validated['badge'],
            'is_popular' => $request->boolean('is_popular'),
        ]);

        return back()->with('success', "Token package {$pkg->name} updated.");
    }

    public function toggle($id)
    {
        $pkg = TokenPackage::findOrFail($id);
        $pkg->is_active = !$pkg->is_active;
        $pkg->save();

        $statusText = $pkg->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Package {$pkg->name} is now {$statusText}.");
    }

    public function destroy($id)
    {
        $pkg = TokenPackage::findOrFail($id);
        $pkg->delete();

        return back()->with('success', 'Token package deleted.');
    }
}
