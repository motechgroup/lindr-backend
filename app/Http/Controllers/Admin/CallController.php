<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index()
    {
        $calls = CallSession::with(['caller', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalDurationMinutes = round(CallSession::sum('duration_seconds') / 60, 1);
        $totalTokensSpent = CallSession::sum('tokens_spent');
        $totalCreditsEarned = CallSession::sum('credits_earned');

        return view('admin.calls.index', compact('calls', 'totalDurationMinutes', 'totalTokensSpent', 'totalCreditsEarned'));
    }
}
