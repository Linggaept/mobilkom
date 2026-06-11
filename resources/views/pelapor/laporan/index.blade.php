@extends('layouts.app')
@section('title','Daftar Laporan')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-file-earmark-text-fill me-2"></i>Laporan Saya</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pelapor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pelapor.laporan.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Buat Laporan
    </a>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-list-ul me-2"></i>Semua Laporan</h6>
        <small class="text-muted">Total: {{ $laporan->total() }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Laporan</th>
                    <th>Tanggal</th>
                    <th>Site</th>
                    <th>Tipe Radio</th>
                    <th>Jenis Kerusakan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $l)
                <tr>
                    <td><span class="fw-700 small text-primary">{{ $l->nomor_laporan }}</span></td>
                    <td><small>{{ $l->tanggal_laporan->format('d M Y') }}</small></td>
                    <td><small>{{ $l->site->nama }}</small></td>
                    <td><small>{{ $l->tipeRadio->nama }}</small></td>
                    <td><small>{{ $l->jenisKerusakan->nama }}</small></td>
                    <td><span class="badge bg-{{ $l->status_badge }}">{{ $l->status_label }}</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('pelapor.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($l->status === 'menunggu_verifikasi')
                            <a href="{{ route('pelapor.laporan.edit', $l->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="confirmDelete('del-{{ $l->id }}','Hapus laporan {{ $l->nomor_laporan }}?')"
                                    class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="del-{{ $l->id }}" action="{{ route('pelapor.laporan.destroy', $l->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox d-block fs-1 mb-2 opacity-50"></i>
                    Belum ada laporan. <a href="{{ route('pelapor.laporan.create') }}">Buat laporan pertama</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($laporan->hasPages())
    <div class="p-3 border-top">
        {{ $laporan->links() }}
    </div>
    @endif
</div>
@endsection