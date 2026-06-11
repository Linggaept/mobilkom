@extends('layouts.app')
@section('title','Monitoring Laporan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-graph-up me-2"></i>Monitoring Laporan</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('pimpinan.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Laporan</li></ol></nav>
</div>

<div class="table-card mb-4">
    <div class="table-card-header"><h6><i class="bi bi-funnel me-2"></i>Filter & Pencarian</h6></div>
    <div class="p-3">
        <form method="GET" class="row g-2">
            <div class="col-md-2"><input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}"></div>
            <div class="col-md-2"><input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}"></div>
            <div class="col-md-2">
                <select name="site_id" class="form-select form-select-sm">
                    <option value="">Semua Site</option>
                    @foreach($sites as $s)<option value="{{ $s->id }}" {{ request('site_id')==$s->id?'selected':'' }}>{{ $s->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipe_radio_id" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($tipeRadios as $t)<option value="{{ $t->id }}" {{ request('tipe_radio_id')==$t->id?'selected':'' }}>{{ $t->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="jenis_kerusakan_id" class="form-select form-select-sm">
                    <option value="">Semua Kerusakan</option>
                    @foreach($jenisKerusakans as $j)<option value="{{ $j->id }}" {{ request('jenis_kerusakan_id')==$j->id?'selected':'' }}>{{ $j->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu_verifikasi'=>'Menunggu','diverifikasi'=>'Diverifikasi','sedang_proses'=>'Sedang Proses','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}"></div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('pimpinan.laporan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                <button type="button" class="btn btn-success btn-sm" onclick="cetakBulk()"><i class="bi bi-printer me-1"></i>Cetak Terpilih</button>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="checkAll" class="form-check-input mt-0" onchange="toggleAll(this)">
            <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Laporan</h6>
        </div>
        <small class="text-muted">Total: {{ $laporan->total() }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th style="width:36px"></th><th>No. Laporan</th><th>Pelapor</th><th>Site</th><th>Tipe Radio</th><th>Kerusakan</th><th>Tanggal</th><th>Teknisi</th><th>Status</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($laporan as $l)
                <tr>
                    <td><input type="checkbox" class="form-check-input row-check mt-0" value="{{ $l->id }}"></td>
                    <td><span class="fw-700 small text-primary">{{ $l->nomor_laporan }}</span></td>
                    <td><small>{{ $l->pelapor->name }}</small></td>
                    <td><small>{{ $l->site->nama }}</small></td>
                    <td><small>{{ $l->tipeRadio->nama }}</small></td>
                    <td><small>{{ $l->jenisKerusakan->nama }}</small></td>
                    <td><small>{{ $l->tanggal_laporan->format('d/m/Y') }}</small></td>
                    <td><small>{{ $l->teknisi?->name ?? '-' }}</small></td>
                    <td>
                        <span class="badge bg-{{ $l->status_badge }}">{{ $l->status_label }}</span>
                        @if($l->is_overdue_verifikasi || $l->is_overdue_proses)
                        <span class="badge bg-danger ms-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Overdue</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('pimpinan.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('pimpinan.laporan.cetak', $l->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-file-pdf"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4 text-muted"><i class="bi bi-inbox d-block fs-3 mb-2"></i>Tidak ada laporan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($laporan->hasPages())
    <div class="p-3 border-top">{{ $laporan->links() }}</div>
    @endif
</div>

<form id="bulkPrintForm" action="{{ route('pimpinan.laporan.cetakBulk') }}" method="POST" target="_blank" class="d-none">
    @csrf <div id="bulkIds"></div>
</form>
@endsection

@push('scripts')
<script>
function toggleAll(cb) { document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked); }
function cetakBulk() {
    const ids = [...document.querySelectorAll('.row-check:checked')].map(c => c.value);
    if (!ids.length) { Swal.fire('Perhatian','Pilih minimal satu laporan','warning'); return; }
    document.getElementById('bulkIds').innerHTML = ids.map(id=>`<input type="hidden" name="ids[]" value="${id}">`).join('');
    document.getElementById('bulkPrintForm').submit();
}
</script>
@endpush