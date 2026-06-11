<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\{Laporan, Site, TipeRadio, JenisKerusakan};
use App\Notifications\LaporanMasukNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LaporanController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $laporan = Laporan::where('user_id', Auth::id())
            ->with(['site', 'tipeRadio', 'jenisKerusakan'])
            ->latest()
            ->paginate(10);

        return view('pelapor.laporan.index', compact('laporan'));
    }

    public function create()
    {
        $sites          = Site::where('is_active', true)->get();
        $tipeRadios     = TipeRadio::where('is_active', true)->get();
        $jenisKerusakans = JenisKerusakan::where('is_active', true)->get();
        $nomorLaporan   = Laporan::generateNomor();

        return view('pelapor.laporan.create', compact('sites', 'tipeRadios', 'jenisKerusakans', 'nomorLaporan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan_pelapor'    => 'required|string|max:100',
            'tanggal_laporan'    => 'required|date',
            'site_id'            => 'required|exists:sites,id',
            'tipe_radio_id'      => 'required|exists:tipe_radios,id',
            'jenis_kerusakan_id' => 'required|exists:jenis_kerusakans,id',
            'deskripsi_kerusakan'=> 'required|string|min:10',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'ttd_pelapor'        => 'nullable',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan/foto', 'public');
        }

        $laporan = Laporan::create([
            'nomor_laporan'       => Laporan::generateNomor(),
            'user_id'             => Auth::id(),
            'nama_pelapor'        => Auth::user()->name,
            'jabatan_pelapor'     => $request->jabatan_pelapor,
            'tanggal_laporan'     => $request->tanggal_laporan,
            'site_id'             => $request->site_id,
            'tipe_radio_id'       => $request->tipe_radio_id,
            'jenis_kerusakan_id'  => $request->jenis_kerusakan_id,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'foto'                => $fotoPath,
            'status'              => 'menunggu_verifikasi',
            'ttd_pelapor'         => $request->ttd_pelapor,
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new LaporanMasukNotification($laporan));
        }

        return redirect()->route('pelapor.laporan.index')
            ->with('success', 'Laporan berhasil dikirim dengan nomor: ' . $laporan->nomor_laporan);
    }

    public function show(Laporan $laporan)
    {
        $this->authorize('view', $laporan);
        $laporan->load(['site', 'tipeRadio', 'jenisKerusakan', 'teknisi']);
        return view('pelapor.laporan.show', compact('laporan'));
    }

    public function edit(Laporan $laporan)
    {
        $this->authorize('update', $laporan);

        if (!in_array($laporan->status, ['menunggu_verifikasi'])) {
            return redirect()->route('pelapor.laporan.show', $laporan->id)
                ->with('error', 'Laporan ini sudah diproses dan tidak dapat diedit.');
        }

        $sites           = Site::where('is_active', true)->get();
        $tipeRadios      = TipeRadio::where('is_active', true)->get();
        $jenisKerusakans = JenisKerusakan::where('is_active', true)->get();

        return view('pelapor.laporan.edit', compact('laporan', 'sites', 'tipeRadios', 'jenisKerusakans'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $this->authorize('update', $laporan);

        if (!in_array($laporan->status, ['menunggu_verifikasi'])) {
            return redirect()->route('pelapor.laporan.show', $laporan->id)
                ->with('error', 'Laporan ini sudah diproses dan tidak dapat diedit.');
        }

        $request->validate([
            'jabatan_pelapor'    => 'required|string|max:100',
            'tanggal_laporan'    => 'required|date',
            'site_id'            => 'required|exists:sites,id',
            'tipe_radio_id'      => 'required|exists:tipe_radios,id',
            'jenis_kerusakan_id' => 'required|exists:jenis_kerusakans,id',
            'deskripsi_kerusakan'=> 'required|string|min:10',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only([
            'jabatan_pelapor', 'tanggal_laporan', 'site_id',
            'tipe_radio_id', 'jenis_kerusakan_id', 'deskripsi_kerusakan',
        ]);

        if ($request->hasFile('foto')) {
            if ($laporan->foto) Storage::disk('public')->delete($laporan->foto);
            $data['foto'] = $request->file('foto')->store('laporan/foto', 'public');
        }

        $laporan->update($data);

        return redirect()->route('pelapor.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Laporan $laporan)
    {
        $this->authorize('delete', $laporan);

        if (!in_array($laporan->status, ['menunggu_verifikasi', 'ditolak'])) {
            return redirect()->route('pelapor.laporan.index')
                ->with('error', 'Laporan ini tidak dapat dihapus karena sudah diproses.');
        }

        if ($laporan->foto) Storage::disk('public')->delete($laporan->foto);
        $laporan->delete();

        return redirect()->route('pelapor.laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}