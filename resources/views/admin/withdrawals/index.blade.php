@extends('layouts.admin')

@section('title', 'Cashouts & Withdrawals')
@section('subtitle', 'Review and approve female creator credit cashout requests')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Pending Cashout Volume</div>
        <div class="text-3xl font-black text-yellow-400">${{ number_format($pendingAmountUsd, 2) }}</div>
    </div>
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Approved Cashout Volume</div>
        <div class="text-3xl font-black text-emerald-400">${{ number_format($approvedAmountUsd, 2) }}</div>
    </div>
</div>

<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">User</th>
                    <th class="p-4">Credits</th>
                    <th class="p-4">Amount (USD)</th>
                    <th class="p-4">Payment Method</th>
                    <th class="p-4">Account Details</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 rounded-r-xl text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($withdrawals as $w)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $w->user?->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80' }}" class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <div>{{ $w->user?->name ?? ('User #' . $w->user_id) }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">
                                    @if(($w->user?->country_code ?? 'KE') === 'KE')
                                        <span class="text-green-400 font-bold">🇰🇪 Kenya</span>
                                    @else
                                        <span class="text-blue-400 font-bold">🌐 {{ $w->user?->country_name ?? 'International' }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-semibold text-yellow-400">{{ number_format($w->credits_amount) }} Credits</td>
                        <td class="p-4 font-bold text-emerald-400">${{ number_format($w->amount_usd, 2) }}</td>
                        <td class="p-4">
                            @if(strtolower($w->payment_method) === 'mpesa')
                                <span class="px-2.5 py-1 rounded-lg bg-green-500/20 text-green-400 font-bold text-xs">📱 M-Pesa</span>
                            @elseif(strtolower($w->payment_method) === 'paypal')
                                <span class="px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-400 font-bold text-xs">🅿️ PayPal</span>
                            @elseif(strtolower($w->payment_method) === 'epay')
                                <span class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 font-bold text-xs">⚡ ePay Payout</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-bold text-xs">🏛 {{ strtoupper($w->payment_method) }}</span>
                            @endif
                        </td>
                        <td class="p-4 font-mono text-xs text-gray-300">{{ $w->account_details }}</td>
                        <td class="p-4">
                            @if($w->status === 'approved')
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold uppercase">Approved</span>
                            @elseif($w->status === 'rejected')
                                <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs font-bold uppercase">Rejected</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-bold uppercase">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-right flex items-center justify-end gap-2">
                            @if($w->status === 'pending')
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
                            @else
                                <span class="text-xs text-gray-500 font-semibold">Completed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $withdrawals->links() }}
    </div>
</div>
@endsection
