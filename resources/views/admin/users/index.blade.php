@extends('layouts.admin')

@section('title', 'Users Management')
@section('subtitle', 'Search, filter, top up tokens & credits, edit profiles, view history, and manage user accounts')

@section('content')
<!-- Filter & Search Bar -->
<div class="glass-card p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ !request('gender') ? 'bg-pink-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            All Users
        </a>
        <a href="{{ route('admin.users', ['gender' => 'male']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ request('gender') === 'male' ? 'bg-blue-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            Male Users
        </a>
        <a href="{{ route('admin.users', ['gender' => 'female']) }}" class="px-4 py-2 rounded-2xl text-xs font-bold {{ request('gender') === 'female' ? 'bg-pink-500 text-white' : 'glass-card text-gray-300 hover:text-white' }}">
            Female Creators
        </a>
    </div>

    <form action="{{ route('admin.users') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
        @if(request('gender'))
            <input type="hidden" name="gender" value="{{ request('gender') }}">
        @endif
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Search by name, email, or ID..." 
            class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500 w-full md:w-64"
        >
        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-xs">
            Search
        </button>
    </form>
</div>

<!-- Users List Table -->
<div class="glass-card p-8 rounded-3xl" x-data="{ topupModal: false, editModal: false, activeUser: null }">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">User Profile</th>
                    <th class="p-4">Gender</th>
                    <th class="p-4">Level</th>
                    <th class="p-4">Tokens</th>
                    <th class="p-4">Credits</th>
                    <th class="p-4">Verification</th>
                    <th class="p-4 text-center">Top Up / Balance</th>
                    <th class="p-4 rounded-r-xl text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $u)
                    <tr class="hover:bg-white/[0.02] transition" x-data='{ u: @json($u) }'>
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $u->avatar }}" class="w-10 h-10 rounded-full object-cover border border-white/10">
                            <div>
                                <div class="text-sm font-bold text-white flex items-center gap-1.5">
                                    {{ $u->name }}
                                    @if($u->is_verified)
                                        <span class="text-xs text-emerald-400" title="Verified Creator">✓</span>
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
                            <span class="px-2.5 py-1 rounded-xl bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-bold text-xs">
                                🪙 {{ number_format($u->tokens) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold text-xs">
                                💎 {{ number_format($u->credits) }}
                            </span>
                        </td>

                        <td class="p-4">
                            @if($u->is_verified)
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Verified</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-gray-500/20 text-gray-400 text-xs font-bold">Pending</span>
                            @endif
                        </td>

                        <!-- Quick Top Up Modal Launcher -->
                        <td class="p-4 text-center">
                            <button 
                                type="button" 
                                @click="activeUser = u; topupModal = true" 
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-yellow-500/20 to-emerald-500/20 border border-yellow-500/30 text-yellow-300 text-xs font-bold hover:brightness-125 transition inline-flex items-center gap-1"
                            >
                                <span>➕</span> Top Up
                            </button>
                        </td>

                        <!-- Action Buttons -->
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- View Account History -->
                                <a 
                                    href="{{ route('admin.users.history', $u->id) }}" 
                                    class="px-3 py-1.5 rounded-xl bg-white/10 text-gray-200 text-xs font-semibold hover:bg-white/20 transition flex items-center gap-1"
                                    title="View transaction & call history"
                                >
                                    <span>👁️</span> History
                                </a>

                                <!-- Edit Profile -->
                                <button 
                                    type="button" 
                                    @click="activeUser = u; editModal = true" 
                                    class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold hover:bg-blue-500/20 transition"
                                    title="Edit user profile"
                                >
                                    ✏️ Edit
                                </button>

                                <!-- Toggle Verification -->
                                <form action="{{ route('admin.users.verify', $u->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-white/20 text-xs font-semibold hover:border-white/40 transition">
                                        {{ $u->is_verified ? 'Unverify' : 'Verify' }}
                                    </button>
                                </form>

                                <!-- Delete Account -->
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete user {{ addslashes($u->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold hover:bg-red-500/20 transition">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

    <!-- Quick Wallet Top-Up Modal -->
    <div 
        x-show="topupModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
        <div 
            @click.away="topupModal = false" 
            class="glass-card p-8 rounded-3xl max-w-md w-full border border-yellow-500/30 shadow-2xl relative"
        >
            <button @click="topupModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">✕</button>
            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <span>🪙</span> Wallet Top-Up & Adjustment
            </h3>
            <p class="text-xs text-gray-400 mb-6">
                Grant tokens or credits directly to <span class="text-yellow-400 font-bold" x-text="activeUser ? activeUser.name : ''"></span>
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
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white font-bold text-sm focus:outline-none focus:border-yellow-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Add Credits / Earnings (+ to add, - to subtract)</label>
                    <input 
                        type="number" 
                        name="credits" 
                        value="0" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white font-bold text-sm focus:outline-none focus:border-emerald-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Audit / Reason Note (Optional)</label>
                    <input 
                        type="text" 
                        name="note" 
                        placeholder="e.g. Promotional bonus or customer support compensation" 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500"
                    >
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="topupModal = false" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold hover:bg-white/20">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-yellow-500 to-emerald-500 text-black font-extrabold text-xs shadow-lg hover:opacity-90">Confirm Top-Up</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Profile Modal -->
    <div 
        x-show="editModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
        <div 
            @click.away="editModal = false" 
            class="glass-card p-8 rounded-3xl max-w-md w-full border border-blue-500/30 shadow-2xl relative"
        >
            <button @click="editModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">✕</button>
            <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <span>✏️</span> Edit User Profile
            </h3>
            <p class="text-xs text-gray-400 mb-6">
                Update details for <span class="text-blue-400 font-bold" x-text="activeUser ? activeUser.name : ''"></span>
            </p>

            <form :action="activeUser ? '/admin/users/' + activeUser.id + '/profile' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Full Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        :value="activeUser ? activeUser.name : ''" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        :value="activeUser ? activeUser.email : ''" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Gender</label>
                    <select 
                        name="gender" 
                        :value="activeUser ? activeUser.gender : 'male'" 
                        class="w-full px-4 py-2.5 rounded-2xl bg-[#15112a] border border-white/15 text-white text-xs focus:outline-none focus:border-blue-500"
                    >
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-2xl bg-white/10 text-xs font-bold hover:bg-white/20">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-blue-600 text-white font-extrabold text-xs shadow-lg hover:bg-blue-500">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
