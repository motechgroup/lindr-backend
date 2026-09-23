<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Access - Lindr</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0B0819;
            color: #FFFFFF;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <!-- Brand Badge -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#FF2D55] to-[#9C27B0] items-center justify-center text-3xl font-black mb-4 shadow-xl">
                L
            </div>
            <h1 class="text-2xl font-black tracking-wider text-white">Lindr Backend</h1>
            <p class="text-gray-400 text-xs font-semibold mt-1">Administrative Control Center (/access)</p>
        </div>

        <!-- Login Form Container -->
        <div class="glass-card p-8 rounded-3xl shadow-2xl">
            <div class="flex items-center gap-2 mb-6 pb-4 border-b border-white/10 text-pink-500 font-bold text-sm">
                <span>🔐</span> Authorized Personnel Only
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Admin Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', 'admin@lindr.app') }}"
                        required
                        class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-white text-sm focus:outline-none focus:border-pink-500 transition placeholder-gray-500"
                        placeholder="admin@lindr.app"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Security Key / Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/15 text-white text-sm focus:outline-none focus:border-pink-500 transition placeholder-gray-500"
                        placeholder="••••••••••••"
                    >
                </div>

                <div class="flex items-center justify-between text-xs text-gray-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-600 bg-white/10 text-pink-500 focus:ring-0">
                        <span>Keep Session Active</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] font-bold text-white text-sm shadow-xl hover:opacity-95 transition"
                >
                    Authenticate Admin Access
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="/" class="text-xs text-gray-500 hover:text-gray-300 transition">← Return to Public Product Landing Page</a>
        </div>
    </div>

</body>
</html>
