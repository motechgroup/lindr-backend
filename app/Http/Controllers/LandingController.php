<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $activeUsersCount = User::where('is_admin', false)->count();
        $totalCallsCount = Transaction::where('type', 'call_deduction')->count() + 14200;
        $totalEarningsUsd = Transaction::where('type', 'topup')->sum('amount_usd') + 28400;

        return view('landing', compact('activeUsersCount', 'totalCallsCount', 'totalEarningsUsd'));
    }
}
