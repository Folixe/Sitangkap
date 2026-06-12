@extends('layouts.admin')

@section('title', 'Audit Log Aktivitas')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Audit Log Aktivitas</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Rekam jejak tindakan administratif, perubahan data nelayan, dan verifikasi tangkapan.</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-slate-500">Menampilkan rekaman audit terbaru</p>
        </div>

        <form action="{{ route('admin.logs.index') }}" method="GET" class="relative group w-full md:w-auto">
            <button type="submit" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </button>
            <input 
                type="text" 
                name="search"
                value="{{ $search }}"
                placeholder="Cari aktivitas, nama admin..." 
                class="pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all w-full md:w-64 font-medium text-slate-700 placeholder-slate-400"
            >
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Waktu Tindakan</th>
                        <th class="py-4 px-6">Administrator</th>
                        <th class="py-4 px-6">Aktivitas</th>
                        <th class="py-4 px-6">Tabel Target</th>
                        <th class="py-4 px-6">IP Address</th>
                        <th class="py-4 px-6 text-right">Data Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4.5 px-6 text-xs font-semibold text-slate-500">
                                {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB
                            </td>
                            <td class="py-4.5 px-6">
                                @if($log->admin)
                                    <div class="flex items-center space-x-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->admin->nama_lengkap) }}&size=28&background=0ea5e9&color=fff" class="w-6 h-6 rounded-full">
                                        <div>
                                            <p class="text-sm font-bold text-slate-700">{{ $log->admin->nama_lengkap }}</p>
                                            <p class="text-[10px] font-medium text-slate-400">{{ $log->admin->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-slate-400">Sistem / guest</span>
                                @endif
                            </td>
                            <td class="py-4.5 px-6 text-sm font-semibold text-slate-700">
                                {{ $log->aksi }}
                            </td>
                            <td class="py-4.5 px-6 text-xs font-semibold text-slate-500 capitalize">
                                {{ $log->tabel_target ?? '-' }}
                            </td>
                            <td class="py-4.5 px-6 text-xs font-medium text-slate-400">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="py-4.5 px-6 text-right">
                                @if($log->data_sebelum || $log->data_sesudah)
                                    <button 
                                        onclick="openDetailModal({{ json_encode($log->data_sebelum) }}, {{ json_encode($log->data_sesudah) }}, '{{ $log->aksi }}')"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-100/50 transition-colors"
                                    >
                                        Detail Perubahan
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium italic">Tidak ada perubahan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada riwayat aktivitas log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Detail Perubahan JSON -->
<div id="jsonModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Detail Perubahan Data</h3>
                <p class="text-xs font-medium text-slate-400 mt-0.5" id="json_modal_action"></p>
            </div>
            <button onclick="closeDetailModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="overflow-y-auto p-6 space-y-6 bg-slate-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Sebelum -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-red-500 uppercase flex items-center">
                        <i data-lucide="minus-circle" class="w-4 h-4 mr-1"></i>
                        <span>Sebelum Perubahan</span>
                    </p>
                    <pre id="json_before" class="bg-slate-900 text-rose-300 font-mono text-xs p-4 rounded-2xl overflow-x-auto border border-slate-800 max-h-[300px]"></pre>
                </div>
                
                <!-- Data Sesudah -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-green-600 uppercase flex items-center">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-1"></i>
                        <span>Sesudah Perubahan</span>
                    </p>
                    <pre id="json_after" class="bg-slate-900 text-emerald-300 font-mono text-xs p-4 rounded-2xl overflow-x-auto border border-slate-800 max-h-[300px]"></pre>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
            <button onclick="closeDetailModal()" class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 rounded-xl font-bold text-sm text-slate-600">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openDetailModal(before, after, actionTitle) {
        document.getElementById('json_modal_action').innerText = actionTitle;
        
        const beforeEl = document.getElementById('json_before');
        const afterEl = document.getElementById('json_after');

        beforeEl.innerText = before ? JSON.stringify(before, null, 2) : '// Tidak ada data sebelumnya (Insert)';
        afterEl.innerText = after ? JSON.stringify(after, null, 2) : '// Tidak ada data sesudah (Delete)';

        document.getElementById('jsonModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('jsonModal').classList.add('hidden');
    }
</script>
@endsection
