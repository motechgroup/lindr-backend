@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('subtitle', 'Platform overview, active calls, user stats, and revenue metrics')

@section('content')
<!-- Metrics Overview Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Total App Users</div>
        <div class="text-3xl font-black text-white">{{ number_format($usersCount) }}</div>
        <div class="text-xs text-gray-400 mt-2">
            <span class="text-blue-400 font-bold">{{ $malesCount }} Males</span> • 
            <span class="text-pink-400 font-bold">{{ $femalesCount }} Females</span>
        </div>
    </div>

    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Total Revenue (USD)</div>
        <div class="text-3xl font-black text-emerald-400">${{ number_format($totalTopupsUsd, 2) }}</div>
        <div class="text-xs text-gray-400 mt-2">Token purchases via Flutterwave & M-Pesa</div>
    </div>

    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Pending Cashout Requests</div>
        <div class="text-3xl font-black text-yellow-400">{{ $pendingWithdrawals->count() }}</div>
        <div class="text-xs text-gray-400 mt-2">Female Creator Credit Cashouts</div>
    </div>

    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">System Status</div>
        <div class="text-3xl font-black text-blue-400">100% Operational</div>
        <div class="text-xs text-gray-400 mt-2">Agora RTC & SQLite DB Active</div>
    </div>
</div>

<!-- Pending Female Credit Withdrawals -->
<div class="glass-card p-8 rounded-3xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold flex items-center gap-2">
            <span>💸</span> Pending Female Creator Credit Cashouts
        </h2>
        <a href="{{ route('admin.withdrawals') }}" class="text-xs font-semibold text-pink-400 hover:underline">View All Cashouts →</a>
    </div>

    @if($pendingWithdrawals->isEmpty())
        <div class="p-8 text-center text-gray-400 text-sm glass-card rounded-2xl">
            No pending cashout requests at this time.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                    <tr>
                        <th class="p-4 rounded-l-xl">User</th>
                        <th class="p-4">Credits</th>
                        <th class="p-4">Amount (USD)</th>
                        <th class="p-4">Payment Method</th>
                        <th class="p-4">Account Details</th>
                        <th class="p-4 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($pendingWithdrawals as $w)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="p-4 font-bold text-white flex items-center gap-3">
                                <img src="{{ $w->user?->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80' }}" class="w-8 h-8 rounded-full object-cover">
                                {{ $w->user?->name ?? ('User #' . $w->user_id) }}
                            </td>
                            <td class="p-4 font-semibold text-yellow-400">{{ number_format($w->credits_amount) }} Credits</td>
                            <td class="p-4 font-bold text-emerald-400">${{ number_format($w->amount_usd, 2) }}</td>
                            <td class="p-4 uppercase font-semibold text-gray-300">{{ $w->payment_method }}</td>
                            <td class="p-4 font-mono text-xs text-gray-400">{{ $w->account_details }}</td>
                            <td class="p-4 text-right flex items-center justify-end gap-2">
                                <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold hover:bg-emerald-500/30 transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.withdrawals.reject', $w->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 rounded-full bg-red-500/20 text-red-400 text-xs font-bold hover:bg-red-500/30 transition">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Recent Registered Users -->
<div class="glass-card p-8 rounded-3xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold flex items-center gap-2">
            <span>👥</span> Recent Mobile App Users
        </h2>
        <a href="{{ route('admin.users') }}" class="text-xs font-semibold text-pink-400 hover:underline">View All Users →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">User</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Gender</th>
                    <th class="p-4">Tokens</th>
                    <th class="p-4">Credits</th>
                    <th class="p-4">Verification</th>
                    <th class="p-4 rounded-r-xl text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($recentUsers as $u)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $u->avatar }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $u->name }}
                        </td>
                        <td class="p-4 font-mono text-xs text-gray-400">{{ $u->email }}</td>
                        <td class="p-4 uppercase font-bold text-xs {{ $u->gender === 'female' ? 'text-pink-400' : 'text-blue-400' }}">
                            {{ $u->gender }}
                        </td>
                        <td class="p-4 font-semibold text-yellow-400">{{ number_format($u->tokens) }}</td>
                        <td class="p-4 font-semibold text-emerald-400">{{ number_format($u->credits) }}</td>
                        <td class="p-4">
                            @if($u->is_verified)
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Verified</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-gray-500/20 text-gray-400 text-xs font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <form action="{{ route('admin.users.verify', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-full border border-white/20 text-xs font-semibold hover:border-white/40 transition">
                                    {{ $u->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
