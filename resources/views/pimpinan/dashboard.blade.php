@extends('layouts.app')
@section('title','Dashboard Pimpinan')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-graph-up me-2"></i>Dashboard Pimpinan</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <a href="{{ route('pimpinan.laporan.index') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-clipboard2-data me-1"></i>Monitoring Laporan
    </a>
</div>

<div class="p-4 mb-4 rounded-4 text-white" style="background:linear-gradient(135deg,#2c3e50,#3498db)">
    <h5 class="fw-800 mb-1">Selamat Datang, {{ auth()->user()->name }}! 📊</h5>
    <p class="mb-0 opacity-75 small">{{ auth()->user()->jabatan }} &bull; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    @php $cards = [
        ['val'=>$stats['total'],   'label'=>'Total Laporan',  'icon'=>'bi-clipboard2-fill',   'bg'=>'#e8f0fe','color'=>'#0d3b8e'],
        ['val'=>$stats['menunggu'],'label'=>'Menunggu/Verif', 'icon'=>'bi-hourglass-split',    'bg'=>'#fff3cd','color'=>'#e8a020'],
        ['val'=>$stats['proses'],  'label'=>'Sedang Proses',  'icon'=>'bi-tools',              'bg'=>'#cff4fc','color'=>'#0dcaf0'],
        ['val'=>$stats['selesai'], 'label'=>'Selesai',        'icon'=>'bi-check-circle-fill',  'bg'=>'#d1e7dd','color'=>'#198754'],
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
        <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.07)">
            <h6 class="fw-700 mb-3" style="color:#0d3b8e"><i class="bi bi-bar-chart-line me-2"></i>Laporan per Bulan ({{ date('Y') }})</h6>
            <canvas id="chartBulan" height="110"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.07)">
            <h6 class="fw-700 mb-3" style="color:#0d3b8e"><i class="bi bi-pie-chart me-2"></i>Status Laporan</h6>
            <canvas id="chartStatus" height="180"></canvas>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-12">
        <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.07)">
            <h6 class="fw-700 mb-3" style="color:#0d3b8e"><i class="bi bi-geo-alt me-2"></i>Top 5 Site Laporan Terbanyak</h6>
            <canvas id="chartSite" height="80"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Laporan',
            data: @json($chartData),
            backgroundColor: 'rgba(52,152,219,0.75)',
            borderColor: '#2980b9',
            borderWidth: 2, borderRadius: 6,
        }]
    },
    options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});

const statusLabels = ['Menunggu','Diverifikasi','Sedang Proses','Selesai','Ditolak'];
const statusKeys   = ['menunggu_verifikasi','diverifikasi','sedang_proses','selesai','ditolak'];
const statusColors = ['#ffc107','#0dcaf0','#0d6efd','#198754','#dc3545'];
const statusData   = @json($statusData);
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusKeys.map(k=>statusData[k]??0), backgroundColor: statusColors, borderWidth:2 }]
    },
    options: { responsive:true, plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}, cutout:'60%' }
});

const siteData = @json($siteData);
new Chart(document.getElementById('chartSite'), {
    type: 'bar',
    data: {
        labels: siteData.map(s=>s.site?s.site.nama:'-'),
        datasets: [{ label:'Laporan', data:siteData.map(s=>s.total), backgroundColor:['#0d3b8e','#3498db','#0dcaf0','#198754','#e8a020'], borderRadius:6 }]
    },
    options: { indexAxis:'y', responsive:true, plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true}} }
});
</script>
@endpush