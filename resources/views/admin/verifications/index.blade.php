@extends('layouts.admin')

@section('title', 'Biometric Verifications Queue')
@section('subtitle', 'Audit 4-step biometric liveness verification requests (head shake, nod, mouth open, blink)')

@section('content')
<div class="glass-card p-8 rounded-3xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs uppercase bg-white/5 text-gray-400 font-bold">
                <tr>
                    <th class="p-4 rounded-l-xl">User Profile</th>
                    <th class="p-4">Gender</th>
                    <th class="p-4">Verification Steps</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 rounded-r-xl text-right">Action</th>
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
                        <td class="p-4 text-xs font-semibold text-gray-300">
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 mr-1">Shake</span>
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 mr-1">Nod</span>
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 mr-1">Open Mouth</span>
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Blink</span>
                        </td>
                        <td class="p-4">
                            @if($u->is_verified)
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">Verified ✅</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-bold">Pending Moderation</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @if(!$u->is_verified)
                                <form action="{{ route('admin.verifications.approve', $u->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold hover:bg-emerald-500/30 transition">
                                        Approve Verification
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500 font-semibold">Verified</span>
                            @endif
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
