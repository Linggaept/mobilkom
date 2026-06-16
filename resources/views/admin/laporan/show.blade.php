@extends('layouts.app')
@section('title','Detail Laporan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-clipboard2-data me-2"></i>Detail Laporan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Laporan</a></li>
            <li class="breadcrumb-item active">{{ $laporan->nomor_laporan }}</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card mb-4">
            <div class="table-card-header">
                <h6><i class="bi bi-info-circle me-2"></i>Informasi Laporan</h6>
                <div class="d-flex gap-2 align-items-center">
                    @if($laporan->status_penyelesaian === 'on_time')
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>On-Time</span>
                    @elseif($laporan->status_penyelesaian === 'terlambat')
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat</span>
                    @endif
                    <span class="badge bg-{{ $laporan->status_badge }} fs-6">{{ $laporan->status_label }}</span>
                </div>
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
                    @if($laporan->ttd_pelapor)
                    <div class="col-12">
                        <div class="small text-muted mb-2">TTD Pelapor</div>
                        <img src="{{ $laporan->ttd_pelapor }}" class="img-fluid rounded border" style="max-height:100px;background:#fff">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($laporan->catatan_teknisi || $laporan->ttd_teknisi)
        <div class="table-card">
            <div class="table-card-header"><h6><i class="bi bi-tools me-2"></i>Laporan Teknisi</h6></div>
            <div class="p-4">
                @if($laporan->catatan_teknisi)
                <div class="mb-3">
                    <div class="small text-muted mb-1">Catatan Teknisi</div>
                    <div class="p-3 rounded-3" style="background:#f8f9ff">{{ $laporan->catatan_teknisi }}</div>
                </div>
                @endif
                @if($laporan->ttd_teknisi)
                <div class="small text-muted mb-2">TTD Teknisi</div>
                <img src="{{ $laporan->ttd_teknisi }}" class="img-fluid rounded border" style="max-height:100px;background:#fff">
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Verifikasi form -->
        @if($laporan->status === 'menunggu_verifikasi')
        <div class="table-card mb-4 border border-warning">
            <div class="table-card-header" style="background:#fff8e1">
                <h6 class="text-warning"><i class="bi bi-shield-check me-2"></i>Verifikasi Laporan</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.laporan.verifikasi', $laporan->id) }}" method="POST" id="verifForm">
                    @csrf
                    <input type="hidden" name="aksi" id="aksiInput" value="verifikasi">
                    <div class="mb-3">
                        <label class="form-label fw-700 small">Tugaskan ke Teknisi <span class="text-danger">*</span></label>
                        <select name="teknisi_id" class="form-select" required>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($teknisis as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }} ({{ $t->site }}) — {{ $t->aktif_count }} tugas aktif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Diurutkan dari yang paling sedikit tugasnya</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-700 small">Catatan Admin</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Catatan untuk teknisi..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="submitVerif('verifikasi')">
                            <i class="bi bi-check-circle-fill me-2"></i>Verifikasi & Tugaskan
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="submitVerif('tolak')">
                            <i class="bi bi-x-circle me-2"></i>Tolak Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Info admin verifikator -->
        @if($laporan->admin)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-person-check me-2"></i>Diverifikasi Oleh</h6></div>
            <div class="p-4">
                <div class="fw-700">{{ $laporan->admin->name }}</div>
                <div class="small text-muted">{{ $laporan->tanggal_verifikasi?->isoFormat('D MMMM Y HH:mm') }}</div>
                @if($laporan->catatan_admin)<div class="mt-2 p-2 rounded-3 small" style="background:#f8f9ff">{{ $laporan->catatan_admin }}</div>@endif
            </div>
        </div>
        @endif

        @if($laporan->teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-person-gear me-2"></i>Teknisi Bertugas</h6></div>
            <div class="p-4 text-center">
                <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:56px;height:56px">
                    <i class="bi bi-person-fill fs-2 text-success"></i>
                </div>
                <div class="fw-700">{{ $laporan->teknisi->name }}</div>
                <div class="small text-muted">{{ $laporan->teknisi->jabatan }}</div>
                <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $laporan->teknisi->no_hp }}</div>
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
                    @if($laporan->status_penyelesaian)
                    <tr>
                        <td class="text-muted py-1"><i class="bi bi-flag-fill me-1"></i>Status Penyelesaian</td>
                        <td class="text-end">
                            @if($laporan->status_penyelesaian === 'on_time')
                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>On-Time</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($laporan->durasi_total)
                    <tr style="border-top:1px solid #dee2e6">
                        <td class="text-muted py-1 pt-2"><i class="bi bi-hourglass-split me-1"></i>Total Durasi</td>
                        <td class="fw-700 text-end text-primary pt-2">{{ $laporan->durasi_total }}</td>
                    </tr>
                    @endif
                </table>
                @if($laporan->status_penyelesaian === 'on_time')
                <div class="mt-2 p-2 rounded-2 small bg-success-subtle text-success" style="border:1px dashed #198754">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <strong>Selesai On-Time</strong> — sebelum tenggat {{ $laporan->deadline_proses->isoFormat('D MMM HH:mm') }}
                </div>
                @elseif($laporan->status_penyelesaian === 'terlambat')
                <div class="mt-2 p-2 rounded-2 small bg-danger-subtle text-danger" style="border:1px dashed #dc3545">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Selesai Terlambat</strong> — melewati tenggat {{ $laporan->deadline_proses->isoFormat('D MMM HH:mm') }}
                </div>
                @elseif($laporan->is_overdue_verifikasi)
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

        <div class="d-grid gap-2">
            <a href="{{ route('admin.laporan.cetak', $laporan->id) }}" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-file-pdf me-2"></i>Cetak PDF
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitVerif(aksi) {
    const label = aksi === 'verifikasi' ? 'verifikasi dan tugaskan' : 'tolak';
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin ${label} laporan ini?`,
        icon: aksi === 'verifikasi' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: aksi === 'verifikasi' ? '#198754' : '#dc3545',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('aksiInput').value = aksi;
            if (aksi === 'tolak') {
                document.querySelector('[name="teknisi_id"]').removeAttribute('required');
            }
            document.getElementById('verifForm').submit();
        }
    });
}
</script>
@endpush