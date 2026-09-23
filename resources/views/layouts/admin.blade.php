<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') - Lindr Backend</title>
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
        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .nav-item-active {
            background: linear-gradient(135deg, rgba(255, 45, 85, 0.2) 0%, rgba(156, 39, 176, 0.2) 100%);
            border: 1px solid rgba(255, 45, 85, 0.4);
            color: #FFFFFF;
            font-weight: 700;
        }
    </style>
</head>
<body class="min-h-screen flex bg-[#0F0C20] text-white">

    <!-- Sticky Consistent Sidebar Menu -->
    <aside class="w-64 min-h-screen glass-card border-r border-white/10 flex flex-col justify-between p-4 sticky top-0 shrink-0 z-40">
        <div>
            <!-- Sidebar Brand Header -->
            <div class="flex items-center gap-3 px-3 py-4 mb-6 border-b border-white/10">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#FF2D55] to-[#9C27B0] flex items-center justify-center font-black text-xl shadow-lg">
                    L
                </div>
                <div>
                    <div class="text-lg font-black tracking-wider text-white">Lindr</div>
                    <div class="text-[10px] text-pink-400 font-extrabold uppercase tracking-widest">Admin Control</div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">📊</span> Dashboard
                </a>

                <a href="{{ route('admin.users') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.users*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">👥</span> App Users
                </a>

                <a href="{{ route('admin.calls') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.calls*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">📞</span> Video Call Logs
                </a>

                <a href="{{ route('admin.withdrawals') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.withdrawals*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">💸</span> Cashouts
                </a>

                <a href="{{ route('admin.transactions') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.transactions*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">💰</span> Financial Ledger
                </a>

                <a href="{{ route('admin.chats') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.chats*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">💬</span> Chat & Gift Logs
                </a>

                <a href="{{ route('admin.verifications') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.verifications*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">🛡️</span> Verifications
                </a>

                <a href="{{ route('admin.tasks') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.tasks*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">📋</span> Task Rewards
                </a>

                <a href="{{ route('admin.gateways') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.gateways*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">💳</span> Payment Gateways
                </a>

                <a href="{{ route('admin.tokens') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.tokens*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">🪙</span> Token Packages
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold transition {{ request()->routeIs('admin.settings*') ? 'nav-item-active' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="text-base">⚙️</span> System Settings
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer & Admin User Profile -->
        <div class="pt-4 border-t border-white/10">
            <div class="flex items-center justify-between p-2 rounded-2xl bg-white/5 mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-pink-500/20 text-pink-400 font-bold flex items-center justify-center text-xs">
                        A
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? 'admin@lindr.app' }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold hover:bg-red-500/20 transition flex items-center justify-center gap-2">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="border-b border-white/10 glass-card px-8 py-4 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-xl font-bold text-white">@yield('title', 'Admin Dashboard')</h1>
                <p class="text-xs text-gray-400">@yield('subtitle', 'Manage Lindr mobile application services & user activity')</p>
            </div>

            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    API Online
                </span>
            </div>
        </header>

        <!-- Main Page Content -->
        <main class="flex-1 p-8 space-y-8">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
