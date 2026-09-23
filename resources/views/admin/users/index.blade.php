@extends('layouts.admin')

@section('title', 'Users Management')
@section('subtitle', 'Search, filter, edit token/credit balances, and verify mobile app accounts')

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
            placeholder="Search by name or email..." 
            class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500 w-full md:w-64"
        >
        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-xs">
            Search
        </button>
    </form>
</div>

<!-- Users List Table -->
<div class="glass-card p-8 rounded-3xl">
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
                    <th class="p-4">Edit Balance</th>
                    <th class="p-4 rounded-r-xl text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $u)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-bold text-white flex items-center gap-3">
                            <img src="{{ $u->avatar }}" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <div class="text-sm font-bold text-white">{{ $u->name }}</div>
                                <div class="text-xs font-mono text-gray-400">{{ $u->email }}</div>
                            </div>
                        </td>
                        <td class="p-4 uppercase font-bold text-xs {{ $u->gender === 'female' ? 'text-pink-400' : 'text-blue-400' }}">
                            {{ $u->gender }}
                        </td>
                        <td class="p-4 font-bold text-xs text-yellow-400">Level {{ $u->level }}</td>
                        
                        <!-- Form to Edit Balance -->
                        <form action="{{ route('admin.users.balance', $u->id) }}" method="POST">
                            @csrf
                            <td class="p-4">
                                <input type="number" name="tokens" value="{{ $u->tokens }}" class="w-20 px-2 py-1 rounded-xl bg-white/10 text-yellow-400 font-bold text-xs text-center border border-white/10">
                            </td>
                            <td class="p-4">
                                <input type="number" name="credits" value="{{ $u->credits }}" class="w-20 px-2 py-1 rounded-xl bg-white/10 text-emerald-400 font-bold text-xs text-center border border-white/10">
                            </td>
                            <td class="p-4">
                                @if($u->is_verified)
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Verified</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-gray-500/20 text-gray-400 text-xs font-bold">Pending</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <button type="submit" class="px-3 py-1 rounded-xl bg-white/10 text-xs font-semibold hover:bg-white/20 transition">Save</button>
                            </td>
                        </form>

                        <td class="p-4 text-right">
                            <form action="{{ route('admin.users.verify', $u->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-full border border-white/20 text-xs font-semibold hover:border-white/40 transition">
                                    {{ $u->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
