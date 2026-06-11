<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total'         => Laporan::where('user_id', $user->id)->count(),
            'menunggu'      => Laporan::where('user_id', $user->id)->whereIn('status', ['menunggu_verifikasi', 'diverifikasi'])->count(),
            'proses'        => Laporan::where('user_id', $user->id)->where('status', 'sedang_proses')->count(),
            'selesai'       => Laporan::where('user_id', $user->id)->where('status', 'selesai')->count(),
        ];

        $laporan_terbaru = Laporan::where('user_id', $user->id)
            ->with(['site', 'tipeRadio', 'jenisKerusakan'])
            ->latest()
            ->take(5)
            ->get();

        return view('pelapor.dashboard', compact('stats', 'laporan_terbaru'));
    }
}