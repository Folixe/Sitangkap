@extends('layouts.admin')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <!-- Notifications -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 text-sm font-semibold rounded-2xl p-4 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 text-sm font-semibold rounded-2xl p-4 flex items-start space-x-2">
            <i data-lucide="x-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pengaturan Akun</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Ubah rincian profil Anda, kelola otentikasi, dan perbarui kata sandi masuk.</p>
    </div>

    <!-- Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 pb-6 border-b border-slate-100">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->nama_lengkap) }}&size=80&background=0ea5e9&color=fff" class="w-20 h-20 rounded-full shadow-md object-cover border-2 border-white">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg font-bold text-slate-800">{{ $admin->nama_lengkap }}</h3>
                    <p class="text-xs text-slate-400 capitalize font-semibold mt-0.5">{{ $admin->level }}</p>
                </div>
            </div>

            <!-- Profile Fields -->
            <div class="space-y-4 pt-2">
                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Rincian Informasi</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $admin->nama_lengkap) }}" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase">No. Telepon</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon', $admin->no_telepon) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase">Email Administrator</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                    </div>
                </div>
            </div>

            <!-- Password Fields -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Keamanan & Sandi</h4>
                
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase">Kata Sandi Saat Ini</label>
                        <input type="password" name="old_password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase">Kata Sandi Baru</label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-500 text-white px-6 py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all duration-200">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
