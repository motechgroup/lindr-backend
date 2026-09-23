@extends('layouts.admin')

@section('title', 'Git & Deployment Manager')
@section('subtitle', 'Pull live updates from GitHub, run database migrations, and manage system deployments')

@section('content')

@if(session('error'))
    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>❌</span>
            <pre class="font-mono text-xs whitespace-pre-wrap">{{ session('error') }}</pre>
        </div>
    </div>
@endif

@if(session('deploy_log'))
    <div class="glass-card p-6 rounded-3xl border border-blue-500/30 bg-blue-950/20 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-blue-400 flex items-center gap-2">
                <span>💻</span> Deployment Execution Console Log
            </h3>
            <span class="text-xs px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-300 font-mono">Status: Finished</span>
        </div>
        <pre class="p-4 rounded-2xl bg-black/60 border border-white/10 text-emerald-400 font-mono text-xs overflow-x-auto max-h-60 leading-relaxed">{{ session('deploy_log') }}</pre>
    </div>
@endif

<!-- Repository Overview & Action Control Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Card 1: Repository Info -->
    <div class="glass-card p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <h3 class="text-sm font-bold text-purple-300 flex items-center gap-2">
                <span>📦</span> Repository Details
            </h3>
            <span class="px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-bold uppercase tracking-wider">GitHub</span>
        </div>

        <div class="space-y-3 text-xs">
            <div>
                <span class="text-gray-400 block mb-1">Git Remote URL</span>
                <a href="{{ $repoUrl }}" target="_blank" class="font-mono text-pink-400 font-semibold hover:underline block truncate">
                    {{ $repoUrl }}
                </a>
            </div>

            <div class="flex justify-between items-center py-2 border-t border-white/5">
                <span class="text-gray-400">Current Branch:</span>
                <span class="font-mono text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                    🌿 {{ $currentBranch }}
                </span>
            </div>

            <div class="flex justify-between items-center py-2 border-t border-white/5">
                <span class="text-gray-400">Latest Commit:</span>
                <span class="font-mono text-yellow-400 font-bold">
                    {{ $currentCommit['hash'] ?? 'N/A' }}
                </span>
            </div>

            @if(!empty($currentCommit))
                <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                    <div class="font-semibold text-white truncate">{{ $currentCommit['message'] }}</div>
                    <div class="text-[10px] text-gray-400 flex justify-between">
                        <span>👤 {{ $currentCommit['author'] }}</span>
                        <span>🕒 {{ $currentCommit['time'] }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Card 2: Quick Deployment Controls -->
    <div class="glass-card p-6 rounded-3xl space-y-4 lg:col-span-2 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-sm font-bold text-emerald-400 flex items-center gap-2">
                    <span>🚀</span> Deployment & Action Controls
                </h3>
                <span class="text-xs text-gray-400">Web-based zero-SSH deployment</span>
            </div>
            <p class="text-xs text-gray-300 mt-2">
                Click below to fetch code updates directly from GitHub, execute missing database migrations, and clear application caches in one click.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
            <!-- 1-Click Auto Deploy -->
            <form action="{{ route('admin.git.deploy') }}" method="POST" class="col-span-2 sm:col-span-1">
                @csrf
                <button type="submit" class="w-full h-full py-4 px-3 rounded-2xl bg-gradient-to-tr from-[#FF2D55] to-[#9C27B0] font-black text-white text-xs shadow-xl hover:opacity-95 transition flex flex-col items-center justify-center gap-1.5 border border-pink-500/30">
                    <span class="text-xl">🚀</span>
                    <span>1-Click Deploy</span>
                </button>
            </form>

            <!-- Git Pull -->
            <form action="{{ route('admin.git.pull') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-full py-4 px-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-bold text-xs hover:bg-emerald-500/30 transition flex flex-col items-center justify-center gap-1.5">
                    <span class="text-xl">⚡</span>
                    <span>Git Pull</span>
                </button>
            </form>

            <!-- Run Migrations -->
            <form action="{{ route('admin.git.migrate') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-full py-4 px-3 rounded-2xl bg-blue-500/20 border border-blue-500/30 text-blue-300 font-bold text-xs hover:bg-blue-500/30 transition flex flex-col items-center justify-center gap-1.5">
                    <span class="text-xl">🗄️</span>
                    <span>Run Migrations</span>
                </button>
            </form>

            <!-- Flush Caches -->
            <form action="{{ route('admin.git.clear-cache') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-full py-4 px-3 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-300 font-bold text-xs hover:bg-amber-500/30 transition flex flex-col items-center justify-center gap-1.5">
                    <span class="text-xl">🧹</span>
                    <span>Flush Cache</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Pending Migrations & Uncommitted Changes Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Pending Database Migrations -->
    <div class="glass-card p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <h3 class="text-sm font-bold text-yellow-400 flex items-center gap-2">
                <span>🗄️</span> Pending Database Migrations
            </h3>
            <span class="px-2.5 py-0.5 rounded-full {{ count($pendingMigrations) > 0 ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }} text-xs font-bold">
                {{ count($pendingMigrations) }} Pending
            </span>
        </div>

        @if(count($pendingMigrations) > 0)
            <div class="space-y-2">
                @foreach($pendingMigrations as $mig)
                    <div class="p-3 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-between text-xs">
                        <span class="font-mono text-yellow-200 truncate">{{ $mig }}</span>
                        <span class="text-[10px] text-yellow-400 font-bold uppercase">Ready</span>
                    </div>
                @endforeach
            </div>
            <form action="{{ route('admin.git.migrate') }}" method="POST" class="pt-2">
                @csrf
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 text-gray-950 font-black text-xs hover:opacity-95 transition shadow-lg">
                    ⚡ Execute {{ count($pendingMigrations) }} Migrations Now
                </button>
            </form>
        @else
            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 text-center text-xs text-gray-400 space-y-1">
                <div class="text-2xl">✅</div>
                <div class="font-semibold text-white">Database is Up to Date</div>
                <div>All schema migrations have been executed.</div>
            </div>
        @endif
    </div>

    <!-- Working Directory Git Status -->
    <div class="glass-card p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <h3 class="text-sm font-bold text-pink-400 flex items-center gap-2">
                <span>📁</span> Working Directory Status (`git status`)
            </h3>
            <span class="text-xs text-gray-400 font-mono">Local Changes</span>
        </div>

        @if(!empty($gitStatus))
            <pre class="p-4 rounded-2xl bg-black/50 border border-white/10 text-yellow-300 font-mono text-xs overflow-x-auto max-h-48 leading-relaxed">{{ $gitStatus }}</pre>
        @else
            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 text-center text-xs text-gray-400 space-y-1">
                <div class="text-2xl">✨</div>
                <div class="font-semibold text-white">Working Directory Clean</div>
                <div>No uncommitted local changes detected.</div>
            </div>
        @endif
    </div>
</div>

<!-- Recent Commits Log -->
<div class="glass-card p-6 rounded-3xl space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-white/10">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <span>📜</span> Recent Commit Log (GitHub Sync History)
        </h3>
        <span class="text-xs text-purple-300 font-mono">Top 12 Commits</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-gray-400 border-b border-white/10 font-bold uppercase tracking-wider text-[10px]">
                    <th class="pb-3 px-2">Commit Hash</th>
                    <th class="pb-3 px-2">Message</th>
                    <th class="pb-3 px-2">Author</th>
                    <th class="pb-3 px-2">Date / Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($recentCommits as $commit)
                    <tr class="hover:bg-white/5 transition">
                        <td class="py-3 px-2 font-mono text-pink-400 font-bold">
                            {{ $commit['hash'] }}
                        </td>
                        <td class="py-3 px-2 font-semibold text-white">
                            {{ $commit['message'] }}
                        </td>
                        <td class="py-3 px-2 text-gray-300">
                            {{ $commit['author'] }}
                        </td>
                        <td class="py-3 px-2 text-gray-400 font-mono text-[11px]">
                            {{ $commit['time'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400">No git commits found in repository history.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
