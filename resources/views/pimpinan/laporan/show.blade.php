@extends('layouts.app')
@section('title','Detail Laporan')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-clipboard2-data me-2"></i>Detail Laporan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pimpinan.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pimpinan.laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">{{ $laporan->nomor_laporan }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pimpinan.laporan.cetak', $laporan->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4">
        <i class="bi bi-file-pdf me-2"></i>Cetak PDF
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="bi bi-info-circle me-2"></i>Informasi Laporan</h6>
                <span class="badge bg-{{ $laporan->status_badge }} fs-6">{{ $laporan->status_label }}</span>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6"><div class="small text-muted">Nomor Laporan</div><div class="fw-700 text-primary">{{ $laporan->nomor_laporan }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Tanggal</div><div class="fw-600">{{ $laporan->tanggal_laporan->isoFormat('D MMMM Y') }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Pelapor</div><div class="fw-600">{{ $laporan->nama_pelapor }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Jabatan</div><div class="fw-600">{{ $laporan->jabatan_pelapor }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Site</div><div class="fw-600">{{ $laporan->site->nama }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Tipe Radio</div><div class="fw-600">{{ $laporan->tipeRadio->nama }}</div></div>
                    <div class="col-12"><div class="small text-muted">Jenis Kerusakan</div><div class="fw-600">{{ $laporan->jenisKerusakan->nama }}</div></div>
                    <div class="col-12">
                        <div class="small text-muted mb-1">Deskripsi</div>
                        <div class="p-3 rounded-3" style="background:#f8f9ff;border:1px solid #e8eaf0">{{ $laporan->deskripsi_kerusakan }}</div>
                    </div>
                    @if($laporan->foto)
                    <div class="col-12">
                        <div class="small text-muted mb-2">Foto</div>
                        <img src="{{ Storage::url($laporan->foto) }}" class="img-fluid rounded-3" style="max-height:260px">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if($laporan->teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-person-gear me-2"></i>Teknisi Bertugas</h6></div>
            <div class="p-4 text-center">
                <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:56px;height:56px">
                    <i class="bi bi-person-fill fs-2 text-success"></i>
                </div>
                <div class="fw-700">{{ $laporan->teknisi->name }}</div>
                <div class="small text-muted">{{ $laporan->teknisi->jabatan }}</div>
                @if($laporan->tanggal_selesai)
                <div class="mt-2 badge bg-success">Selesai: {{ $laporan->tanggal_selesai->isoFormat('D MMM Y') }}</div>
                @endif
            </div>
        </div>
        @endif

        @if($laporan->catatan_admin || $laporan->catatan_teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-chat-left-text me-2"></i>Catatan</h6></div>
            <div class="p-4">
                @if($laporan->catatan_admin)<div class="mb-3"><div class="small fw-700 text-muted mb-1">Admin:</div><div class="small">{{ $laporan->catatan_admin }}</div></div>@endif
                @if($laporan->catatan_teknisi)<div><div class="small fw-700 text-muted mb-1">Teknisi:</div><div class="small">{{ $laporan->catatan_teknisi }}</div></div>@endif
            </div>
        </div>
        @endif

        <!-- TTD -->
        @if($laporan->ttd_pelapor || $laporan->ttd_teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-pen me-2"></i>Tanda Tangan</h6></div>
            <div class="p-4">
                @if($laporan->ttd_pelapor)
                <div class="mb-3">
                    <div class="small text-muted mb-1">Pelapor:</div>
                    <img src="{{ $laporan->ttd_pelapor }}" class="img-fluid rounded border" style="max-height:90px;background:#fff">
                </div>
                @endif
                @if($laporan->ttd_teknisi)
                <div>
                    <div class="small text-muted mb-1">Teknisi:</div>
                    <img src="{{ $laporan->ttd_teknisi }}" class="img-fluid rounded border" style="max-height:90px;background:#fff">
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Ringkasan Tenggat --}}
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-calendar3 me-2"></i>Ringkasan Tenggat</h6></div>
            <div class="p-3">
                <table class="w-100" style="font-size:13px">
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-send me-1"></i>Dikirim</td>
                        <td class="fw-600 text-end">{{ $laporan->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-alarm me-1"></i>Tenggat Verif.</td>
                        <td class="fw-600 text-end {{ $laporan->is_overdue_verifikasi ? 'text-danger' : '' }}">
                            {{ $laporan->deadline_verifikasi->isoFormat('D MMM Y, HH:mm') }}
                        </td>
                    </tr>
                    @if($laporan->tanggal_verifikasi)
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-shield-check me-1"></i>Diverifikasi</td>
                        <td class="fw-600 text-end text-info">{{ $laporan->tanggal_verifikasi->isoFormat('D MMM Y, HH:mm') }}</td>
                    </tr>
                    @endif
                    @if($laporan->deadline_proses)
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-alarm me-1"></i>Tenggat Proses</td>
                        <td class="fw-600 text-end {{ $laporan->is_overdue_proses ? 'text-danger' : '' }}">
                            {{ $laporan->deadline_proses->isoFormat('D MMM Y, HH:mm') }}
                        </td>
                    </tr>
                    @endif
                    @if($laporan->tanggal_selesai)
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-check-circle me-1"></i>Selesai</td>
                        <td class="fw-600 text-end text-success">{{ $laporan->tanggal_selesai->isoFormat('D MMM Y, HH:mm') }}</td>
                    </tr>
                    @endif
                    @if($laporan->durasi_total)
                    <tr style="border-top:1px solid #dee2e6">
                        <td class="text-muted py-1 pt-2"><i class="bi bi-hourglass-split me-1"></i>Total Durasi</td>
                        <td class="fw-700 text-end text-primary pt-2">{{ $laporan->durasi_total }}</td>
                    </tr>
                    @endif
                </table>
                @if($laporan->is_overdue_verifikasi)
                <div class="mt-2 p-2 rounded-2 small bg-danger-subtle text-danger" style="border:1px dashed #dc3545">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Tenggat verifikasi telah lewat!</strong>
                    <div class="mt-1">{{ $laporan->sisa_waktu_verifikasi }}</div>
                </div>
                @elseif($laporan->is_overdue_proses)
                <div class="mt-2 p-2 rounded-2 small bg-danger-subtle text-danger" style="border:1px dashed #dc3545">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Tenggat proses telah lewat!</strong>
                    <div class="mt-1">{{ $laporan->sisa_waktu_proses }}</div>
                </div>
                @elseif($laporan->status === 'menunggu_verifikasi')
                <div class="mt-2 p-2 rounded-2 small bg-warning-subtle text-warning-emphasis" style="border:1px dashed #ffc107">
                    <i class="bi bi-alarm me-1"></i>
                    Sisa waktu verifikasi: <strong>{{ $laporan->sisa_waktu_verifikasi }}</strong>
                </div>
                @elseif(in_array($laporan->status, ['diverifikasi','sedang_proses']) && $laporan->deadline_proses)
                <div class="mt-2 p-2 rounded-2 small bg-warning-subtle text-warning-emphasis" style="border:1px dashed #ffc107">
                    <i class="bi bi-alarm me-1"></i>
                    Sisa waktu pengerjaan: <strong>{{ $laporan->sisa_waktu_proses }}</strong>
                </div>
                @endif
            </div>
        </div>

        <a href="{{ route('pimpinan.laporan.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>
@endsection