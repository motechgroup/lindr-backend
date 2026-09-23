<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->has('gender') && in_array($request->gender, ['male', 'female'])) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function updateBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'tokens' => 'required|integer|min:0',
            'credits' => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($id);
        $user->tokens = $validated['tokens'];
        $user->credits = $validated['credits'];
        $user->save();

        return back()->with('success', "Updated token/credit balance for {$user->name}.");
    }

    public function toggleVerify($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = !$user->is_verified;
        $user->save();

        $statusText = $user->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "User {$user->name} is now {$statusText}.");
    }
}
