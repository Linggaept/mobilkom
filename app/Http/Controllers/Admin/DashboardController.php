<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, User, Site, TipeRadio, JenisKerusakan};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Laporan::count(),
            'menunggu' => Laporan::whereIn('status', ['menunggu_verifikasi'])->count(),
            'proses'   => Laporan::whereIn('status', ['diverifikasi', 'sedang_proses'])->count(),
            'selesai'  => Laporan::where('status', 'selesai')->count(),
            'ditolak'  => Laporan::where('status', 'ditolak')->count(),
            'pelapor'  => User::where('role', 'pelapor')->count(),
            'teknisi'  => User::where('role', 'teknisi')->count(),
        ];

        // Chart: laporan per bulan (12 bulan terakhir)
        $chartBulan = Laporan::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('YEAR(created_at) as tahun'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('tahun', 'bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $chartLabels = [];
        $chartData   = [];
        $namaBulan   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = $namaBulan[$i - 1];
            $chartData[]   = $chartBulan->get($i)->total ?? 0;
        }

        // Chart: laporan per status
        $statusData = Laporan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Chart: laporan per site (top 5)
        $siteData = Laporan::select('site_id', DB::raw('COUNT(*) as total'))
            ->with('site')
            ->groupBy('site_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $laporan_terbaru = Laporan::with(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor'])
            ->where('status', 'menunggu_verifikasi')
            ->latest()
            ->take(5)
            ->get();

        // Workload per teknisi: aktif, selesai (on-time/terlambat), ditolak terlibat
        $deadlineHours = Laporan::DEADLINE_PROSES_JAM;
        $teknisiWorkload = User::where('role', 'teknisi')
            ->where('is_active', true)
            ->leftJoin('laporan', function ($join) {
                $join->on('laporan.teknisi_id', '=', 'users.id')
                     ->whereNull('laporan.deleted_at');
            })
            ->selectRaw('
                users.id, users.name, users.site, users.jabatan,
                COUNT(CASE WHEN laporan.status IN ("diverifikasi","sedang_proses") THEN 1 END) AS aktif,
                COUNT(CASE WHEN laporan.status = "selesai" THEN 1 END) AS selesai_total,
                COUNT(CASE WHEN laporan.status = "selesai"
                    AND laporan.tanggal_selesai <= DATE_ADD(laporan.tanggal_verifikasi, INTERVAL ? HOUR)
                    THEN 1 END) AS ontime,
                COUNT(CASE WHEN laporan.status = "selesai"
                    AND laporan.tanggal_selesai > DATE_ADD(laporan.tanggal_verifikasi, INTERVAL ? HOUR)
                    THEN 1 END) AS terlambat
            ', [$deadlineHours, $deadlineHours])
            ->groupBy('users.id', 'users.name', 'users.site', 'users.jabatan')
            ->orderByDesc('aktif')
            ->orderByDesc('selesai_total')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'chartLabels', 'chartData', 'statusData', 'siteData', 'laporan_terbaru', 'teknisiWorkload'
        ));
    }
}