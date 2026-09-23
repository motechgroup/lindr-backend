<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function topup(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|string',
            'amountTokens' => 'required|integer|min:1',
            'amountUsd' => 'required|numeric',
            'provider' => 'required|string', // flutterwave, mpesa
            'reference' => 'nullable|string',
        ]);

        $user = User::find($validated['userId']) ?? User::first();
        if ($user) {
            $user->tokens += $validated['amountTokens'];
            $user->total_topup_tokens += $validated['amountTokens'];
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount_tokens' => $validated['amountTokens'],
                'amount_usd' => $validated['amountUsd'],
                'payment_provider' => $validated['provider'],
                'reference' => $validated['reference'] ?? 'TXN_' . time(),
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'tokens' => $user ? $user->tokens : 0,
            'totalTopUpTokens' => $user ? $user->total_topup_tokens : 0,
            'message' => 'Tokens credited successfully'
        ]);
    }

    public function cashout(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|string',
            'creditsAmount' => 'required|integer|min:100',
            'method' => 'required|string',
            'accountDetails' => 'nullable|string',
        ]);

        $user = User::find($validated['userId']) ?? User::first();
        if (!$user || $user->credits < $validated['creditsAmount']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient credits balance'
            ], 400);
        }

        $amountUsd = $validated['creditsAmount'] / 100;
        $user->credits -= $validated['creditsAmount'];
        $user->save();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'credits_amount' => $validated['creditsAmount'],
            'amount_usd' => $amountUsd,
            'payment_method' => $validated['method'],
            'account_details' => $validated['accountDetails'] ?? 'Default Account',
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'credits' => $user->credits,
            'withdrawalId' => $withdrawal->id,
            'message' => 'Withdrawal request submitted successfully'
        ]);
    }
}
