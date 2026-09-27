<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function topup(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required',
            'amountTokens' => 'required|integer|min:1',
            'amountUsd' => 'required|numeric',
            'provider' => 'required|string', // mpesa, flutterwave, paypal
            'phoneNumber' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $userId = $validated['userId'];
        $user = User::where('id', $userId)->orWhere('email', $userId)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $provider = strtolower($validated['provider']);
        $reference = $validated['reference'] ?? strtoupper($provider) . '_' . time() . '_' . bin2hex(random_bytes(3));

        // Credit tokens to user balance
        $user->tokens += $validated['amountTokens'];
        $user->total_topup_tokens += $validated['amountTokens'];
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount_tokens' => $validated['amountTokens'],
            'amount_usd' => $validated['amountUsd'],
            'payment_provider' => $provider,
            'reference' => $reference,
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'tokens' => (int) $user->tokens,
            'totalTopUpTokens' => (int) $user->total_topup_tokens,
            'provider' => $provider,
            'reference' => $reference,
            'message' => 'Tokens credited successfully via ' . strtoupper($provider)
        ]);
    }

    public function cashout(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required',
            'creditsAmount' => 'required|integer|min:100',
            'method' => 'required|string', // mpesa, paypal, epay, bank
            'accountDetails' => 'nullable|string',
            'countryCode' => 'nullable|string',
        ]);

        $userId = $validated['userId'];
        $user = User::where('id', $userId)->orWhere('email', $userId)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if ($user->credits < $validated['creditsAmount']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient credits balance. Available: ' . $user->credits . ' credits'
            ], 400);
        }

        // 100 Credits = $1.00 USD
        $creditsPerUsd = (int) (SystemSetting::where('key', 'credits_per_usd')->value('value') ?? 100);
        $amountUsd = round($validated['creditsAmount'] / $creditsPerUsd, 2);

        // Deduct credits from user
        $user->credits -= $validated['creditsAmount'];
        $user->save();

        $method = strtolower($validated['method']);

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'credits_amount' => $validated['creditsAmount'],
            'amount_usd' => $amountUsd,
            'payment_method' => $method,
            'account_details' => $validated['accountDetails'] ?? 'Default Account',
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'credits' => (int) $user->credits,
            'withdrawalId' => $withdrawal->id,
            'amountUsd' => $amountUsd,
            'method' => $method,
            'message' => 'Cashout request submitted successfully via ' . strtoupper($method)
        ]);
    }
}
