@extends('layouts.admin')

@section('title', 'Financial Ledger')
@section('subtitle', 'Audit top-ups, gifts, call deductions, and payment gateways')

@section('content')
<div class="glass-card p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.transactions') }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ !request('type') ? 'bg-pink-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            All Transactions
        </a>
        <a href="{{ route('admin.transactions', ['type' => 'topup']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ request('type') === 'topup' ? 'bg-emerald-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            Token Purchases
        </a>
        <a href="{{ route('admin.transactions', ['type' => 'gift']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ request('type') === 'gift' ? 'bg-purple-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            Virtual Gifts
        </a>
    </div>

    <div class="text-right">
        <div class="text-xs text-gray-400 font-bold uppercase">Total Topups Revenue</div>
        <div class="text-2xl font-black text-emerald-400">${{ number_format($totalTopupUsd, 2) }}</div>
    </div>
</div>

<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">User</th>
                    <th class="p-4">Type</th>
                    <th class="p-4">Tokens</th>
                    <th class="p-4">Amount (USD)</th>
                    <th class="p-4">Gateway / Provider</th>
                    <th class="p-4">Reference</th>
                    <th class="p-4 rounded-r-xl">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($transactions as $t)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $t->user->avatar ?? '' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $t->user->name ?? 'User' }}
                        </td>
                        <td class="p-4 font-bold text-xs uppercase text-pink-400">{{ $t->type }}</td>
                        <td class="p-4 font-semibold text-yellow-400">+{{ number_format($t->amount_tokens) }} Tokens</td>
                        <td class="p-4 font-bold text-emerald-400">${{ number_format($t->amount_usd, 2) }}</td>
                        <td class="p-4 uppercase font-semibold text-gray-300">{{ $t->payment_provider ?? 'N/A' }}</td>
                        <td class="p-4 font-mono text-xs text-gray-400">{{ $t->reference }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold uppercase">
                                {{ $t->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
