@extends('layouts.admin')

@section('title', 'Token Management & Pricing Hub')
@section('subtitle', 'Manage user token balances, perform direct top-ups/adjustments, and configure app token purchase packages')

@section('content')
<div x-data="{ 
    userTopupModal: false, 
    userBalanceModal: false, 
    editPackageModal: false, 
    activeUser: null, 
    activePackage: null 
}" class="space-y-8">

    <!-- Top Token Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-yellow-500/20 text-yellow-400 font-extrabold flex items-center justify-center text-2xl border border-yellow-500/30">
                🪙
            </div>
            <div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tokens in Circulation</div>
                <div class="text-2xl font-black text-yellow-400 mt-1">{{ number_format($totalTokensInCirculation) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Total active tokens held across all users</div>
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 font-extrabold flex items-center justify-center text-2xl border border-emerald-500/30">
                💎
            </div>
            <div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Tokens Purchased</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($totalTokensPurchased) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Historical total top-up tokens credited</div>
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-500/20 text-purple-400 font-extrabold flex items-center justify-center text-2xl border border-purple-500/30">
                📦
            </div>
            <div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Active Token Packages</div>
                <div class="text-2xl font-black text-purple-400 mt-1">{{ $packages->where('is_active', true)->count() }} / {{ $packages->count() }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Configured pricing tiers for mobile app</div>
            </div>
        </div>
    </div>

    <!-- SECTION 1: USER TOKEN BALANCES & DIRECT MANAGEMENT -->
    <div class="glass-card p-8 rounded-3xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6 pb-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/20 text-yellow-400 font-bold flex items-center justify-center text-xl">
                    👥
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">User Token Balances & Direct Management</h2>
                    <p class="text-xs text-gray-400">Search users, edit exact token balances, or grant manual top-up / promotional tokens</p>
                </div>
            </div>

            <!-- User Search Bar -->
            <form action="{{ route('admin.tokens') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <input 
                    type="text" 
                    name="user_search" 
                    value="{{ request('user_search') }}" 
                    placeholder="Search user by name, email, or ID..." 
                    class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-yellow-500 w-full md:w-72"
                >
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-yellow-500 to-amber-600 text-black font-extrabold text-xs shadow-lg hover:opacity-90">
                    Search User
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                    <tr>
                        <th class="p-4 rounded-l-xl">User Account</th>
                        <th class="p-4">Gender</th>
                        <th class="p-4">Level</th>
                        <th class="p-4">Token Balance</th>
                        <th class="p-4">Credits</th>
                        <th class="p-4">Total Top-up</th>
                        <th class="p-4 rounded-r-xl text-right">Manage Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $u)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="p-4 font-bold text-white flex items-center gap-3">
                                <img src="{{ $u->avatar }}" class="w-10 h-10 rounded-full object-cover border border-white/10">
                                <div>
                                    <div class="text-sm font-bold text-white flex items-center gap-1.5">
                                        {{ $u->name }}
                                        @if($u->is_verified)
                                            <span class="text-xs text-emerald-400" title="Verified Account">✓</span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-mono text-gray-400">{{ $u->email }}</div>
                                </div>
                            </td>
                            <td class="p-4 uppercase font-bold text-xs {{ $u->gender === 'female' ? 'text-pink-400' : 'text-blue-400' }}">
                                {{ $u->gender }}
                            </td>
                            <td class="p-4 font-bold text-xs text-yellow-400">Lvl {{ $u->level }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1.5 rounded-xl bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-extrabold text-sm">
                                    🪙 {{ number_format($u->tokens) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-extrabold text-sm">
                                    💎 {{ number_format($u->credits) }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs text-gray-400">
                                {{ number_format($u->total_topup_tokens) }} Tokens
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Top Up / Adjust Modal Launcher -->
                                    <button 
                                        type="button" 
                                        @click="activeUser = @js($u); userTopupModal = true"
                                        class="px-3 py-1.5 rounded-xl bg-yellow-500/20 border border-yellow-500/30 text-yellow-300 text-xs font-bold hover:bg-yellow-500/30 transition flex items-center gap-1"
                                    >
                                        <span>➕</span> Top Up
                                    </button>

                                    <!-- Edit Exact Balance Launcher -->
                                    <button 
                                        type="button" 
                                        @click="activeUser = @js($u); userBalanceModal = true"
                                        class="px-3 py-1.5 rounded-xl bg-blue-500/20 border border-blue-500/30 text-blue-300 text-xs font-bold hover:bg-blue-500/30 transition flex items-center gap-1"
                                    >
                                        <span>✏️</span> Set Balance
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400 italic">No user accounts found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->appends(['user_search' => request('user_search')])->links() }}
        </div>
    </div>

    <!-- SECTION 2: TOKEN PRICING PACKAGES MANAGER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 1. Create New Package Form -->
        <div class="glass-card p-6 rounded-3xl h-fit">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span>➕</span> Create Token Package
            </h2>

            <form action="{{ route('admin.tokens.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Package Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="e.g. Starter Pack"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-yellow-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Token Quantity</label>
                    <input 
                        type="number" 
                        name="tokens" 
                        required 
                        placeholder="e.g. 500"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-yellow-400 font-bold text-sm focus:outline-none focus:border-yellow-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Price (USD $)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="price_usd" 
                        required 
                        placeholder="e.g. 4.99"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-emerald-400 font-bold text-sm focus:outline-none focus:border-yellow-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Badge / Tag (Optional)</label>
                    <input 
                        type="text" 
                        name="badge" 
                        placeholder="e.g. POPULAR, BEST VALUE"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-yellow-500"
                    >
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_popular" value="1" class="rounded border-gray-600 bg-white/10 text-yellow-500 focus:ring-0">
                    <label class="text-xs font-semibold text-gray-300">Highlight as Popular Pack</label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-yellow-500 to-amber-600 font-extrabold text-black text-xs shadow-lg hover:opacity-95 transition">
                    Create Package
                </button>
            </form>
        </div>

        <!-- 2. Existing Packages List -->
        <div class="lg:col-span-2 glass-card p-8 rounded-3xl">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <span>📦</span> Active & Configured Packages
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                        <tr>
                            <th class="p-4 rounded-l-xl">Package Name</th>
                            <th class="p-4">Tokens</th>
                            <th class="p-4">Price ($)</th>
                            <th class="p-4">Badge</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 rounded-r-xl text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($packages as $pkg)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-4 font-bold text-white">
                                    {{ $pkg->name }}
                                    @if($pkg->is_popular)
                                        <span class="ml-2 px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-400 text-[10px] uppercase font-extrabold border border-yellow-500/30">Popular</span>
                                    @endif
                                </td>
                                <td class="p-4 font-bold text-yellow-400">🪙 {{ number_format($pkg->tokens) }}</td>
                                <td class="p-4 font-bold text-emerald-400">${{ number_format($pkg->price_usd, 2) }}</td>
                                <td class="p-4 font-mono text-xs text-gray-400">{{ $pkg->badge ?? '-' }}</td>
                                <td class="p-4">
                                    @if($pkg->is_active)
                                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Active</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-gray-500/20 text-gray-400 text-xs font-bold">Disabled</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right flex items-center justify-end gap-2">
                                    <!-- Edit Package Button -->
                                    <button 
                                        type="button" 
                                        @click="activePackage = @js($pkg); editPackageModal = true"
                                        class="px-3 py-1 rounded-xl bg-blue-500/20 text-blue-300 text-xs font-bold hover:bg-blue-500/30 transition"
                                    >
                                        Edit
                                    </button>

                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.tokens.toggle', $pkg->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-xl bg-white/10 text-xs font-semibold hover:bg-white/20 transition">
                                            {{ $pkg->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>

                                    <!-- Delete Package Button -->
                                    <form action="{{ route('admin.tokens.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Delete token package?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded-xl bg-red-500/20 text-red-400 text-xs font-bold hover:bg-red-500/30 transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Quick Wallet Top-Up / Adjust Tokens -->
    <div 
        x-show="userTopupModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
        <div 
            @click.away="userTopupModal = false" 
            class="glass-card p-8 rounded-3xl max-w-md w-full border border-yellow-500/30 shadow-2xl relative"
        >
            <button @click="userTopupModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">✕</button>
            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <span>🪙</span> Top-Up / Adjust User Tokens
            </h3>
            <p class="text-xs text-gray-400 mb-6">
                Grant or subtract tokens for <span class="text-yellow-400 font-bold" x-text="activeUser ? activeUser.name : ''"></span>
            </p>

            <form :action="activeUser ? '/admin/users/' + activeUser.id + '/topup' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Add Tokens (+ to add, - to subtract)</label>
                    <input 
                        type="number" 
                        name="tokens" 
                        value="100" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-yellow-400 font-extrabold text-sm focus:outline-none focus:border-yellow-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Add Credits (+ to add, - to subtract)</label>
                    <input 
                        type="number" 
                        name="credits" 
                        value="0" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-emerald-400 font-extrabold text-sm focus:outline-none focus:border-emerald-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Audit / Reason Note (Optional)</label>
                    <input 
                        type="text" 
                        name="note" 
                        placeholder="e.g. Promotional bonus or manual customer support credit" 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-yellow-500"
                    >
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="userTopupModal = false" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold hover:bg-white/20">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-yellow-500 to-amber-600 text-black font-extrabold text-xs shadow-lg hover:opacity-90">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit User Exact Token & Credit Balances -->
    <div 
        x-show="userBalanceModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
        <div 
            @click.away="userBalanceModal = false" 
            class="glass-card p-8 rounded-3xl max-w-md w-full border border-blue-500/30 shadow-2xl relative"
        >
            <button @click="userBalanceModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">✕</button>
            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <span>✏️</span> Set Exact User Token Balance
            </h3>
            <p class="text-xs text-gray-400 mb-6">
                Directly edit exact token & credit balance for <span class="text-blue-400 font-bold" x-text="activeUser ? activeUser.name : ''"></span>
            </p>

            <form :action="activeUser ? '/admin/users/' + activeUser.id + '/balance' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Exact Token Count</label>
                    <input 
                        type="number" 
                        name="tokens" 
                        :value="activeUser ? activeUser.tokens : 0" 
                        required 
                        min="0"
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-yellow-400 font-extrabold text-sm focus:outline-none focus:border-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Exact Credit Count</label>
                    <input 
                        type="number" 
                        name="credits" 
                        :value="activeUser ? activeUser.credits : 0" 
                        required 
                        min="0"
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-emerald-400 font-extrabold text-sm focus:outline-none focus:border-blue-500"
                    >
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="userBalanceModal = false" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold hover:bg-white/20">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-blue-600 text-white font-extrabold text-xs shadow-lg hover:bg-blue-500">Save Exact Balance</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Edit Token Package -->
    <div 
        x-show="editPackageModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
        <div 
            @click.away="editPackageModal = false" 
            class="glass-card p-8 rounded-3xl max-w-md w-full border border-purple-500/30 shadow-2xl relative"
        >
            <button @click="editPackageModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">✕</button>
            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <span>📦</span> Edit Token Package
            </h3>
            <p class="text-xs text-gray-400 mb-6">
                Update details for package <span class="text-purple-400 font-bold" x-text="activePackage ? activePackage.name : ''"></span>
            </p>

            <form :action="activePackage ? '/admin/tokens/' + activePackage.id + '/update' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Package Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        :value="activePackage ? activePackage.name : ''"
                        required 
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-purple-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Token Quantity</label>
                    <input 
                        type="number" 
                        name="tokens" 
                        :value="activePackage ? activePackage.tokens : 0"
                        required 
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-yellow-400 font-bold text-sm focus:outline-none focus:border-purple-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Price (USD $)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="price_usd" 
                        :value="activePackage ? activePackage.price_usd : 0"
                        required 
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-emerald-400 font-bold text-sm focus:outline-none focus:border-purple-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Badge / Tag (Optional)</label>
                    <input 
                        type="text" 
                        name="badge" 
                        :value="activePackage ? activePackage.badge : ''"
                        placeholder="e.g. POPULAR, BEST VALUE"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-purple-500"
                    >
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_popular" value="1" :checked="activePackage && activePackage.is_popular" class="rounded border-gray-600 bg-white/10 text-purple-500 focus:ring-0">
                    <label class="text-xs font-semibold text-gray-300">Highlight as Popular Pack</label>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="editPackageModal = false" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold hover:bg-white/20">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-purple-600 text-white font-extrabold text-xs shadow-lg hover:bg-purple-500">Save Package Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
