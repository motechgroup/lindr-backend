@extends('layouts.admin')

@section('title', 'Chat & Gift Logs')
@section('subtitle', 'Audit conversations and virtual gift transactions between app users')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Total Messages Sent</div>
        <div class="text-3xl font-black text-blue-400">{{ $messages->total() }} Messages</div>
    </div>
    <div class="glass-card p-6 rounded-3xl">
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Virtual Gifts Sent</div>
        <div class="text-3xl font-black text-pink-400">{{ number_format($giftsSentCount) }} Gifts</div>
    </div>
</div>

<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">Sender</th>
                    <th class="p-4">Receiver</th>
                    <th class="p-4">Message / Gift Content</th>
                    <th class="p-4">Tokens Charged</th>
                    <th class="p-4 rounded-r-xl">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($messages as $m)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $m->sender->avatar ?? '' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $m->sender->name ?? 'User' }}
                        </td>
                        <td class="p-4 font-bold text-pink-400 flex items-center gap-3">
                            <img src="{{ $m->receiver->avatar ?? '' }}" class="w-8 h-8 rounded-full object-cover">
                            {{ $m->receiver->name ?? 'User' }}
                        </td>
                        <td class="p-4 font-medium text-white">
                            @if($m->gift_id)
                                <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold">
                                    🎁 Gift: {{ $m->message_text }}
                                </span>
                            @else
                                {{ $m->message_text }}
                            @endif
                        </td>
                        <td class="p-4 font-semibold text-yellow-400">
                            {{ $m->tokens_spent > 0 ? '-' . $m->tokens_spent . ' Tokens' : 'Free' }}
                        </td>
                        <td class="p-4 font-mono text-xs text-gray-400">{{ $m->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
</div>
@endsection
