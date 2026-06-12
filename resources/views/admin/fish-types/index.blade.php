@extends('layouts.admin')

@section('title', 'Master Jenis Ikan')

@section('content')
<div class="space-y-8">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 text-sm font-semibold rounded-2xl p-4 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 text-sm font-semibold rounded-2xl p-4 flex items-center space-x-2">
            <i data-lucide="x-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Master Jenis Ikan</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Daftar pustaka jenis ikan Cilacap, kategori pelagis/demersal, dan kontrol keaktifan data.</p>
        </div>
        
        <button onclick="openAddModal()" class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-500 text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all duration-200">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Jenis Ikan</span>
        </button>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-slate-500">Total data master: <span class="text-slate-800 font-bold">{{ $fishTypes->total() }} jenis ikan</span></p>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.fish-types.index') }}" method="GET" class="relative group w-full md:w-auto">
            <button type="submit" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </button>
            <input 
                type="text" 
                name="search"
                value="{{ $search }}"
                placeholder="Cari jenis ikan..." 
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
                        <th class="py-4 px-6">Nama Lokal</th>
                        <th class="py-4 px-6">Nama Ilmiah</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Ditambahkan Oleh</th>
                        <th class="py-4 px-6">Status Data</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fishTypes as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-5 px-6 font-bold text-slate-800 text-sm">
                                {{ $item->nama_lokal }}
                            </td>
                            <td class="py-5 px-6 text-sm font-semibold text-slate-600 italic">
                                {{ $item->nama_ilmiah ?? '-' }}
                            </td>
                            <td class="py-5 px-6 text-sm font-medium">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-lg {{ $item->kategori === 'Pelagis' ? 'bg-sky-50 text-sky-600 border border-sky-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-5 px-6 text-xs font-medium text-slate-500">
                                {{ $item->admin->nama_lengkap ?? 'System' }}
                            </td>
                            <td class="py-5 px-6">
                                @if($item->is_active)
                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-600 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button 
                                        onclick="openEditModal({{ json_encode($item) }})"
                                        class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-xl transition-colors"
                                        title="Ubah Data"
                                    >
                                        <i data-lucide="edit" class="w-4.5 h-4.5"></i>
                                    </button>

                                    <form action="{{ route('admin.fish-types.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data master jenis ikan ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-slate-100 rounded-xl transition-colors" title="Hapus Data">
                                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada master data jenis ikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fishTypes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $fishTypes->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Tambah Jenis Ikan -->
<div id="addModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Tambah Master Jenis Ikan</h3>
            <button onclick="closeAddModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.fish-types.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Nama Lokal</label>
                <input type="text" name="nama_lokal" required placeholder="Contoh: Tuna Sirip Kuning" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Nama Ilmiah (Opsional)</label>
                <input type="text" name="nama_ilmiah" placeholder="Contoh: Thunnus albacares" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Kategori Habitat</label>
                <select name="kategori" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                    <option value="Pelagis">Pelagis (Permukaan Air)</option>
                    <option value="Demersal">Demersal (Dasar Laut)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Status Aktif</label>
                <select name="is_active" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 rounded-2xl font-bold text-sm text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/10">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Jenis Ikan -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Ubah Master Jenis Ikan</h3>
            <button onclick="closeEditModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Nama Lokal</label>
                <input type="text" name="nama_lokal" id="edit_nama_lokal" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Nama Ilmiah (Opsional)</label>
                <input type="text" name="nama_ilmiah" id="edit_nama_ilmiah" class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Kategori Habitat</label>
                <select name="kategori" id="edit_kategori" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                    <option value="Pelagis">Pelagis (Permukaan Air)</option>
                    <option value="Demersal">Demersal (Dasar Laut)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase">Status Aktif</label>
                <select name="is_active" id="edit_is_active" required class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-sm font-medium outline-none transition-colors cursor-pointer">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 rounded-2xl font-bold text-sm text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/10">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(item) {
        document.getElementById('editForm').action = `/admin/fish-types/${item.id}`;
        document.getElementById('edit_nama_lokal').value = item.nama_lokal;
        document.getElementById('edit_nama_ilmiah').value = item.nama_ilmiah || '';
        document.getElementById('edit_kategori').value = item.kategori;
        document.getElementById('edit_is_active').value = item.is_active ? "1" : "0";

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
