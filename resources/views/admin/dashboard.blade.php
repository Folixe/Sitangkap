@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Top Greeting Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Ringkasan Sistem</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Selamat datang kembali, Admin! Pantau terus statistik nelayan Cilacap hari ini.</p>
        </div>
        
        <!-- Year filter -->
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center space-x-3 bg-white px-4 py-2 border border-slate-200 rounded-2xl shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Grafik:</span>
            <select name="year" onchange="this.form.submit()" class="text-sm font-bold text-slate-700 bg-transparent outline-none cursor-pointer pr-4 focus:ring-0">
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ $yr == $selectedYear ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Nelayan Terdaftar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="space-y-2">
                <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Nelayan</p>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $totalNelayan }}</h3>
                <span class="text-xs text-blue-500 font-bold bg-blue-50 px-2.5 py-1 rounded-full inline-block">Terdaftar di sistem</span>
            </div>
            <div class="bg-blue-50 text-blue-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                <i data-lucide="users" class="w-8 h-8"></i>
            </div>
        </div>

        <!-- Card 2: Pengajuan Pending -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="space-y-2">
                <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Verifikasi Akun</p>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $pendingCount }}</h3>
                @if($pendingCount > 0)
                    <span class="text-xs text-orange-500 font-bold bg-orange-50 px-2.5 py-1 rounded-full inline-block animate-pulse">Menunggu verifikasi</span>
                @else
                    <span class="text-xs text-green-500 font-bold bg-green-50 px-2.5 py-1 rounded-full inline-block">Semua sudah terverifikasi</span>
                @endif
            </div>
            <div class="p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300 {{ $pendingCount > 0 ? 'bg-orange-50 text-orange-500' : 'bg-green-50 text-green-600' }}">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
        </div>

        <!-- Card 3: Volume Catch Bulan Ini -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="space-y-2">
                <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Hasil Tangkapan (Bulan Ini)</p>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $monthlyWeightTon }} <span class="text-lg font-bold text-slate-500">Ton</span></h3>
                @if($trendPercentage >= 0)
                    <span class="text-xs text-green-500 font-bold bg-green-50 px-2.5 py-1 rounded-full inline-flex items-center space-x-1">
                        <i data-lucide="trending-up" class="w-3.5 h-3.5 mr-0.5"></i>
                        <span>+{{ $trendPercentage }}% dari bln lalu</span>
                    </span>
                @else
                    <span class="text-xs text-red-500 font-bold bg-red-50 px-2.5 py-1 rounded-full inline-flex items-center space-x-1">
                        <i data-lucide="trending-down" class="w-3.5 h-3.5 mr-0.5"></i>
                        <span>{{ $trendPercentage }}% dari bln lalu</span>
                    </span>
                @endif
            </div>
            <div class="bg-indigo-50 text-indigo-600 p-4 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                <i data-lucide="anchor" class="w-8 h-8"></i>
            </div>
        </div>
    </div>

    <!-- Chart & Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line Chart -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-lg font-bold text-slate-800">Tren Hasil Tangkapan</h4>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Grafik total berat hasil tangkapan nelayan dalam satuan ton</p>
                </div>
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-500 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Tahun {{ $selectedYear }}</span>
                </div>
            </div>
            <div class="relative h-[320px]">
                <canvas id="catchTrendChart"></canvas>
            </div>
        </div>

        <!-- Catch Info Panel (e.g. Jenis Ikan tangkapan terbanyak) -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-800 mb-1">Kategori Ikan Terpopuler</h4>
                <p class="text-xs font-semibold text-slate-400 mb-6">Distribusi tangkapan paling sering dilaporkan</p>
                
                @php
                    $popularFish = DB::table('detail_tangkapan')
                        ->join('jenis_ikan', 'detail_tangkapan.jenis_ikan_id', '=', 'jenis_ikan.id')
                        ->join('tangkapan', 'detail_tangkapan.tangkapan_id', '=', 'tangkapan.id')
                        ->where('tangkapan.status', '=', 'verified')
                        ->selectRaw('jenis_ikan.nama_lokal, SUM(detail_tangkapan.berat_kg) as total_weight')
                        ->groupBy('jenis_ikan.nama_lokal')
                        ->orderBy('total_weight', 'desc')
                        ->take(4)
                        ->get();
                    $totalWeightAll = DB::table('detail_tangkapan')
                        ->join('tangkapan', 'detail_tangkapan.tangkapan_id', '=', 'tangkapan.id')
                        ->where('tangkapan.status', '=', 'verified')
                        ->sum('berat_kg');
                @endphp

                <div class="space-y-4">
                    @forelse($popularFish as $fish)
                        @php
                            $percentage = $totalWeightAll > 0 ? round(($fish->total_weight / $totalWeightAll) * 100) : 0;
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-sm font-bold text-slate-700">
                                <span>{{ $fish->nama_lokal }}</span>
                                <span>{{ round($fish->total_weight / 1000, 1) }} Ton ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm font-semibold text-slate-400 text-center py-8">Belum ada data tangkapan.</p>
                    @endforelse
                </div>
            </div>
            
            <a href="{{ route('admin.catches.index') }}" class="w-full flex items-center justify-center space-x-2 bg-slate-50 hover:bg-slate-100 text-slate-700 py-3 rounded-2xl font-bold text-xs border border-slate-200 mt-6 transition-all duration-200">
                <span>Kelola Riwayat Tangkapan</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    <!-- Recent Catches Table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Laporan Tangkapan Terbaru</h4>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Catatan pengajuan hasil tangkapan nelayan terakhir</p>
            </div>
            <a href="{{ route('admin.catches.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-xl transition-all">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Nelayan</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Jenis Kapal</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Berat (Kg)</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Cuaca</th>
                        <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $recentCatches = \App\Models\Tangkapan::with(['nelayan.profil', 'details'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentCatches as $catch)
                        <tr>
                            <td class="py-3.5 flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($catch->nelayan->nama_lengkap) }}&background=f1f5f9&color=0f172a" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm font-bold text-slate-700">{{ $catch->nelayan->nama_lengkap }}</p>
                                    <p class="text-xs font-medium text-slate-400">{{ $catch->nelayan->profil->kelompokNelayan->nama_kelompok ?? 'Tanpa Kelompok' }}</p>
                                </div>
                            </td>
                            <td class="py-3.5 text-sm font-semibold text-slate-600">
                                {{ $catch->tanggal_penangkapan->format('d-m-Y') }}
                            </td>
                            <td class="py-3.5 text-sm font-medium text-slate-500">
                                {{ $catch->nelayan->profil->jenis_kapal ?? '-' }}
                            </td>
                            <td class="py-3.5 text-sm font-bold text-slate-700">
                                {{ number_format($catch->details->sum('berat_kg'), 1) }}
                            </td>
                            <td class="py-3.5 text-sm font-medium text-slate-500">
                                {{ $catch->kondisi_cuaca }}
                            </td>
                            <td class="py-3.5">
                                @if($catch->status === 'verified')
                                    <span class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200">
                                        Terverifikasi
                                    </span>
                                @elseif($catch->status === 'pending')
                                    <span class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm font-semibold text-slate-400">
                                Belum ada laporan tangkapan masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('catchTrendChart').getContext('2d');
        
        // Grab chart data from PHP variable
        const rawData = @json(array_values($chartData));
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

        // Create Chart gradient background
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.45)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Berat Tangkapan (Ton)',
                    data: rawData,
                    borderColor: '#2563eb', // Blue-600
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: {
                            family: 'Plus Jakarta Sans',
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Plus Jakarta Sans',
                            size: 13
                        },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ` Catch Volume: ${context.parsed.y} Ton`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 12,
                                weight: '500'
                            },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: '#e2e8f0',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 12,
                                weight: '500'
                            },
                            color: '#94a3b8',
                            callback: function(value) {
                                return value + ' Ton';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
