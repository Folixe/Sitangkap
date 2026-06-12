@extends('layouts.admin')

@section('title', 'Riwayat Tangkapan')

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
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Riwayat Tangkapan</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Daftar laporan tangkapan nelayan Cilacap, verifikasi foto tangkapan, dan data operasional melaut.</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Status Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.catches.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ !$status ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Semua Tangkapan
            </a>
            <a href="{{ route('admin.catches.index', ['status' => 'pending']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Pending Verifikasi
            </a>
            <a href="{{ route('admin.catches.index', ['status' => 'verified']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'verified' ? 'bg-green-50 text-green-600 border border-green-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Terverifikasi
            </a>
            <a href="{{ route('admin.catches.index', ['status' => 'rejected']) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $status === 'rejected' ? 'bg-red-50 text-red-600 border border-red-100' : 'text-slate-500 hover:bg-slate-50 border border-transparent' }}">
                Ditolak
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.catches.index') }}" method="GET" class="relative group">
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
                placeholder="Cari nama nelayan..." 
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
                        <th class="py-4 px-6">Tanggal Tangkapan</th>
                        <th class="py-4 px-6">Jenis Ikan</th>
                        <th class="py-4 px-6">Total Berat</th>
                        <th class="py-4 px-6">Kondisi Cuaca</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($catches as $c)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-5 px-6">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($c->nelayan->nama_lengkap) }}&background=f1f5f9&color=0f172a" class="w-10 h-10 rounded-full shadow-sm">
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $c->nelayan->nama_lengkap }}</p>
                                        <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $c->nelayan->profil->kelompokNelayan->nama_kelompok ?? 'Tanpa Kelompok' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 text-sm font-semibold text-slate-700">
                                {{ $c->tanggal_penangkapan->format('d-m-Y') }}
                            </td>
                            <td class="py-5 px-6 text-xs font-medium text-slate-500 max-w-[200px] truncate">
                                {{ implode(', ', $c->details->pluck('jenisIkan.nama_lokal')->toArray()) }}
                            </td>
                            <td class="py-5 px-6 text-sm font-bold text-slate-800">
                                {{ number_format($c->details->sum('berat_kg'), 1) }} Kg
                            </td>
                            <td class="py-5 px-6 text-sm font-medium text-slate-500">
                                {{ $c->kondisi_cuaca ?? '-' }}
                            </td>
                            <td class="py-5 px-6">
                                @if($c->status === 'verified')
                                    <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200">
                                        Terverifikasi
                                    </span>
                                @elseif($c->status === 'pending')
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
                                <button 
                                    onclick="openDetailModal('{{ $c->id }}')"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center"
                                >
                                    <span>Tinjau</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada laporan tangkapan nelayan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($catches->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $catches->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Tinjau Detail Tangkapan -->
<div id="detailModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Detail Hasil Tangkapan Nelayan</h3>
            <button onclick="closeDetailModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="overflow-y-auto p-6 flex flex-col lg:flex-row gap-8">
            <!-- Left Side: Capture Info -->
            <div class="flex-1 space-y-6">
                <!-- Fisherman Info Banner -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex items-center space-x-3">
                    <div class="bg-blue-500 text-white p-2.5 rounded-xl">
                        <i data-lucide="anchor" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Pelapor:</p>
                        <p class="text-base font-bold text-slate-800" id="detail_nama_nelayan"></p>
                        <p class="text-xs font-medium text-slate-500" id="detail_kelompok_nelayan"></p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 border border-slate-150 rounded-2xl">
                        <p class="text-xs font-bold text-slate-400 uppercase">Tanggal Melaut</p>
                        <p class="text-sm font-bold text-slate-800 mt-1" id="detail_tanggal"></p>
                    </div>
                    <div class="bg-slate-50 p-4 border border-slate-150 rounded-2xl">
                        <p class="text-xs font-bold text-slate-400 uppercase">Kondisi Cuaca</p>
                        <p class="text-sm font-bold text-slate-800 mt-1" id="detail_cuaca"></p>
                    </div>
                    <div class="bg-slate-50 p-4 border border-slate-150 rounded-2xl col-span-2">
                        <p class="text-xs font-bold text-slate-400 uppercase">Lokasi Penangkapan</p>
                        <p class="text-sm font-bold text-slate-800 mt-1" id="detail_lokasi"></p>
                        <p class="text-[10px] text-slate-400 mt-0.5" id="detail_coordinates"></p>
                    </div>
                </div>

                <!-- Fish Breakdown Table -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Rincian Jenis Ikan</h4>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-4">Spesies</th>
                                    <th class="py-2.5 px-4">Berat (Kg)</th>
                                    <th class="py-2.5 px-4">Jumlah (Ekor)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700" id="detail_fish_rows">
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200 text-sm font-bold text-slate-800">
                                <tr>
                                    <td class="py-3 px-4">Total Hasil</td>
                                    <td class="py-3 px-4 text-blue-600" id="detail_total_weight"></td>
                                    <td class="py-3 px-4" id="detail_total_ekor"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <p class="text-xs font-bold text-slate-500 uppercase">Keterangan Nelayan</p>
                    <p class="text-sm font-medium text-slate-700 bg-slate-50 rounded-2xl p-4 border border-slate-100" id="detail_keterangan"></p>
                </div>
            </div>

            <!-- Right Side: Photo Proof & Verification Controls -->
            <div class="w-full lg:w-[360px] space-y-6">
                <!-- Photo proof -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Bukti Foto Hasil Tangkapan</h4>
                    <div class="relative aspect-video lg:aspect-square bg-slate-100 border border-slate-200 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center">
                        <img id="detail_catch_photo" src="" alt="Catch Proof" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Admin Action Box -->
                <div class="border border-slate-200 rounded-3xl p-5" id="detail_action_container">
                    <h4 class="text-sm font-bold text-slate-800 mb-4">Aksi Verifikasi Laporan</h4>
                    
                    <form id="verifyCatchForm" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Keputusan</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="border border-slate-200 rounded-xl py-2.5 flex items-center justify-center space-x-1.5 cursor-pointer hover:bg-slate-50 select-none [&:has(input:checked)]:border-green-500 [&:has(input:checked)]:bg-green-50 [&:has(input:checked)]:text-green-700">
                                    <input type="radio" name="status" value="verified" checked class="text-green-600 border-slate-300 focus:ring-green-500/20 w-4 h-4">
                                    <span class="text-xs font-bold">Setujui</span>
                                </label>
                                
                                <label class="border border-slate-200 rounded-xl py-2.5 flex items-center justify-center space-x-1.5 cursor-pointer hover:bg-slate-50 select-none [&:has(input:checked)]:border-red-500 [&:has(input:checked)]:bg-red-50 [&:has(input:checked)]:text-red-700">
                                    <input type="radio" name="status" value="rejected" class="text-red-600 border-slate-300 focus:ring-red-500/20 w-4 h-4">
                                    <span class="text-xs font-bold">Tolak</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Alasan / Catatan Verifikasi</label>
                            <textarea name="keterangan" rows="2" placeholder="Tulis alasan jika menolak laporan ini..." class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-2xl px-4 py-3 text-xs font-medium outline-none resize-none transition-colors"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-2xl font-bold text-xs shadow-md shadow-blue-500/10 transition-colors">
                            Submit Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openDetailModal(catchId) {
        // Fetch catch details via AJAX
        fetch(`/admin/catches/${catchId}`)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    const data = res.data;
                    
                    // Fill text fields
                    document.getElementById('detail_nama_nelayan').innerText = data.nelayan_nama;
                    document.getElementById('detail_kelompok_nelayan').innerText = data.kelompok;
                    document.getElementById('detail_tanggal').innerText = data.tanggal;
                    document.getElementById('detail_cuaca').innerText = data.cuaca;
                    document.getElementById('detail_lokasi').innerText = data.lokasi;
                    document.getElementById('detail_coordinates').innerText = `Lat: ${data.lat || '-'} | Lng: ${data.lng || '-'}`;
                    document.getElementById('detail_keterangan').innerText = data.keterangan;

                    // Fill fish rows
                    const tableBody = document.getElementById('detail_fish_rows');
                    tableBody.innerHTML = '';
                    data.details.forEach(fish => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="py-2.5 px-4 font-bold text-slate-800">${fish.nama_ikan}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-600">${parseFloat(fish.berat).toFixed(1)} Kg</td>
                            <td class="py-2.5 px-4 text-slate-500">${fish.jumlah} Ekor</td>
                        `;
                        tableBody.appendChild(tr);
                    });

                    // Set totals
                    document.getElementById('detail_total_weight').innerText = `${parseFloat(data.total_weight).toFixed(1)} Kg`;
                    document.getElementById('detail_total_ekor').innerText = `${data.total_ekor} Ekor`;

                    // Set Image
                    const photoElement = document.getElementById('detail_catch_photo');
                    if (data.fotos && data.fotos.length > 0) {
                        // We use unsplash fish sample for aesthetic seeding fallback
                        photoElement.src = 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=800';
                    } else {
                        photoElement.src = 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=800';
                    }

                    // Setup verifikasi action form path
                    const actionContainer = document.getElementById('detail_action_container');
                    if (data.status === 'pending') {
                        actionContainer.classList.remove('hidden');
                        document.getElementById('verifyCatchForm').action = `/admin/catches/${data.id}/verify`;
                    } else {
                        actionContainer.classList.add('hidden');
                    }

                    // Open Modal
                    document.getElementById('detailModal').classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error("Gagal memuat rincian tangkapan:", err);
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endsection
