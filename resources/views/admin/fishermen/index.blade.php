@extends('layouts.admin')

@section('title', 'Manajemen Nelayan')

@section('content')
<div class="space-y-8">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 text-sm font-semibold rounded-2xl p-4 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Data Nelayan</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Daftar nelayan terdaftar, riwayat pengajuan, dan status verifikasi akun.</p>
        </div>
        
        <button onclick="openAddModal()" class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-500 text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all duration-200">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Nelayan</span>
        </button>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Status Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.fishermen.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ !$status ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Semua Nelayan
            </a>
            <a href="{{ route('admin.fishermen.index', ['status' => 'pending']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Pending Verifikasi
            </a>
            <a href="{{ route('admin.fishermen.index', ['status' => 'verified']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'verified' ? 'bg-green-50 text-green-600 border border-green-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Terverifikasi
            </a>
            <a href="{{ route('admin.fishermen.index', ['status' => 'rejected']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'rejected' ? 'bg-red-50 text-red-600 border border-red-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Ditolak
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.fishermen.index') }}" method="GET" class="relative group">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <button type="submit" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </button>
            <input 
                type="text" 
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama, email, RT/RW..." 
                class="pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all w-full md:w-64 font-medium text-slate-700 placeholder-slate-400"
            >
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Nelayan</th>
                        <th class="py-4 px-6">TTL / Kontak</th>
                        <th class="py-4 px-6">Wilayah / Kelompok</th>
                        <th class="py-4 px-6">Detail Kapal</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fishermen as $f)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-5 px-6">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $f->profil->foto_profil ?? 'https://ui-avatars.com/api/?name='.urlencode($f->nama_lengkap) }}" class="w-10 h-10 rounded-full shadow-sm object-cover">
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $f->nama_lengkap }}</p>
                                        <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $f->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <p class="text-xs font-semibold text-slate-700">{{ $f->tempat_lahir }}, {{ $f->tanggal_lahir->format('d-m-Y') }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-1">{{ $f->no_telepon ?? '-' }}</p>
                            </td>
                            <td class="py-5 px-6">
                                <p class="text-xs font-bold text-slate-700">{{ $f->profil->kelompokNelayan->nama_kelompok ?? 'Tanpa Kelompok' }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-1">
                                    Desa {{ $f->profil->desa->nama ?? '-' }}, RT {{ $f->profil->rt ?? '-' }}/RW {{ $f->profil->rw ?? '-' }}
                                </p>
                            </td>
                            <td class="py-5 px-6">
                                <p class="text-xs font-semibold text-slate-700">{{ $f->profil->jenis_kapal }}</p>
                                @if($f->profil->nama_kapal)
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $f->profil->nama_kapal }} ({{ $f->profil->no_registrasi_kapal ?? '-' }})</p>
                                @endif
                            </td>
                            <td class="py-5 px-6">
                                @if($f->profil->status_verifikasi === 'verified')
                                    <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200">
                                        Terverifikasi
                                    </span>
                                @elseif($f->profil->status_verifikasi === 'pending')
                                    <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-200 animate-pulse">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @if($f->profil->status_verifikasi === 'pending')
                                        <button 
                                            onclick="openVerifyModal('{{ $f->id }}', '{{ $f->nama_lengkap }}')"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors"
                                            title="Verifikasi Akun"
                                        >
                                            <i data-lucide="check-circle" class="w-4.5 h-4.5"></i>
                                        </button>
                                    @endif

                                    <button 
                                        onclick="openEditModal({{ json_encode($f) }})"
                                        class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-xl transition-colors"
                                        title="Ubah Nelayan"
                                    >
                                        <i data-lucide="edit" class="w-4.5 h-4.5"></i>
                                    </button>

                                    <form action="{{ route('admin.fishermen.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus nelayan ini? Semua data tangkapan juga akan dihapus.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-slate-100 rounded-xl transition-colors" title="Hapus Nelayan">
                                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada nelayan terdaftar di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($fishermen->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $fishermen->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Tambah Nelayan -->
<div id="addModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Tambah Akun Nelayan Baru</h3>
            <button onclick="closeAddModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.fishermen.store') }}" method="POST" class="overflow-y-auto p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Email</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Min. 6 Karakter" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">No. Telepon</label>
                    <input type="text" name="no_telepon" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Desa Wilayah</label>
                    <select name="desa_id" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}">{{ $desa->nama }} (Kec. {{ $desa->kecamatan->nama }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Kelompok Nelayan</label>
                    <select name="kelompok_id" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        <option value="">-- Tanpa Kelompok --</option>
                        @foreach($kelompoks as $kel)
                            <option value="{{ $kel->id }}">{{ $kel->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">RT</label>
                    <input type="text" name="rt" required placeholder="Contoh: 02" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">RW</label>
                    <input type="text" name="rw" required placeholder="Contoh: 04" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Jenis Kapal</label>
                    <select name="jenis_kapal" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        <option value="Compreng">Compreng</option>
                        <option value="Jukung">Jukung</option>
                        <option value="Gilnet">Gilnet</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Nama Kapal</label>
                    <input type="text" name="nama_kapal" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">No. Registrasi Kapal</label>
                    <input type="text" name="no_registrasi_kapal" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Jenis Tangkapan Utama</label>
                    <input type="text" name="jenis_tangkapan_utama" placeholder="Contoh: Tuna, Layur" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 rounded-2xl font-bold text-sm text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/10">Simpan Nelayan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Nelayan -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Ubah Data Nelayan</h3>
            <button onclick="closeEditModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" class="overflow-y-auto p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama_lengkap" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diganti" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">No. Telepon</label>
                    <input type="text" name="no_telepon" id="edit_no_telepon" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Desa Wilayah</label>
                    <select name="desa_id" id="edit_desa_id" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}">{{ $desa->nama }} (Kec. {{ $desa->kecamatan->nama }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Kelompok Nelayan</label>
                    <select name="kelompok_id" id="edit_kelompok_id" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        <option value="">-- Tanpa Kelompok --</option>
                        @foreach($kelompoks as $kel)
                            <option value="{{ $kel->id }}">{{ $kel->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">RT</label>
                    <input type="text" name="rt" id="edit_rt" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">RW</label>
                    <input type="text" name="rw" id="edit_rw" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Jenis Kapal</label>
                    <select name="jenis_kapal" id="edit_jenis_kapal" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                        <option value="Compreng">Compreng</option>
                        <option value="Jukung">Jukung</option>
                        <option value="Gilnet">Gilnet</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Nama Kapal</label>
                    <input type="text" name="nama_kapal" id="edit_nama_kapal" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">No. Registrasi Kapal</label>
                    <input type="text" name="no_registrasi_kapal" id="edit_no_registrasi_kapal" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase">Jenis Tangkapan Utama</label>
                    <input type="text" name="jenis_tangkapan_utama" id="edit_jenis_tangkapan_utama" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 rounded-2xl font-bold text-sm text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/10">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Verifikasi Nelayan -->
<div id="verifyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Verifikasi Pengajuan Akun</h3>
            <button onclick="closeVerifyModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="verifyForm" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-2">
                <p class="text-sm font-semibold text-slate-500">Nama Nelayan:</p>
                <p class="text-base font-bold text-slate-800" id="verify_nama_nelayan"></p>
            </div>
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Keputusan Verifikasi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="border border-slate-200 rounded-2xl p-4 flex items-center justify-center space-x-2 cursor-pointer hover:bg-slate-50 select-none [&:has(input:checked)]:border-green-500 [&:has(input:checked)]:bg-green-50/50 [&:has(input:checked)]:text-green-700">
                        <input type="radio" name="status_verifikasi" value="verified" checked class="text-green-600 border-slate-300 focus:ring-green-500/20 w-4.5 h-4.5">
                        <span class="text-sm font-bold">Setujui</span>
                    </label>
                    
                    <label class="border border-slate-200 rounded-2xl p-4 flex items-center justify-center space-x-2 cursor-pointer hover:bg-slate-50 select-none [&:has(input:checked)]:border-red-500 [&:has(input:checked)]:bg-red-50/50 [&:has(input:checked)]:text-red-700">
                        <input type="radio" name="status_verifikasi" value="rejected" class="text-red-600 border-slate-300 focus:ring-red-500/20 w-4.5 h-4.5">
                        <span class="text-sm font-bold">Tolak</span>
                    </label>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Catatan / Alasan</label>
                <textarea name="catatan_verifikasi" rows="3" placeholder="Tulis alasan jika menolak pengajuan akun..." class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none resize-none transition-colors"></textarea>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeVerifyModal()" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 rounded-2xl font-bold text-sm text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/10">Proses Verifikasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal Add
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Modal Edit
    function openEditModal(nelayan) {
        document.getElementById('editForm').action = `/admin/fishermen/${nelayan.id}`;
        document.getElementById('edit_nama_lengkap').value = nelayan.nama_lengkap;
        document.getElementById('edit_email').value = nelayan.email;
        document.getElementById('edit_no_telepon').value = nelayan.no_telepon || '';
        document.getElementById('edit_tempat_lahir').value = nelayan.tempat_lahir;
        
        // Format date string YYYY-MM-DD
        if (nelayan.tanggal_lahir) {
            const dateObj = new Date(nelayan.tanggal_lahir);
            const dateStr = dateObj.toISOString().split('T')[0];
            document.getElementById('edit_tanggal_lahir').value = dateStr;
        }

        if (nelayan.profil) {
            document.getElementById('edit_desa_id').value = nelayan.profil.desa_id || '';
            document.getElementById('edit_kelompok_id').value = nelayan.profil.kelompok_id || '';
            document.getElementById('edit_rt').value = nelayan.profil.rt || '';
            document.getElementById('edit_rw').value = nelayan.profil.rw || '';
            document.getElementById('edit_jenis_kapal').value = nelayan.profil.jenis_kapal || 'Compreng';
            document.getElementById('edit_nama_kapal').value = nelayan.profil.nama_kapal || '';
            document.getElementById('edit_no_registrasi_kapal').value = nelayan.profil.no_registrasi_kapal || '';
            document.getElementById('edit_jenis_tangkapan_utama').value = nelayan.profil.jenis_tangkapan_utama || '';
        }

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Modal Verify
    function openVerifyModal(id, nama) {
        document.getElementById('verifyForm').action = `/admin/fishermen/${id}/verify`;
        document.getElementById('verify_nama_nelayan').innerText = nama;
        document.getElementById('verifyModal').classList.remove('hidden');
    }
    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('hidden');
    }
</script>
@endsection
