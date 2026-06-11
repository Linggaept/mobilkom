<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, User};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Laporan::count(),
            'menunggu' => Laporan::whereIn('status', ['menunggu_verifikasi', 'diverifikasi'])->count(),
            'proses'   => Laporan::where('status', 'sedang_proses')->count(),
            'selesai'  => Laporan::where('status', 'selesai')->count(),
        ];

        // Chart: laporan per bulan tahun ini
        $chartBulan = Laporan::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $chartLabels = $namaBulan;
        $chartData   = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $chartBulan->get($i)->total ?? 0;
        }

        $statusData = Laporan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->get()->pluck('total', 'status');

        $siteData = Laporan::select('site_id', DB::raw('COUNT(*) as total'))
            ->with('site')
            ->groupBy('site_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('pimpinan.dashboard', compact('stats', 'chartLabels', 'chartData', 'statusData', 'siteData'));
    }
}