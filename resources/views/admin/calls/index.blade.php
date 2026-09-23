@extends('layouts.admin')

@section('title', 'Video Call Logs')
@section('subtitle', 'Monitor active and past Agora RTC video call sessions')

@section('content')
<!-- Call Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Total Call Time</div>
        <div class="text-3xl font-black text-purple-400">{{ $totalDurationMinutes }} Mins</div>
    </div>
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Male Tokens Charged</div>
        <div class="text-3xl font-black text-yellow-400">{{ number_format($totalTokensSpent) }} Tokens</div>
    </div>
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Female Creator Earnings</div>
        <div class="text-3xl font-black text-emerald-400">{{ number_format($totalCreditsEarned) }} Credits</div>
    </div>
</div>

<!-- Calls Table -->
<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">Caller (Male)</th>
                    <th class="p-4">Receiver (Female)</th>
                    <th class="p-4">Agora Channel</th>
                    <th class="p-4">Duration</th>
                    <th class="p-4">Tokens Spent</th>
                    <th class="p-4">Credits Paid</th>
                    <th class="p-4 rounded-r-xl">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($calls as $c)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $c->caller->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=600&q=80' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $c->caller->name ?? 'Male User' }}
                        </td>
                        <td class="p-4 font-bold text-pink-400 flex items-center gap-3">
                            <img src="{{ $c->receiver->avatar ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $c->receiver->name ?? 'Female Creator' }}
                        </td>
                        <td class="p-4 font-mono text-xs text-gray-400">{{ $c->channel_name }}</td>
                        <td class="p-4 font-semibold text-gray-200">{{ round($c->duration_seconds / 60, 1) }} Mins</td>
                        <td class="p-4 font-bold text-yellow-400">-{{ $c->tokens_spent }} Tokens</td>
                        <td class="p-4 font-bold text-emerald-400">+{{ $c->credits_earned }} Credits</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold uppercase">
                                {{ $c->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $calls->links() }}
    </div>
</div>
@endsection
