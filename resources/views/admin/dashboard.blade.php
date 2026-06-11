@extends('layouts.app')
@section('title','Dashboard Admin')

@push('styles')
<style>.chart-box{background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.07)}</style>
@endpush

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-clipboard2-data me-1"></i>Kelola Laporan
    </a>
</div>

<div class="p-4 mb-4 rounded-4 text-white" style="background:linear-gradient(135deg,#0d3b8e,#e8a020)">
    <h5 class="fw-800 mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h5>
    <p class="mb-0 opacity-75 small">Administrator &bull; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<!-- Stats Row 1 -->
<div class="row g-3 mb-4">
    @php $cards = [
        ['val'=>$stats['total'],   'label'=>'Total Laporan',   'icon'=>'bi-clipboard2-fill',      'bg'=>'#e8f0fe','color'=>'#0d3b8e'],
        ['val'=>$stats['menunggu'],'label'=>'Menunggu Verif.', 'icon'=>'bi-hourglass-split',       'bg'=>'#fff3cd','color'=>'#e8a020'],
        ['val'=>$stats['proses'],  'label'=>'Sedang Proses',   'icon'=>'bi-tools',                 'bg'=>'#cff4fc','color'=>'#0dcaf0'],
        ['val'=>$stats['selesai'], 'label'=>'Selesai',         'icon'=>'bi-check-circle-fill',     'bg'=>'#d1e7dd','color'=>'#198754'],
        ['val'=>$stats['ditolak'], 'label'=>'Ditolak',         'icon'=>'bi-x-circle-fill',         'bg'=>'#f8d7da','color'=>'#dc3545'],
        ['val'=>$stats['pelapor'], 'label'=>'Jumlah Pelapor',  'icon'=>'bi-people-fill',           'bg'=>'#f0e6ff','color'=>'#6f42c1'],
        ['val'=>$stats['teknisi'], 'label'=>'Jumlah Teknisi',  'icon'=>'bi-person-gear',           'bg'=>'#fce8f3','color'=>'#e91e8c'],
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

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-box">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-700 mb-0" style="color:#0d3b8e"><i class="bi bi-bar-chart-line me-2"></i>Laporan per Bulan ({{ date('Y') }})</h6>
            </div>
            <canvas id="chartBulan" height="110"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-box">
            <h6 class="fw-700 mb-3" style="color:#0d3b8e"><i class="bi bi-pie-chart me-2"></i>Status Laporan</h6>
            <canvas id="chartStatus" height="180"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="chart-box">
            <h6 class="fw-700 mb-3" style="color:#0d3b8e"><i class="bi bi-geo-alt me-2"></i>Top 5 Site Laporan</h6>
            <canvas id="chartSite" height="140"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <!-- Laporan masuk terbaru -->
        <div class="table-card h-100">
            <div class="table-card-header">
                <h6><i class="bi bi-bell-fill me-2 text-warning"></i>Laporan Masuk (Perlu Verifikasi)</h6>
                <a href="{{ route('admin.laporan.index', ['status'=>'menunggu_verifikasi']) }}" class="btn btn-sm btn-outline-warning rounded-pill">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>No. Laporan</th><th>Pelapor</th><th>Site</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($laporan_terbaru as $l)
                        <tr>
                            <td class="small fw-700">{{ $l->nomor_laporan }}</td>
                            <td class="small">{{ $l->pelapor->name }}</td>
                            <td class="small">{{ $l->site->nama }}</td>
                            <td>
                                <a href="{{ route('admin.laporan.show', $l->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted small">Tidak ada laporan baru</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const colorPrimary = '#0d3b8e';
const colorAccent  = '#e8a020';

// Chart Bulan
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Jumlah Laporan',
            data: @json($chartData),
            backgroundColor: 'rgba(13,59,142,0.75)',
            borderColor: colorPrimary,
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
    }
});

// Chart Status (Doughnut)
const statusLabels = ['Menunggu', 'Diverifikasi', 'Sedang Proses', 'Selesai', 'Ditolak'];
const statusKeys   = ['menunggu_verifikasi', 'diverifikasi', 'sedang_proses', 'selesai', 'ditolak'];
const statusColors = ['#ffc107','#0dcaf0','#0d6efd','#198754','#dc3545'];
const statusData   = @json($statusData);
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusKeys.map(k => statusData[k] ?? 0),
            backgroundColor: statusColors,
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
        cutout: '60%',
    }
});

// Chart Site
const siteData = @json($siteData);
new Chart(document.getElementById('chartSite'), {
    type: 'horizontalBar' in Chart.defaults ? 'horizontalBar' : 'bar',
    data: {
        labels: siteData.map(s => s.site ? s.site.nama : '-'),
        datasets: [{
            label: 'Laporan',
            data: siteData.map(s => s.total),
            backgroundColor: ['#0d3b8e','#1a5fbf','#0dcaf0','#198754','#e8a020'],
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
    }
});
</script>
@endpush