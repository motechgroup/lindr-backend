@extends('layouts.admin')

@section('title', 'User Profile & Account History')
@section('subtitle', 'Detailed wallet audit, transactions, call logs, and cashouts for ' . $user->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-2xl glass-card text-xs font-bold text-gray-300 hover:text-white flex items-center gap-2">
        <span>← Back to Users List</span>
    </a>

    <div class="flex items-center gap-3">
        <button onclick="document.getElementById('topupModal-{{ $user->id }}').classList.remove('hidden')" class="px-4 py-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg hover:brightness-110 transition flex items-center gap-2">
            <span>🪙 Top Up Wallet</span>
        </button>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete user account {{ $user->name }} ({{ $user->email }})? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-400 font-bold text-xs hover:bg-red-500/30 transition">
                🗑 Delete Account
            </button>
        </form>
    </div>
</div>

<!-- User Profile Hero Banner -->
<div class="glass-card p-8 rounded-3xl mb-8 relative overflow-hidden">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10">
        <img src="{{ $user->avatar }}" class="w-24 h-24 rounded-full object-cover border-4 border-pink-500/40 shadow-2xl">

        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                <h2 class="text-2xl font-black text-white">{{ $user->name }}</h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $user->gender === 'female' ? 'bg-pink-500/20 text-pink-400 border border-pink-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' }}">
                    {{ $user->gender }}
                </span>
                @if($user->is_verified)
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold border border-emerald-500/30">✓ Verified Host</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-bold border border-yellow-500/30">Pending Verification</span>
                @endif
            </div>

            <div class="text-sm font-mono text-gray-400 mt-1">{{ $user->email }} @if($user->google_id) • <span class="text-blue-400">Google Connected</span> @endif</div>
            <div class="text-xs text-gray-400 mt-2 flex flex-wrap items-center justify-center md:justify-start gap-4">
                <span>📍 {{ $user->country_name ?? 'International' }} ({{ $user->country_code ?? 'KE' }})</span>
                <span>📅 Joined: {{ $user->created_at ? $user->created_at->format('M d, Y • H:i') : 'N/A' }}</span>
                <span>🆔 User ID: #{{ $user->id }}</span>
            </div>
        </div>
    </div>

    <!-- Wallet Stat Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
        <div class="glass-card p-4 rounded-2xl bg-white/5 border border-white/10">
            <div class="text-xs font-bold text-gray-400 uppercase">Token Balance</div>
            <div class="text-2xl font-black text-yellow-400 mt-1">🪙 {{ number_format($user->tokens) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">Top-up Cumulative: {{ number_format($user->total_topup_tokens) }}</div>
        </div>

        <div class="glass-card p-4 rounded-2xl bg-white/5 border border-white/10">
            <div class="text-xs font-bold text-gray-400 uppercase">Credit Balance</div>
            <div class="text-2xl font-black text-emerald-400 mt-1">💎 {{ number_format($user->credits) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">≈ ${{ number_format($user->credits / 100, 2) }} USD (Earned: {{ number_format($user->total_credits_earned) }})</div>
        </div>

        <div class="glass-card p-4 rounded-2xl bg-white/5 border border-white/10">
            <div class="text-xs font-bold text-gray-400 uppercase">Current Level</div>
            <div class="text-2xl font-black text-purple-400 mt-1">Lv. {{ $user->level }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">EXP Points: {{ number_format($user->exp_points) }}</div>
        </div>

        <div class="glass-card p-4 rounded-2xl bg-white/5 border border-white/10">
            <div class="text-xs font-bold text-gray-400 uppercase">Account Status</div>
            <div class="text-2xl font-black text-white mt-1">Active</div>
            <div class="text-[10px] text-emerald-400 mt-0.5">Normal Standing</div>
        </div>
    </div>
</div>

<!-- Tabs Section: Transactions / Calls / Withdrawals -->
<div class="space-y-8">
    <!-- Transactions Ledger -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-white">Financial Transactions Ledger</h3>
                <p class="text-xs text-gray-400">Token top-ups, purchases, and manual wallet adjustments</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-gray-300">{{ $transactions->total() }} Records</span>
        </div>

        @if($transactions->isEmpty())
            <div class="text-center py-8 text-gray-500 text-xs font-bold">No transaction records found for this user.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                        <tr>
                            <th class="p-4 rounded-l-xl">Reference</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Tokens</th>
                            <th class="p-4">Credits</th>
                            <th class="p-4">USD Value</th>
                            <th class="p-4">Provider</th>
                            <th class="p-4 rounded-r-xl">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-4 font-mono text-xs text-gray-300">{{ $tx->reference ?? 'TX-'.$tx->id }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $tx->type === 'topup' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-purple-500/20 text-purple-400' }}">
                                        {{ $tx->type }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-yellow-400">+{{ number_format($tx->amount_tokens) }}</td>
                                <td class="p-4 font-bold text-emerald-400">+{{ number_format($tx->amount_credits) }}</td>
                                <td class="p-4 font-bold text-white">${{ number_format($tx->amount_usd, 2) }}</td>
                                <td class="p-4 text-xs text-gray-400 uppercase">{{ $tx->payment_provider }}</td>
                                <td class="p-4 text-xs text-gray-400">{{ $tx->created_at ? $tx->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>

    <!-- Video Call Logs -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-white">Live Call Logs</h3>
                <p class="text-xs text-gray-400">Incoming & outgoing video call sessions and earnings</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-gray-300">{{ $calls->total() }} Calls</span>
        </div>

        @if($calls->isEmpty())
            <div class="text-center py-8 text-gray-500 text-xs font-bold">No video call logs recorded for this user.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                        <tr>
                            <th class="p-4 rounded-l-xl">Role</th>
                            <th class="p-4">Duration</th>
                            <th class="p-4">Tokens Spent</th>
                            <th class="p-4">Credits Earned</th>
                            <th class="p-4 rounded-r-xl">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($calls as $call)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-4">
                                    @if($call->caller_id == $user->id)
                                        <span class="px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-bold">Caller</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-400 text-xs font-bold">Host Receiver</span>
                                    @endif
                                </td>
                                <td class="p-4 font-bold text-white">{{ gmdate("i:s", $call->duration_seconds) }} mins</td>
                                <td class="p-4 font-bold text-yellow-400">-{{ number_format($call->tokens_spent) }}</td>
                                <td class="p-4 font-bold text-emerald-400">+{{ number_format($call->credits_earned) }}</td>
                                <td class="p-4 text-xs text-gray-400">{{ $call->created_at ? $call->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $calls->links() }}</div>
        @endif
    </div>

    <!-- Cashouts & Withdrawals -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-white">Cashouts & Withdrawals History</h3>
                <p class="text-xs text-gray-400">Payout redemption requests</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-gray-300">{{ $withdrawals->total() }} Requests</span>
        </div>

        @if($withdrawals->isEmpty())
            <div class="text-center py-8 text-gray-500 text-xs font-bold">No cashout requests found for this user.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                        <tr>
                            <th class="p-4 rounded-l-xl">Method</th>
                            <th class="p-4">Account Detail</th>
                            <th class="p-4">Credits</th>
                            <th class="p-4">USD Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 rounded-r-xl">Requested At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($withdrawals as $w)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-4 uppercase font-bold text-xs text-white">{{ $w->method }}</td>
                                <td class="p-4 text-xs font-mono text-gray-300">{{ $w->account_details }}</td>
                                <td class="p-4 font-bold text-emerald-400">{{ number_format($w->credits_amount) }} CR</td>
                                <td class="p-4 font-bold text-white">${{ number_format($w->amount_usd, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $w->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : ($w->status === 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') }}">
                                        {{ $w->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-gray-400">{{ $w->created_at ? $w->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal: Top Up Wallet -->
<div id="topupModal-{{ $user->id }}" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card p-8 rounded-3xl max-w-md w-full border border-white/20 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-white">Top Up Wallet for {{ $user->name }}</h3>
            <button onclick="document.getElementById('topupModal-{{ $user->id }}').classList.add('hidden')" class="text-gray-400 hover:text-white font-bold text-lg">✕</button>
        </div>

        <form action="{{ route('admin.users.topup', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Tokens to Add (+ or -)</label>
                <input type="number" name="tokens" value="500" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-yellow-400 font-bold text-sm focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Credits to Add (+ or -)</label>
                <input type="number" name="credits" value="0" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-emerald-400 font-bold text-sm focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Note / Transaction Reason</label>
                <input type="text" name="note" placeholder="e.g. Admin promotional bonus or manual deposit" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" onclick="document.getElementById('topupModal-{{ $user->id }}').classList.add('hidden')" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold text-gray-300 hover:bg-white/20">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg">Confirm Top Up</button>
            </div>
        </form>
    </div>
</div>
@endsection
