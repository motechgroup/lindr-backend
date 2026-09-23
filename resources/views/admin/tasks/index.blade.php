@extends('layouts.admin')

@section('title', 'Creator Task Rewards System')
@section('subtitle', 'Track onboarding, chat, and call milestones for female creators')

@section('content')
<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">Creator</th>
                    <th class="p-4">Task Title</th>
                    <th class="p-4">Progress</th>
                    <th class="p-4">EXP Reward</th>
                    <th class="p-4">Credit Reward</th>
                    <th class="p-4 rounded-r-xl">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($tasks as $t)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $t->user->avatar ?? '' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $t->user->name ?? 'Creator' }}
                        </td>
                        <td class="p-4 font-bold text-white">{{ $t->title }}</td>
                        <td class="p-4 font-mono text-xs text-gray-400">{{ $t->progress_text }}</td>
                        <td class="p-4 font-bold text-yellow-400">+{{ $t->reward_exp }} EXP</td>
                        <td class="p-4 font-bold text-emerald-400">+{{ $t->reward_credits }} Credits</td>
                        <td class="p-4">
                            @if($t->is_completed)
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Completed</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-bold">In Progress</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
