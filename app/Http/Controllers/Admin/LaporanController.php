<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, User, Site, TipeRadio, JenisKerusakan};
use App\Notifications\{StatusLaporanNotification, TugasTeknisiNotification};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $teknisis = User::where('role', 'teknisi')->where('is_active', true)->get();

        return view('admin.laporan.index', compact('laporan', 'sites', 'tipeRadios', 'jenisKerusakans', 'teknisis'));
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor', 'teknisi', 'admin']);

        $teknisis = User::where('role', 'teknisi')
            ->where('is_active', true)
            ->withCount(['laporanSebagaiTeknisi as aktif_count' => function ($q) {
                $q->whereIn('status', ['diverifikasi', 'sedang_proses']);
            }])
            ->orderBy('aktif_count')
            ->orderBy('name')
            ->get();

        return view('admin.laporan.show', compact('laporan', 'teknisis'));
    }

    public function verifikasi(Request $request, Laporan $laporan)
    {
        $request->validate([
            'aksi' => 'required|in:verifikasi,tolak',
            'teknisi_id' => 'required_if:aksi,verifikasi|nullable|exists:users,id',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if ($request->aksi === 'verifikasi') {
            $laporan->update([
                'status' => 'diverifikasi',
                'admin_id' => Auth::id(),
                'teknisi_id' => $request->teknisi_id,
                'catatan_admin' => $request->catatan_admin,
                'tanggal_verifikasi' => now(),
            ]);

            // Notify pelapor
            $laporan->pelapor->notify(new StatusLaporanNotification($laporan, 'Laporan Anda telah diverifikasi dan sedang ditugaskan ke teknisi.'));

            // Notify teknisi
            $teknisi = User::find($request->teknisi_id);
            if ($teknisi) {
                $teknisi->notify(new TugasTeknisiNotification($laporan));
            }

            return redirect()->route('admin.laporan.show', $laporan->id)->with('success', 'Laporan berhasil diverifikasi dan ditugaskan ke teknisi.');
        }

        // Tolak
        $laporan->update([
            'status' => 'ditolak',
            'admin_id' => Auth::id(),
            'catatan_admin' => $request->catatan_admin,
            'tanggal_verifikasi' => now(),
        ]);

        $laporan->pelapor->notify(new StatusLaporanNotification($laporan, 'Laporan Anda telah ditolak. Alasan: ' . ($request->catatan_admin ?? '-')));

        return redirect()->route('admin.laporan.show', $laporan->id)->with('success', 'Laporan berhasil ditolak.');
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
