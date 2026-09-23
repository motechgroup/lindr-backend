<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::where('is_admin', false)->count();
        $malesCount = User::where('gender', 'male')->where('is_admin', false)->count();
        $femalesCount = User::where('gender', 'female')->where('is_admin', false)->count();
        
        $totalTopupsUsd = Transaction::where('type', 'topup')->sum('amount_usd');
        $pendingWithdrawals = Withdrawal::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $allWithdrawals = Withdrawal::with('user')->orderBy('created_at', 'desc')->take(20)->get();
        $recentUsers = User::where('is_admin', false)->orderBy('created_at', 'desc')->take(10)->get();
        $recentTransactions = Transaction::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact(
            'usersCount',
            'malesCount',
            'femalesCount',
            'totalTopupsUsd',
            'pendingWithdrawals',
            'allWithdrawals',
            'recentUsers',
            'recentTransactions'
        ));
    }

    public function approveWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'approved';
        $withdrawal->processed_at = now();
        $withdrawal->save();

        return back()->with('success', 'Withdrawal of $' . number_format($withdrawal->amount_usd, 2) . ' approved successfully.');
    }

    public function rejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'rejected';
        $withdrawal->processed_at = now();
        $withdrawal->save();

        // Refund credits back to female user
        $user = $withdrawal->user;
        if ($user) {
            $user->credits += $withdrawal->credits_amount;
            $user->save();
        }

        return back()->with('success', 'Withdrawal rejected and credits refunded to user.');
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = !$user->is_verified;
        $user->save();

        $statusText = $user->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "User {$user->name} is now {$statusText}.");
    }
}
