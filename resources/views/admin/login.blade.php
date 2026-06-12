<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SITANGKAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-900 via-slate-950 to-indigo-950 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo & Branding -->
        <div class="flex flex-col items-center mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3.5 rounded-2xl shadow-lg shadow-blue-500/25 mb-4 border border-blue-400/30">
                <i data-lucide="anchor" class="text-white w-8 h-8"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">SITANGKAP</h1>
            <p class="text-slate-400 text-sm font-medium mt-2">Dinas Perikanan Kabupaten Cilacap</p>
        </div>

        <!-- Glassmorphism Card -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-6">Masuk Administrator</h2>

                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl p-4 mb-6 flex items-start space-x-2">
                        <i data-lucide="x-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="font-semibold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="text-xs font-bold text-slate-300 uppercase tracking-wider">Email Utama</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                placeholder="nama@cilacap.go.id" 
                                class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-2xl pl-12 pr-4 py-3.5 text-sm font-medium text-white placeholder-slate-500 outline-none transition-all"
                            >
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label for="password" class="text-xs font-bold text-slate-300 uppercase tracking-wider">Kata Sandi</label>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                required 
                                placeholder="••••••••" 
                                class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-2xl pl-12 pr-4 py-3.5 text-sm font-medium text-white placeholder-slate-500 outline-none transition-all"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="rounded border-slate-700 bg-white/5 text-blue-600 focus:ring-blue-500/20 focus:ring-offset-slate-900 w-4.5 h-4.5 mr-2"
                            >
                            <span class="text-xs font-semibold text-slate-300">Ingat Saya</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all duration-200"
                    >
                        Masuk Sekarang
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-8 font-medium">
            &copy; 2026 Dinas Kelautan dan Perikanan Kab. Cilacap. All rights reserved.
        </p>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
