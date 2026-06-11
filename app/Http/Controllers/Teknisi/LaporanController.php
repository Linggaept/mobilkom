<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, Site, TipeRadio, JenisKerusakan};
use App\Notifications\StatusLaporanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LaporanController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Laporan::where('teknisi_id', Auth::id())
            ->with(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor'])
            ->filter($request->only(['tanggal_mulai', 'tanggal_akhir', 'site_id', 'tipe_radio_id', 'jenis_kerusakan_id', 'status', 'search']));

        $laporan = $query->latest()->paginate(10)->withQueryString();

        $sites           = Site::where('is_active', true)->get();
        $tipeRadios      = TipeRadio::where('is_active', true)->get();
        $jenisKerusakans = JenisKerusakan::where('is_active', true)->get();

        return view('teknisi.laporan.index', compact('laporan', 'sites', 'tipeRadios', 'jenisKerusakans'));
    }

    public function show(Laporan $laporan)
    {
        if ($laporan->teknisi_id !== Auth::id()) abort(403);
        $laporan->load(['site', 'tipeRadio', 'jenisKerusakan', 'pelapor']);
        return view('teknisi.laporan.show', compact('laporan'));
    }

    public function updateStatus(Request $request, Laporan $laporan)
    {
        if ($laporan->teknisi_id !== Auth::id()) abort(403);

        $request->validate([
            'status'          => 'required|in:sedang_proses,selesai',
            'catatan_teknisi' => 'nullable|string|max:500',
            'ttd_teknisi'     => 'nullable|string',
        ]);

        $data = [
            'status'          => $request->status,
            'catatan_teknisi' => $request->catatan_teknisi,
        ];

        if ($request->status === 'selesai') {
            $data['tanggal_selesai'] = now();
            if ($request->ttd_teknisi) {
                $data['ttd_teknisi'] = $request->ttd_teknisi;
            }
        }

        $laporan->update($data);

        // Notify pelapor
        $pesan = match($request->status) {
            'sedang_proses' => 'Laporan Anda sedang dalam proses perbaikan oleh teknisi.',
            'selesai'       => 'Laporan Anda telah selesai diperbaiki.',
            default         => 'Status laporan Anda telah diperbarui.',
        };

        $laporan->pelapor->notify(new StatusLaporanNotification($laporan, $pesan));

        return redirect()->route('teknisi.laporan.show', $laporan->id)
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}