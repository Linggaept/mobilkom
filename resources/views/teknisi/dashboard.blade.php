@extends('layouts.app')
@section('title','Dashboard Teknisi')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-tools me-2"></i>Dashboard Teknisi</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
</div>

<div class="p-4 mb-4 rounded-4 text-white" style="background:linear-gradient(135deg,#198754,#20c997)">
    <h5 class="fw-800 mb-1">Selamat Datang, {{ auth()->user()->name }}! 🔧</h5>
    <p class="mb-0 opacity-75 small">Teknisi Radio &bull; {{ auth()->user()->site }} &bull; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<div class="row g-3 mb-4">
    @php $cards = [
        ['val'=>$stats['total'],   'label'=>'Total Tugas',     'icon'=>'bi-clipboard2-data-fill','bg'=>'#e8f5e9','color'=>'#198754'],
        ['val'=>$stats['menunggu'],'label'=>'Menunggu Proses', 'icon'=>'bi-hourglass-split',      'bg'=>'#fff3cd','color'=>'#e8a020'],
        ['val'=>$stats['proses'],  'label'=>'Sedang Proses',   'icon'=>'bi-wrench-adjustable',    'bg'=>'#cff4fc','color'=>'#0dcaf0'],
        ['val'=>$stats['selesai'], 'label'=>'Selesai',         'icon'=>'bi-check-circle-fill',    'bg'=>'#d1e7dd','color'=>'#198754'],
    ]; @endphp
    @foreach($cards as $c)
    <div class="col-6 col-md-3">
        <div class="stat-card h-100" style="background:#fff">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:{{ $c['bg'] }}">
                    <i class="bi {{ $c['icon'] }}" style="color:{{ $c['color'] }}"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:{{ $c['color'] }}">{{ $c['val'] }}</div>
                    <div class="stat-label">{{ $c['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-clock-history me-2"></i>Tugas Terbaru Menunggu Proses</h6>
        <a href="{{ route('teknisi.laporan.index') }}" class="btn btn-sm btn-outline-success rounded-pill">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Laporan</th>
                    <th>Pelapor</th>
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
                    <td><span class="fw-700 small text-success">{{ $l->nomor_laporan }}</span></td>
                    <td><small>{{ $l->pelapor->name }}</small></td>
                    <td><small>{{ $l->site->nama }}</small></td>
                    <td><small>{{ $l->tipeRadio->nama }}</small></td>
                    <td><small>{{ $l->jenisKerusakan->nama }}</small></td>
                    <td><span class="badge bg-{{ $l->status_badge }}">{{ $l->status_label }}</span></td>
                    <td>
                        <a href="{{ route('teknisi.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i> Proses
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox d-block fs-3 mb-2"></i>Tidak ada tugas menunggu
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection