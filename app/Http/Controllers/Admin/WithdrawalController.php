<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingAmountUsd = Withdrawal::where('status', 'pending')->sum('amount_usd');
        $approvedAmountUsd = Withdrawal::where('status', 'approved')->sum('amount_usd');

        return view('admin.withdrawals.index', compact('withdrawals', 'pendingAmountUsd', 'approvedAmountUsd'));
    }

    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'approved';
        $withdrawal->processed_at = now();
        $withdrawal->save();

        return back()->with('success', 'Withdrawal request of $' . number_format($withdrawal->amount_usd, 2) . ' approved.');
    }

    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'rejected';
        $withdrawal->processed_at = now();
        $withdrawal->save();

        // Refund credits back to female creator
        if ($withdrawal->user) {
            $withdrawal->user->credits += $withdrawal->credits_amount;
            $withdrawal->user->save();
        }

        return back()->with('success', 'Withdrawal request rejected and credits refunded to creator.');
    }
}
