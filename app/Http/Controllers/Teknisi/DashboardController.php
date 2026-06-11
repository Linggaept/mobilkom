<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total'   => Laporan::where('teknisi_id', $user->id)->count(),
            'proses'  => Laporan::where('teknisi_id', $user->id)->where('status', 'sedang_proses')->count(),
            'selesai' => Laporan::where('teknisi_id', $user->id)->where('status', 'selesai')->count(),
            'menunggu'=> Laporan::where('teknisi_id', $user->id)->where('status', 'diverifikasi')->count(),
        ];

        $laporan_terbaru = Laporan::where('teknisi_id', $user->id)
            ->whereIn('status', ['diverifikasi', 'sedang_proses'])
            ->with(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor'])
            ->latest()
            ->take(5)
            ->get();

        return view('teknisi.dashboard', compact('stats', 'laporan_terbaru'));
    }
}