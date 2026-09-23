@extends('layouts.admin')

@section('title', 'Payment Gateways & Google Auth Credentials')
@section('subtitle', 'Manage Flutterwave, Safaricom M-Pesa, and Google OAuth Client IDs directly from the admin panel')

@section('content')
<form action="{{ route('admin.gateways.update') }}" method="POST" class="space-y-8">
    @csrf

    <!-- 1. Flutterwave Gateway Credentials -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-400 font-bold flex items-center justify-center text-xl">
                💳
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Flutterwave Payment Gateway</h2>
                <p class="text-xs text-gray-400">Card payments, Mobile Money, and global token top-up processing</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">FLW Public Key</label>
                <input 
                    type="text" 
                    name="flw_public_key" 
                    value="{{ $gatewaySettings['flw_public_key']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">FLW Secret Key</label>
                <input 
                    type="password" 
                    name="flw_secret_key" 
                    value="{{ $gatewaySettings['flw_secret_key']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">FLW Webhook Secret Hash</label>
                <input 
                    type="text" 
                    name="flw_secret_hash" 
                    value="{{ $gatewaySettings['flw_secret_hash']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-orange-500"
                >
            </div>
        </div>
    </div>

    <!-- 2. Safaricom M-Pesa Daraja Credentials -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center text-xl">
                📲
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Safaricom M-Pesa Daraja API</h2>
                <p class="text-xs text-gray-400">STK Push token purchases and female creator MPESA cashouts</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Consumer Key</label>
                <input 
                    type="text" 
                    name="mpesa_consumer_key" 
                    value="{{ $gatewaySettings['mpesa_consumer_key']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Consumer Secret</label>
                <input 
                    type="password" 
                    name="mpesa_consumer_secret" 
                    value="{{ $gatewaySettings['mpesa_consumer_secret']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Paybill / Shortcode</label>
                <input 
                    type="text" 
                    name="mpesa_shortcode" 
                    value="{{ $gatewaySettings['mpesa_shortcode']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Online Passkey</label>
                <input 
                    type="password" 
                    name="mpesa_passkey" 
                    value="{{ $gatewaySettings['mpesa_passkey']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
                >
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Instant Webhook Callback URL</label>
                <input 
                    type="text" 
                    name="mpesa_callback_url" 
                    value="{{ $gatewaySettings['mpesa_callback_url']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
                >
            </div>
        </div>
    </div>

    <!-- 3. ePay Global Payout Gateway -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 font-bold flex items-center justify-center text-xl">
                ⚡
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">ePay Global Payout Gateway</h2>
                <p class="text-xs text-gray-400">Direct global ePay merchant payouts and instant creator credit withdrawals</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">ePay Merchant ID</label>
                <input 
                    type="text" 
                    name="epay_merchant_id" 
                    value="{{ $gatewaySettings['epay_merchant_id']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-purple-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">ePay Secret API Key</label>
                <input 
                    type="password" 
                    name="epay_api_key" 
                    value="{{ $gatewaySettings['epay_api_key']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-purple-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">ePay Environment Mode</label>
                <select 
                    name="epay_environment" 
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-bold text-xs focus:outline-none focus:border-purple-500"
                >
                    <option value="sandbox" {{ ($gatewaySettings['epay_environment']->value ?? '') === 'sandbox' ? 'selected' : '' }} class="bg-gray-900 text-white">Sandbox (Testing)</option>
                    <option value="production" {{ ($gatewaySettings['epay_environment']->value ?? '') === 'production' ? 'selected' : '' }} class="bg-gray-900 text-white">Production (Live Payouts)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 4. Google OAuth Credentials -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center text-xl">
                🌐
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Google OAuth 2.0 Client IDs</h2>
                <p class="text-xs text-gray-400">Mobile app and web Google sign-in configuration</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Google Web Client ID</label>
                <input 
                    type="text" 
                    name="google_web_client_id" 
                    value="{{ $gatewaySettings['google_web_client_id']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-blue-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Google iOS Client ID</label>
                <input 
                    type="text" 
                    name="google_ios_client_id" 
                    value="{{ $gatewaySettings['google_ios_client_id']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-blue-500"
                >
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Google Android Client ID</label>
                <input 
                    type="text" 
                    name="google_android_client_id" 
                    value="{{ $gatewaySettings['google_android_client_id']->value ?? '' }}"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-white text-sm shadow-xl hover:opacity-95 transition">
            Save Gateway & Auth Credentials
        </button>
    </div>
</form>
@endsection
