@extends('layouts.app')
@section('title','Detail Laporan Tugas')

@push('styles')
<style>
.sig-pad-wrap { border: 2px dashed #dee2e6; border-radius: 10px; background: #fafafa; position: relative; }
.sig-pad-wrap canvas { display: block; border-radius: 8px; cursor: crosshair; touch-action: none; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h4><i class="bi bi-tools me-2"></i>Detail Laporan Tugas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teknisi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teknisi.laporan.index') }}">Laporan</a></li>
            <li class="breadcrumb-item active">{{ $laporan->nomor_laporan }}</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-card mb-4">
            <div class="table-card-header">
                <h6><i class="bi bi-info-circle me-2"></i>Detail Laporan</h6>
                <span class="badge bg-{{ $laporan->status_badge }}">{{ $laporan->status_label }}</span>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-6"><div class="small text-muted">Nomor Laporan</div><div class="fw-700 text-primary">{{ $laporan->nomor_laporan }}</div></div>
                    <div class="col-6"><div class="small text-muted">Tanggal</div><div class="fw-600">{{ $laporan->tanggal_laporan->isoFormat('D MMMM Y') }}</div></div>
                    <div class="col-6"><div class="small text-muted">Pelapor</div><div class="fw-600">{{ $laporan->pelapor->name }}</div></div>
                    <div class="col-6"><div class="small text-muted">Jabatan</div><div class="fw-600">{{ $laporan->jabatan_pelapor }}</div></div>
                    <div class="col-6"><div class="small text-muted">Site</div><div class="fw-600">{{ $laporan->site->nama }}</div></div>
                    <div class="col-6"><div class="small text-muted">Tipe Radio</div><div class="fw-600">{{ $laporan->tipeRadio->nama }}</div></div>
                    <div class="col-12"><div class="small text-muted">Jenis Kerusakan</div><div class="fw-600">{{ $laporan->jenisKerusakan->nama }}</div></div>
                    <div class="col-12">
                        <div class="small text-muted mb-1">Deskripsi</div>
                        <div class="p-3 rounded-3" style="background:#f8f9ff;border:1px solid #e8eaf0">{{ $laporan->deskripsi_kerusakan }}</div>
                    </div>
                    @if($laporan->foto)
                    <div class="col-12">
                        <div class="small text-muted mb-2">Foto Kerusakan</div>
                        <img src="{{ Storage::url($laporan->foto) }}" class="img-fluid rounded-3" style="max-height:250px">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($laporan->ttd_pelapor)
        <div class="table-card">
            <div class="table-card-header"><h6><i class="bi bi-pen me-2"></i>Tanda Tangan Pelapor</h6></div>
            <div class="p-4">
                <img src="{{ $laporan->ttd_pelapor }}" class="img-fluid rounded border" style="max-height:120px;background:#fff">
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-5">
        @if(in_array($laporan->status, ['diverifikasi', 'sedang_proses']))
        <div class="table-card mb-4">
            <div class="table-card-header">
                <h6><i class="bi bi-arrow-up-circle me-2"></i>Update Status</h6>
            </div>
            <div class="p-4">
                @if($laporan->deadline_proses)
                <div class="mb-3 p-3 rounded-3 text-center {{ $laporan->is_overdue_proses ? 'bg-danger-subtle' : 'bg-warning-subtle' }}" style="border:1px dashed {{ $laporan->is_overdue_proses ? '#dc3545' : '#ffc107' }}">
                    <div class="small mb-1 {{ $laporan->is_overdue_proses ? 'text-danger' : 'text-warning-emphasis' }}">
                        <i class="bi bi-alarm me-1"></i>Tenggat Pengerjaan
                    </div>
                    <div class="fw-700 {{ $laporan->is_overdue_proses ? 'text-danger' : 'text-warning-emphasis' }}">
                        {{ $laporan->deadline_proses->isoFormat('D MMMM Y, HH:mm') }}
                    </div>
                    <div class="small fw-600 mt-1 {{ $laporan->is_overdue_proses ? 'text-danger' : 'text-warning-emphasis' }}">
                        {{ $laporan->sisa_waktu_proses }}
                    </div>
                </div>
                @endif
                <form action="{{ route('teknisi.laporan.updateStatus', $laporan->id) }}" method="POST" id="statusForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-700 small">Status Baru <span class="text-danger">*</span></label>
                        <select name="status" id="statusSelect" class="form-select" required onchange="toggleTtd(this.value)">
                            @if($laporan->status === 'diverifikasi')
                            <option value="sedang_proses">Sedang Proses</option>
                            @endif
                            @if(in_array($laporan->status, ['diverifikasi','sedang_proses']))
                            <option value="selesai">Selesai</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-700 small">Catatan Teknisi</label>
                        <textarea name="catatan_teknisi" class="form-control" rows="3" placeholder="Catatan perbaikan...">{{ $laporan->catatan_teknisi }}</textarea>
                    </div>
                    <!-- TTD only when selesai -->
                    <div id="ttdWrap" style="display:none" class="mb-3">
                        <label class="form-label fw-700 small">Tanda Tangan Teknisi</label>
                        <div class="sig-pad-wrap">
                            <canvas id="sigCanvas" width="400" height="130"></canvas>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="position:absolute;top:8px;right:8px" onclick="clearSig()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <input type="hidden" name="ttd_teknisi" id="ttdTeknisi">
                    </div>
                    <button type="submit" class="btn btn-success w-100" onclick="saveSig()">
                        <i class="bi bi-check-circle-fill me-2"></i>Update Status
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($laporan->catatan_teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-chat-left-text me-2"></i>Catatan Teknisi</h6></div>
            <div class="p-4"><p class="small mb-0">{{ $laporan->catatan_teknisi }}</p></div>
        </div>
        @endif

        @if($laporan->ttd_teknisi)
        <div class="table-card">
            <div class="table-card-header"><h6><i class="bi bi-pen me-2"></i>Tanda Tangan Teknisi</h6></div>
            <div class="p-4">
                <img src="{{ $laporan->ttd_teknisi }}" class="img-fluid rounded border" style="max-height:120px;background:#fff">
            </div>
        </div>
        @endif

        {{-- Ringkasan Tenggat --}}
        <div class="table-card mb-3">
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
                @if($laporan->is_overdue_proses)
                <div class="mt-2 p-2 rounded-2 small bg-danger-subtle text-danger" style="border:1px dashed #dc3545">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Tenggat proses telah lewat!</strong>
                    <div class="mt-1">{{ $laporan->sisa_waktu_proses }}</div>
                </div>
                @elseif(in_array($laporan->status, ['diverifikasi','sedang_proses']) && $laporan->deadline_proses)
                <div class="mt-2 p-2 rounded-2 small bg-warning-subtle text-warning-emphasis" style="border:1px dashed #ffc107">
                    <i class="bi bi-alarm me-1"></i>
                    Sisa waktu pengerjaan: <strong>{{ $laporan->sisa_waktu_proses }}</strong>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('teknisi.laporan.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTtd(val) {
    document.getElementById('ttdWrap').style.display = (val === 'selesai') ? 'block' : 'none';
}
toggleTtd(document.getElementById('statusSelect')?.value || '');

const canvas = document.getElementById('sigCanvas');
if (canvas) {
    const ctx = canvas.getContext('2d');
    let drawing = false;
    canvas.style.width = '100%';
    canvas.width = canvas.offsetWidth || 400;
    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return { x: (src.clientX - rect.left) * (canvas.width / rect.width), y: (src.clientY - rect.top) * (canvas.height / rect.height) };
    }
    canvas.addEventListener('mousedown',  e => { drawing=true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  e => { if(!drawing)return; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.strokeStyle='#198754'; ctx.lineWidth=2; ctx.lineCap='round'; ctx.stroke(); });
    canvas.addEventListener('mouseup',    () => drawing=false);
    canvas.addEventListener('mouseleave', () => drawing=false);
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing=true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); },{passive:false});
    canvas.addEventListener('touchmove',  e => { e.preventDefault(); if(!drawing)return; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.strokeStyle='#198754'; ctx.lineWidth=2; ctx.lineCap='round'; ctx.stroke(); },{passive:false});
    canvas.addEventListener('touchend',   () => drawing=false);
}
function clearSig() { const c=document.getElementById('sigCanvas'); c.getContext('2d').clearRect(0,0,c.width,c.height); }
function saveSig()  { const c=document.getElementById('sigCanvas'); if(c) document.getElementById('ttdTeknisi').value=c.toDataURL(); }
</script>
@endpush