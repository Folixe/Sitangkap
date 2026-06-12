<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SITANGKAP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800 h-screen flex overflow-hidden">
 
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r border-slate-200 transition-all duration-300 flex flex-col fixed md:relative z-30 h-full shadow-sm">
        <!-- Logo Branding -->
        <div class="p-6 flex items-center space-x-3 h-20 border-b border-slate-100 flex-shrink-0">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-2 rounded-xl shadow-inner border border-blue-400/20">
                <i data-lucide="anchor" class="text-white w-6 h-6"></i>
            </div>
            <span class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-cyan-500 tracking-tight">
                SITANGKAP
            </span>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex-1 px-4 space-y-1.5 mt-6 overflow-y-auto">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
            
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="layout-dashboard" class="{{ Route::is('admin.dashboard') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Dashboard</span>
                </div>
            </a>

            <a href="{{ route('admin.fishermen.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.fishermen.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="users" class="{{ Route::is('admin.fishermen.*') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Data Nelayan</span>
                </div>
                @php
                    $pendingCount = \App\Models\ProfilNelayan::where('status_verifikasi', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ Route::is('admin.fishermen.*') ? 'bg-white text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.catches.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.catches.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="fish" class="{{ Route::is('admin.catches.*') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Riwayat Tangkapan</span>
                </div>
                @php
                    $pendingCatches = \App\Models\Tangkapan::where('status', 'pending')->count();
                @endphp
                @if($pendingCatches > 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ Route::is('admin.catches.*') ? 'bg-white text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                        {{ $pendingCatches }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.fish-types.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.fish-types.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="grid" class="{{ Route::is('admin.fish-types.*') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Jenis Ikan</span>
                </div>
            </a>

            <a href="{{ route('admin.logs.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.logs.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="scroll" class="{{ Route::is('admin.logs.*') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Audit Log</span>
                </div>
            </a>
            
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mt-8 mb-3">Sistem</p>
            <a href="{{ route('admin.settings.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin.settings.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-600' }}">
                <div class="flex items-center space-x-3">
                    <i data-lucide="settings" class="{{ Route::is('admin.settings.*') ? 'text-white' : 'text-gray-400' }} w-5 h-5"></i>
                    <span class="font-semibold text-sm">Pengaturan</span>
                </div>
            </a>
        </nav>

        <!-- Admin Info Box -->
        <div class="p-4 m-4 bg-slate-50 rounded-2xl border border-slate-200 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->nama_lengkap) }}&background=0ea5e9&color=fff" alt="Admin" class="w-10 h-10 rounded-full shadow-sm" />
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-slate-700 truncate">{{ Auth::guard('admin')->user()->nama_lengkap }}</p>
                    <p class="text-xs text-slate-500 font-medium capitalize">{{ Auth::guard('admin')->user()->level }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <!-- Header -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 z-20 flex-shrink-0 shadow-sm">
            <div class="flex items-center">
                <!-- Sidebar Toggle Button -->
                <button onclick="toggleSidebar()" class="p-2 mr-4 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                
                <!-- Search bar -->
                <form action="{{ route('admin.fishermen.index') }}" method="GET" class="relative hidden md:block group">
                    <button type="submit" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari data nelayan..." 
                        class="pl-11 pr-4 py-2.5 bg-slate-100 border border-transparent rounded-full text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all w-72 shadow-sm font-medium text-slate-700 placeholder-slate-400"
                    >
                </form>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="relative p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if($pendingCount > 0)
                            <span class="absolute top-1.5 right-2 w-2 h-2 bg-red-500 rounded-full border border-white animate-pulse"></span>
                        @endif
                    </button>
                </div>
                
                <div class="h-8 w-px bg-slate-200 mx-2"></div>
                
                <!-- User Profile Dropdown -->
                <div class="relative" id="user-menu-container">
                    <button onclick="toggleUserMenu()" class="flex items-center space-x-2 cursor-pointer hover:bg-slate-50 p-2 rounded-xl transition-colors outline-none">
                        <span class="text-sm font-bold text-slate-600 hidden sm:block">Admin</span>
                        <i data-lucide="chevron-down" class="text-slate-400 w-4 h-4"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 animate-in fade-in">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Aktivitas</p>
                            <p class="text-sm font-bold text-slate-700 mt-1 truncate">{{ Auth::guard('admin')->user()->email }}</p>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 font-medium">
                            <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                            <span>Pengaturan</span>
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="border-t border-slate-100 mt-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-bold text-left">
                                <i data-lucide="log-out" class="w-4 h-4 text-red-400"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Content Body -->
        <div class="flex-1 overflow-y-auto p-6 lg:p-8 bg-slate-50/50">
            @yield('content')
        </div>
    </main>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Toggle Sidebar function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-0', 'md:w-0', 'overflow-hidden');
            } else {
                sidebar.classList.remove('w-0', 'md:w-0', 'overflow-hidden');
                sidebar.classList.add('w-64');
            }
        }

        // Toggle User Menu Dropdown
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-menu');
            if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
