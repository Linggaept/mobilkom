<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, Site, TipeRadio, JenisKerusakan};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LaporanController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Laporan::with(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor', 'teknisi'])->filter($request->only(['tanggal_mulai', 'tanggal_akhir', 'site_id', 'tipe_radio_id', 'jenis_kerusakan_id', 'status', 'search']));

        $laporan = $query->latest()->paginate(15)->withQueryString();

        $sites = Site::where('is_active', true)->get();
        $tipeRadios = TipeRadio::where('is_active', true)->get();
        $jenisKerusakans = JenisKerusakan::where('is_active', true)->get();

        return view('pimpinan.laporan.index', compact('laporan', 'sites', 'tipeRadios', 'jenisKerusakans'));
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor', 'teknisi', 'admin']);
        return view('pimpinan.laporan.show', compact('laporan'));
    }

    public function cetakSatu(Laporan $laporan)
    {
        $laporan->load(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor', 'teknisi', 'admin']);
        $pdf = Pdf::loadView('pdf.laporan_single', compact('laporan'))->setPaper('a4', 'portrait');
        return $pdf->stream('laporan-' . str_replace('/', '-', $laporan->nomor_laporan) . '.pdf');
    }

    public function cetakBulk(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:laporan,id']);

        $laporanList = Laporan::whereIn('id', $request->ids)
            ->with(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor', 'teknisi', 'admin'])
            ->get();

        $pdf = Pdf::loadView('pdf.laporan_bulk', compact('laporanList'))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-bulk-' . now()->format('YmdHis') . '.pdf');
    }
}
