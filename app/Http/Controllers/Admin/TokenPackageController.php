<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TokenPackage;
use App\Models\User;
use Illuminate\Http\Request;

class TokenPackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = TokenPackage::orderBy('tokens', 'asc')->get();

        // Query users for direct Token Balance Management
        $userQuery = User::where('is_admin', false);
        if ($request->filled('user_search')) {
            $search = trim($request->user_search);
            $userQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $users = $userQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'user_page');
        $totalTokensInCirculation = User::sum('tokens');
        $totalTokensPurchased = User::sum('total_topup_tokens');

        return view('admin.tokens.index', compact('packages', 'users', 'totalTokensInCirculation', 'totalTokensPurchased'));
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

        return back()->with('success', "Token package {$pkg->name} updated successfully.");
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
