<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\CallSession;
use App\Models\Withdrawal;
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
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function topup(Request $request, $id)
    {
        $validated = $request->validate([
            'tokens' => 'required|integer',
            'credits' => 'required|integer',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $user->tokens = max(0, $user->tokens + $validated['tokens']);
        $user->credits = max(0, $user->credits + $validated['credits']);
        if ($validated['tokens'] > 0) {
            $user->total_topup_tokens += $validated['tokens'];
        }
        if ($validated['credits'] > 0) {
            $user->total_credits_earned += $validated['credits'];
        }
        $user->save();

        // Record transaction log entry
        Transaction::create([
            'user_id' => $user->id,
            'type' => $validated['tokens'] >= 0 ? 'topup' : 'adjustment',
            'amount_tokens' => $validated['tokens'],
            'amount_credits' => $validated['credits'],
            'amount_usd' => round(($validated['tokens'] / 100), 2),
            'payment_provider' => 'admin_manual',
            'reference' => 'ADMIN_TOPUP_' . strtoupper(bin2hex(random_bytes(4))),
            'status' => 'completed',
        ]);

        return back()->with('success', "Wallet updated for {$user->name}: +{$validated['tokens']} Tokens, +{$validated['credits']} Credits.");
    }

    public function updateBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'tokens' => 'required|integer|min:0',
            'credits' => 'required|integer|min:0',
            'level' => 'nullable|integer|min:1',
            'exp_points' => 'nullable|integer|min:0',
        ]);

        $user = User::findOrFail($id);
        $user->tokens = $validated['tokens'];
        $user->credits = $validated['credits'];
        if (isset($validated['level'])) $user->level = $validated['level'];
        if (isset($validated['exp_points'])) $user->exp_points = $validated['exp_points'];
        $user->save();

        return back()->with('success', "Updated token & credit balances for {$user->name}.");
    }

    public function updateProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'gender' => 'required|in:male,female',
            'country_code' => 'nullable|string|max:10',
            'country_name' => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($id);
        $user->update($validated);

        return back()->with('success', "Updated profile info for {$user->name}.");
    }

    public function history($id)
    {
        $user = User::findOrFail($id);
        $transactions = Transaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(15, ['*'], 'tx_page');
        $calls = CallSession::where('caller_id', $user->id)->orWhere('receiver_id', $user->id)->orderBy('created_at', 'desc')->paginate(15, ['*'], 'call_page');
        $withdrawals = Withdrawal::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(15, ['*'], 'w_page');

        return view('admin.users.history', compact('user', 'transactions', 'calls', 'withdrawals'));
    }

    public function toggleVerify($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = !$user->is_verified;
        $user->save();

        $statusText = $user->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "User {$user->name} is now {$statusText}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "User account {$userName} (ID: {$id}) deleted successfully.");
    }
}
