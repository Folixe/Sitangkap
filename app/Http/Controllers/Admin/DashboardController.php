<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nelayan;
use App\Models\ProfilNelayan;
use App\Models\Tangkapan;
use App\Models\DetailTangkapan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));

        // 1. Total Nelayan
        $totalNelayan = Nelayan::count();

        // 2. Pending Verification Count
        $pendingCount = ProfilNelayan::where('status_verifikasi', 'pending')->count();

        // 3. Tangkapan Bulan Ini (in Tons)
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $monthlyWeightKg = DB::table('detail_tangkapan')
            ->join('tangkapan', 'detail_tangkapan.tangkapan_id', '=', 'tangkapan.id')
            ->where('tangkapan.status', '=', 'verified')
            ->whereBetween('tangkapan.tanggal_penangkapan', [$startOfMonth, $endOfMonth])
            ->sum('detail_tangkapan.berat_kg');

        $monthlyWeightTon = round($monthlyWeightKg / 1000, 1);

        // 4. Comparison to previous month for trend percentage
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $lastMonthWeightKg = DB::table('detail_tangkapan')
            ->join('tangkapan', 'detail_tangkapan.tangkapan_id', '=', 'tangkapan.id')
            ->where('tangkapan.status', '=', 'verified')
            ->whereBetween('tangkapan.tanggal_penangkapan', [$startOfLastMonth, $endOfLastMonth])
            ->sum('detail_tangkapan.berat_kg');

        $trendPercentage = 0;
        if ($lastMonthWeightKg > 0) {
            $trendPercentage = round((($monthlyWeightKg - $lastMonthWeightKg) / $lastMonthWeightKg) * 100, 1);
        }

        // 5. Monthly Catch Trend for the selected year
        $rawMonthlyData = DB::table('detail_tangkapan')
            ->join('tangkapan', 'detail_tangkapan.tangkapan_id', '=', 'tangkapan.id')
            ->where('tangkapan.status', '=', 'verified')
            ->whereYear('tangkapan.tanggal_penangkapan', '=', $selectedYear)
            ->selectRaw('MONTH(tangkapan.tanggal_penangkapan) as month, SUM(detail_tangkapan.berat_kg) as total_weight')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill all 12 months with 0 if no catches recorded
        $chartData = array_fill(1, 12, 0);
        foreach ($rawMonthlyData as $data) {
            // Convert to Tons
            $chartData[$data->month] = round($data->total_weight / 1000, 2);
        }

        // Available years for filter dropdown
        $availableYears = DB::table('tangkapan')
            ->where('status', '=', 'verified')
            ->selectRaw('DISTINCT YEAR(tanggal_penangkapan) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears[] = date('Y');
        }

        return view('admin.dashboard', compact(
            'totalNelayan',
            'pendingCount',
            'monthlyWeightTon',
            'trendPercentage',
            'chartData',
            'selectedYear',
            'availableYears'
        ));
    }
}
