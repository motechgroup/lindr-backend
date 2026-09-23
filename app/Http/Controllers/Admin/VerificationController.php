<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', false)
            ->orderBy('is_verified', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.verifications.index', compact('users'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->exp_points += 300;
        $user->save();

        return back()->with('success', "Approved biometric verification for {$user->name}. Added +300 EXP.");
    }
}
