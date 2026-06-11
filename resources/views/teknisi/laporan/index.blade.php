@extends('layouts.app')
@section('title','Laporan Tugas')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-tools me-2"></i>Laporan Tugas Saya</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('teknisi.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Laporan</li></ol></nav>
</div>

<!-- Filter -->
<div class="table-card mb-4">
    <div class="table-card-header"><h6><i class="bi bi-funnel me-2"></i>Filter & Pencarian</h6></div>
    <div class="p-3">
        <form method="GET" class="row g-2">
            <div class="col-md-2"><input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}" placeholder="Dari Tanggal"></div>
            <div class="col-md-2"><input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}"></div>
            <div class="col-md-2">
                <select name="site_id" class="form-select form-select-sm">
                    <option value="">Semua Site</option>
                    @foreach($sites as $s)<option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected':'' }}>{{ $s->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="diverifikasi" {{ request('status')=='diverifikasi'?'selected':'' }}>Diverifikasi</option>
                    <option value="sedang_proses" {{ request('status')=='sedang_proses'?'selected':'' }}>Sedang Proses</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}"></div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('teknisi.laporan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-list-ul me-2"></i>Daftar Laporan</h6>
        <small class="text-muted">Total: {{ $laporan->total() }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>No. Laporan</th><th>Pelapor</th><th>Site</th><th>Tipe Radio</th><th>Kerusakan</th><th>Tgl Laporan</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($laporan as $l)
                <tr>
                    <td><span class="fw-700 small text-success">{{ $l->nomor_laporan }}</span></td>
                    <td><small>{{ $l->pelapor->name }}</small></td>
                    <td><small>{{ $l->site->nama }}</small></td>
                    <td><small>{{ $l->tipeRadio->nama }}</small></td>
                    <td><small>{{ $l->jenisKerusakan->nama }}</small></td>
                    <td><small>{{ $l->tanggal_laporan->format('d/m/Y') }}</small></td>
                    <td>
                        @if(in_array($l->status, ['diverifikasi','sedang_proses']) && $l->deadline_proses)
                        <small class="{{ $l->is_overdue_proses ? 'text-danger fw-700' : 'text-muted' }}">
                            {{ $l->deadline_proses->format('d/m/Y HH:mm') }}
                            @if($l->is_overdue_proses)
                            <span class="badge bg-danger ms-1">Overdue</span>
                            @endif
                        </small>
                        @else
                        <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $l->status_badge }}">{{ $l->status_label }}</span></td>
                    <td>
                        <a href="{{ route('teknisi.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-inbox d-block fs-3 mb-2"></i>Tidak ada laporan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($laporan->hasPages())
    <div class="p-3 border-top">{{ $laporan->links() }}</div>
    @endif
</div>
@endsection