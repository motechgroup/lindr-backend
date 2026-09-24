<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lindr - Live Video Dating & Real Connections</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0F0C20;
            color: #FFFFFF;
        }
        .glow-pink {
            box-shadow: 0 0 50px -10px rgba(255, 45, 85, 0.4);
        }
        .glow-purple {
            box-shadow: 0 0 50px -10px rgba(156, 39, 176, 0.4);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #FF2D55 0%, #E040FB 50%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden">

    <!-- Top Navigation -->
    <header class="border-b border-white/10 glass-card sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#FF2D55] to-[#9C27B0] flex items-center justify-center font-extrabold text-xl shadow-lg">
                    L
                </div>
                <span class="text-2xl font-black tracking-wider text-white">Lindr</span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-300">
                <a href="#features" class="hover:text-white transition">Features</a>
                <a href="#economy" class="hover:text-white transition">Rewards & Tokens</a>
                <a href="#verification" class="hover:text-white transition">Verification</a>
                <a href="#download" class="hover:text-white transition">Download App</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="/access" class="text-xs font-semibold px-4 py-2 rounded-full border border-white/20 text-gray-300 hover:text-white hover:border-white/40 transition">
                    Admin Portal
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-20 pb-16 px-6 max-w-7xl mx-auto text-center z-10">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card border border-pink-500/30 text-pink-400 text-xs font-bold uppercase tracking-widest mb-8">
            <span class="w-2 h-2 rounded-full bg-pink-500 animate-ping"></span>
            #1 Live Video Dating App
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
            Real Connections in <br>
            <span class="text-gradient">Real-Time Video Calls</span>
        </h1>

        <p class="max-w-2xl mx-auto text-gray-400 text-lg md:text-xl mb-10 leading-relaxed">
            Skip the endless text matches. Connect face-to-face with verified singles instantly through high-speed live video matchmaking.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
            <a href="#download" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-white text-lg shadow-xl hover:opacity-95 transition transform hover:-translate-y-0.5">
                Download Mobile App
            </a>
            <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-2xl glass-card border border-white/20 font-bold text-white text-lg hover:bg-white/10 transition">
                Explore Features
            </a>
        </div>

        <!-- Live Platform Stats Banner -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="glass-card p-6 rounded-3xl text-center">
                <div class="text-3xl font-black text-pink-500 mb-1">{{ number_format($activeUsersCount) }}+</div>
                <div class="text-gray-400 text-sm font-semibold">Active App Users</div>
            </div>
            <div class="glass-card p-6 rounded-3xl text-center">
                <div class="text-3xl font-black text-purple-400 mb-1">{{ number_format($totalCallsCount) }}+</div>
                <div class="text-gray-400 text-sm font-semibold">Video Calls Completed</div>
            </div>
            <div class="glass-card p-6 rounded-3xl text-center">
                <div class="text-3xl font-black text-yellow-400 mb-1">${{ number_format($totalEarningsUsd, 2) }}</div>
                <div class="text-gray-400 text-sm font-semibold">Creator Rewards Paid</div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-20 border-t border-white/10 bg-white/[0.02]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Designed for Genuine Intimacy</h2>
                <p class="text-gray-400 text-base max-w-xl mx-auto">Everything you need to find authentic dates and build real chemistry.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-3xl relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-pink-500/20 text-pink-500 flex items-center justify-center text-2xl font-bold mb-6">
                        📹
                    </div>
                    <h3 class="text-xl font-bold mb-3">Instant HD Video Matches</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">High-definition low-latency video streaming powered by Agora RTC. Match instantly with online profiles opposite your gender.</p>
                </div>

                <div class="glass-card p-8 rounded-3xl relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-2xl font-bold mb-6">
                        🎁
                    </div>
                    <h3 class="text-xl font-bold mb-3">Interactive Virtual Gifts</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Send virtual gifts during chat or calls to make lasting impressions and reward female creators in real-time.</p>
                </div>

                <div class="glass-card p-8 rounded-3xl relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-2xl font-bold mb-6">
                        🛡️
                    </div>
                    <h3 class="text-xl font-bold mb-3">Biometric Liveness Verification</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">4-step real-time head-shake, nod, mouth-open, and blink detection guarantees 100% genuine users without catfishing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section id="download" class="py-20 px-6 max-w-5xl mx-auto text-center">
        <div class="glass-card p-12 rounded-3xl border border-pink-500/30 relative overflow-hidden">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-6">Get Lindr On Mobile</h2>
            <p class="text-gray-300 max-w-xl mx-auto mb-10 text-lg">Available on iOS and Android devices. Download the mobile app to sign up, verify your profile, and start video dating today.</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#" class="px-8 py-4 rounded-2xl bg-white text-black font-extrabold text-base flex items-center justify-center gap-3 hover:bg-gray-200 transition w-full sm:w-auto">
                    <span></span> App Store
                </a>
                <a href="#" class="px-8 py-4 rounded-2xl bg-white/10 text-white font-extrabold text-base border border-white/20 flex items-center justify-center gap-3 hover:bg-white/20 transition w-full sm:w-auto">
                    <span>▶</span> Google Play
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-8 px-6 text-center text-sm text-gray-500">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>&copy; {{ date('Y') }} Lindr Platform Inc. All rights reserved.</div>
            <div class="flex items-center gap-6">
                <a href="/privacy-policy" class="hover:text-gray-300 transition">Privacy Policy</a>
                <a href="/terms-of-service" class="hover:text-gray-300 transition">Terms of Service</a>
                <a href="/access" class="hover:text-gray-300 transition">Admin Portal Access</a>
            </div>
        </div>
    </footer>

</body>
</html>
