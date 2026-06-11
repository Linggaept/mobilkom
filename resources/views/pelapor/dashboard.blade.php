@extends('layouts.app')
@section('title','Dashboard Pelapor')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-house-door-fill me-2"></i>Dashboard</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol>
        </nav>
    </div>
    <a href="{{ route('pelapor.laporan.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Buat Laporan
    </a>
</div>

<!-- Greeting -->
<div class="p-4 mb-4 rounded-4 text-white" style="background:linear-gradient(135deg,#0d3b8e,#1a5fbf)">
    <h5 class="fw-800 mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h5>
    <p class="mb-0 opacity-75 small">{{ auth()->user()->site }} &bull; {{ auth()->user()->jabatan }} &bull; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100" style="background:#fff">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f0fe">
                    <i class="bi bi-clipboard2-fill" style="color:#0d3b8e"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#0d3b8e">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100" style="background:#fff">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff3cd">
                    <i class="bi bi-hourglass-split" style="color:#e8a020"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#e8a020">{{ $stats['menunggu'] }}</div>
                    <div class="stat-label">Menunggu / Verifikasi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100" style="background:#fff">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#cff4fc">
                    <i class="bi bi-tools" style="color:#0dcaf0"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#0dcaf0">{{ $stats['proses'] }}</div>
                    <div class="stat-label">Sedang Proses</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100" style="background:#fff">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d1e7dd">
                    <i class="bi bi-check-circle-fill" style="color:#198754"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:#198754">{{ $stats['selesai'] }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent reports table -->
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-clock-history me-2"></i>Laporan Terbaru</h6>
        <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Laporan</th>
                    <th>Tgl Laporan</th>
                    <th>Site</th>
                    <th>Tipe Radio</th>
                    <th>Kerusakan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan_terbaru as $l)
                <tr>
                    <td><span class="fw-700 small">{{ $l->nomor_laporan }}</span></td>
                    <td><small>{{ $l->tanggal_laporan->format('d/m/Y') }}</small></td>
                    <td><small>{{ $l->site->nama }}</small></td>
                    <td><small>{{ $l->tipeRadio->nama }}</small></td>
                    <td><small>{{ $l->jenisKerusakan->nama }}</small></td>
                    <td>
                        <span class="badge bg-{{ $l->status_badge }}">{{ $l->status_label }}</span>
                    </td>
                    <td>
                        <a href="{{ route('pelapor.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-info py-0 px-2">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox d-block fs-3 mb-2"></i>Belum ada laporan
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection