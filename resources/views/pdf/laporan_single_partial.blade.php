<div class="header">
    <div class="header-logos">
        <img src="{{ public_path('logo/mobilkom.png') }}" alt="Mobilkom">
    </div>
    <div class="header-title">
        <h2>LAPORAN KERUSAKAN RADIO TRUNKING</h2>
        <h3>PT Mobilkom – Pertamina Hulu Rokan</h3>
        <p>Sistem Pelaporan &amp; Perbaikan Radio Trunking</p>
    </div>
    <div class="header-logo-right">
        <img src="{{ public_path('logo/pertaminahulurokan.png') }}" alt="PHR">
    </div>
</div>

<div class="nomor-laporan">
    <h4>{{ $laporan->nomor_laporan }}</h4>
    <span>Dicetak pada: {{ now()->isoFormat('D MMMM Y HH:mm') }}</span>
</div>

<div class="section-title">A. INFORMASI PELAPORAN</div>
<table class="info">
    <tr>
        <td class="label">Nomor Laporan</td>
        <td>{{ $laporan->nomor_laporan }}</td>
        <td class="label">Tanggal Laporan</td>
        <td>{{ $laporan->tanggal_laporan->isoFormat('D MMMM Y') }}</td>
    </tr>
    <tr>
        <td class="label">Nama Pelapor</td>
        <td>{{ $laporan->nama_pelapor }}</td>
        <td class="label">Jabatan</td>
        <td>{{ $laporan->jabatan_pelapor }}</td>
    </tr>
    <tr>
        <td class="label">Site / Lokasi</td>
        <td>{{ $laporan->site->nama }}</td>
        <td class="label">Status Akhir</td>
        <td>
            <span class="status-box status-{{ $laporan->status }}">{{ $laporan->status_label }}</span>
        </td>
    </tr>
</table>

<div class="section-title">B. DETAIL KERUSAKAN</div>
<table class="info">
    <tr>
        <td class="label">Tipe Radio</td>
        <td>{{ $laporan->tipeRadio->nama }}</td>
        <td class="label">Jenis Kerusakan</td>
        <td>{{ $laporan->jenisKerusakan->nama }}</td>
    </tr>
</table>
<div class="deskripsi-box">
    <div class="label">Deskripsi Kerusakan:</div>
    {{ $laporan->deskripsi_kerusakan }}
</div>

@if($laporan->foto && file_exists(public_path('storage/' . $laporan->foto)))
<div class="section-title">C. FOTO KERUSAKAN</div>
<div class="foto-box">
    <img src="{{ public_path('storage/' . $laporan->foto) }}" alt="Foto">
</div>
@endif

<div class="section-title">D. INFORMASI PENANGANAN</div>
<table class="info">
    <tr>
        <td class="label">Teknisi Bertugas</td>
        <td>{{ $laporan->teknisi?->name ?? '-' }}</td>
        <td class="label">Diverifikasi Oleh</td>
        <td>{{ $laporan->admin?->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Catatan Admin</td>
        <td>{{ $laporan->catatan_admin ?? '-' }}</td>
        <td class="label">Catatan Teknisi</td>
        <td>{{ $laporan->catatan_teknisi ?? '-' }}</td>
    </tr>
</table>

<div class="section-title">E. TIMELINE &amp; DURASI PENANGANAN</div>
<table class="info">
    <tr>
        <td class="label">Tanggal Dikirim</td>
        <td>{{ $laporan->created_at->isoFormat('D MMMM Y, HH:mm') }}</td>
        <td class="label">Tenggat Verifikasi</td>
        <td>
            {{ $laporan->deadline_verifikasi->isoFormat('D MMMM Y, HH:mm') }}
            @if($laporan->is_overdue_verifikasi)
            <span style="color:#dc3545;font-weight:bold"> (Terlambat)</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Tanggal Diverifikasi</td>
        <td>{{ $laporan->tanggal_verifikasi ? $laporan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') : '-' }}</td>
        <td class="label">Tenggat Proses</td>
        <td>
            {{ $laporan->deadline_proses ? $laporan->deadline_proses->isoFormat('D MMMM Y, HH:mm') : '-' }}
            @if($laporan->is_overdue_proses)
            <span style="color:#dc3545;font-weight:bold"> (Terlambat)</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Tanggal Selesai</td>
        <td>{{ $laporan->tanggal_selesai ? $laporan->tanggal_selesai->isoFormat('D MMMM Y, HH:mm') : '-' }}</td>
        <td class="label">Durasi Verifikasi</td>
        <td>{{ $laporan->durasi_verifikasi ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Durasi Proses</td>
        <td>{{ $laporan->durasi_proses ?? '-' }}</td>
        <td class="label" style="background:#e8f0fe;color:#0d3b8e;font-weight:bold">Total Durasi</td>
        <td style="font-weight:bold;color:#0d3b8e">{{ $laporan->durasi_total ?? '-' }}</td>
    </tr>
</table>

{{-- Progress bar timeline --}}
@php
    $tlSteps = [
        ['key'=>'menunggu_verifikasi','label'=>'Dikirim','tgl'=>$laporan->created_at],
        ['key'=>'diverifikasi','label'=>'Diverifikasi','tgl'=>$laporan->tanggal_verifikasi],
        ['key'=>'sedang_proses','label'=>'Diproses','tgl'=>$laporan->tanggal_verifikasi],
        ['key'=>'selesai','label'=>'Selesai','tgl'=>$laporan->tanggal_selesai],
    ];
    $tlOrder = ['menunggu_verifikasi'=>0,'diverifikasi'=>1,'sedang_proses'=>2,'selesai'=>3,'ditolak'=>-1];
    $tlCur = $tlOrder[$laporan->status] ?? 0;
    $tlColors = ['#856404','#055160','#084298','#0f5132'];
    $tlBgs    = ['#fff3cd','#cff4fc','#cfe2ff','#d1e7dd'];
@endphp
<div style="display:table;width:100%;border-collapse:collapse;margin-bottom:14px">
    @foreach($tlSteps as $ti => $ts)
    @php $tdone = $tlCur >= $ti; @endphp
    <div style="display:table-cell;text-align:center;padding:6px 3px;border:1px solid {{ $tdone ? $tlColors[$ti] : '#ddd' }};background:{{ $tdone ? $tlBgs[$ti] : '#f8f9fa' }};opacity:{{ $tdone ? 1 : 0.45 }}">
        <div style="font-weight:bold;font-size:10px;color:{{ $tdone ? $tlColors[$ti] : '#999' }}">{{ $ts['label'] }}</div>
        <div style="font-size:9px;color:{{ $tdone ? $tlColors[$ti] : '#ccc' }};margin-top:2px">
            {{ ($ts['tgl'] && $tdone) ? $ts['tgl']->isoFormat('D MMM Y') : '-' }}
        </div>
    </div>
    @endforeach
</div>

@if($laporan->status === 'ditolak')
<div style="background:#f8d7da;border:1px solid #dc3545;border-radius:4px;padding:8px 12px;margin-bottom:14px;color:#842029">
    <strong>Laporan Ditolak</strong> &mdash; {{ $laporan->catatan_admin ?? '-' }}
    @if($laporan->tanggal_verifikasi)
    <div style="font-size:10px;margin-top:2px">Ditolak pada: {{ $laporan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</div>
    @endif
</div>
@endif

<div class="ttd-section">
    <div class="section-title">F. TANDA TANGAN</div>
    <div class="ttd-row">
        <div class="ttd-cell">
            <div style="margin-bottom:4px; font-size:10px; color:#555">Yang Melapor</div>
            <div class="ttd-box">
                @if($laporan->ttd_pelapor)
                <img src="{{ $laporan->ttd_pelapor }}" alt="TTD Pelapor">
                @else
                <span style="color:#ccc;font-size:10px">Belum ada TTD</span>
                @endif
            </div>
            <div class="ttd-name">{{ $laporan->nama_pelapor }}</div>
            <div class="ttd-label">{{ $laporan->jabatan_pelapor }}</div>
        </div>
        <div class="ttd-cell">
            <div style="margin-bottom:4px; font-size:10px; color:#555">Teknisi Perbaikan</div>
            <div class="ttd-box">
                @if($laporan->ttd_teknisi)
                <img src="{{ $laporan->ttd_teknisi }}" alt="TTD Teknisi">
                @else
                <span style="color:#ccc;font-size:10px">Belum ada TTD</span>
                @endif
            </div>
            <div class="ttd-name">{{ $laporan->teknisi?->name ?? '...................' }}</div>
            <div class="ttd-label">{{ $laporan->teknisi?->jabatan ?? 'Teknisi Radio' }}</div>
        </div>
    </div>
</div>

<div class="footer">
    Dokumen ini dicetak secara otomatis oleh Sistem Pelaporan Radio Trunking PT Mobilkom – Pertamina Hulu Rokan &bull; {{ now()->isoFormat('D MMMM Y HH:mm') }}
</div>
