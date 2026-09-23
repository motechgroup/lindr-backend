@extends('layouts.admin')

@section('title', 'Token Packages Manager')
@section('subtitle', 'Create, edit, toggle, and configure token pricing packages for mobile app users')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- 1. Create New Package Form -->
    <div class="glass-card p-6 rounded-3xl h-fit">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <span>🪙</span> Create Token Package
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
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Token Quantity</label>
                <input 
                    type="number" 
                    name="tokens" 
                    required 
                    placeholder="e.g. 500"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500"
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
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Badge / Tag (Optional)</label>
                <input 
                    type="text" 
                    name="badge" 
                    placeholder="e.g. POPULAR, BEST VALUE"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/15 text-white text-xs focus:outline-none focus:border-pink-500"
                >
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_popular" value="1" class="rounded border-gray-600 bg-white/10 text-pink-500 focus:ring-0">
                <label class="text-xs font-semibold text-gray-300">Highlight as Popular Pack</label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-white text-xs shadow-lg hover:opacity-95 transition">
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
                        <th class="p-4 rounded-l-xl">Package</th>
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
                                    <span class="ml-2 px-2 py-0.5 rounded-full bg-pink-500/20 text-pink-400 text-[10px] uppercase font-extrabold">Popular</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-yellow-400">{{ number_format($pkg->tokens) }} Tokens</td>
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
                                <form action="{{ route('admin.tokens.toggle', $pkg->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-xl bg-white/10 text-xs font-semibold hover:bg-white/20 transition">
                                        {{ $pkg->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

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
@endsection
