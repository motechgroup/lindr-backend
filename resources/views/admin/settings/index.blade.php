@extends('layouts.admin')

@section('title', 'System Rates & App Rules Settings')
@section('subtitle', 'Configure call cost rates per minute, chat messaging costs, female creator payout %, and app features')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
    @csrf

    <!-- 1. Video Call Cost Rates (Male User Levels) -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 font-bold flex items-center justify-center text-xl">
                📞
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Video Call Cost Rates (Tokens / Min)</h2>
                <p class="text-xs text-gray-400">Configure token deductions per minute for male users based on their account level</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(isset($settingsGrouped['call_rates']))
                @foreach($settingsGrouped['call_rates'] as $s)
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ $s->description }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="number" 
                                name="{{ $s->key }}" 
                                value="{{ $s->value }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-yellow-400 font-bold text-sm focus:outline-none focus:border-pink-500"
                            >
                            <span class="text-xs text-gray-400 font-semibold">Tokens/min</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- 2. Chat & Messaging Charges -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center text-xl">
                💬
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Chat & Messaging Charges</h2>
                <p class="text-xs text-gray-400">Token costs for sending text messages, photos, and voice notes</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(isset($settingsGrouped['chat_rates']))
                @foreach($settingsGrouped['chat_rates'] as $s)
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ $s->description }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="number" 
                                name="{{ $s->key }}" 
                                value="{{ $s->value }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-yellow-400 font-bold text-sm focus:outline-none focus:border-pink-500"
                            >
                            <span class="text-xs text-gray-400 font-semibold">Tokens</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- 3. Creator Payout & Earnings Split -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center text-xl">
                💸
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Creator Payout % & Level Pay Rates</h2>
                <p class="text-xs text-gray-400">Set creator revenue split %, credit pay rates per level, and cashout thresholds</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(isset($settingsGrouped['payouts']))
                @foreach($settingsGrouped['payouts'] as $s)
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ $s->description }}
                        </label>
                        <input 
                            type="text" 
                            name="{{ $s->key }}" 
                            value="{{ $s->value }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-emerald-400 font-bold text-sm focus:outline-none focus:border-pink-500"
                        >
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- 4. Video Call Engine & Agora Config -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-400 font-bold flex items-center justify-center text-xl">
                📹
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Video Call Engine Config (Agora)</h2>
                <p class="text-xs text-gray-400">Agora RTC credentials and maximum call duration parameters</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(isset($settingsGrouped['agora']))
                @foreach($settingsGrouped['agora'] as $s)
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ $s->description }}
                        </label>
                        <input 
                            type="text" 
                            name="{{ $s->key }}" 
                            value="{{ $s->value }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-pink-500"
                        >
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- 5. App Rules & Bonus Rewards -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 text-yellow-400 font-bold flex items-center justify-center text-xl">
                🎁
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">App Rules & Bonus Rewards</h2>
                <p class="text-xs text-gray-400">New user welcome gifts, biometric verification requirements, and EXP bonus settings</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(isset($settingsGrouped['app_rules']))
                @foreach($settingsGrouped['app_rules'] as $s)
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ $s->description }}
                        </label>
                        <input 
                            type="text" 
                            name="{{ $s->key }}" 
                            value="{{ $s->value }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-bold text-sm focus:outline-none focus:border-pink-500"
                        >
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
        <button type="submit" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-white text-sm shadow-xl hover:opacity-95 transition">
            Save Operational Rates & Settings
        </button>
    </div>
</form>
@endsection
