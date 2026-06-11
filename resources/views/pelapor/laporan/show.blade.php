@extends('layouts.app')
@section('title','Detail Laporan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-text me-2"></i>Detail Laporan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pelapor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pelapor.laporan.index') }}">Laporan</a></li>
            <li class="breadcrumb-item active">{{ $laporan->nomor_laporan }}</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card mb-4">
            <div class="table-card-header">
                <h6><i class="bi bi-info-circle me-2"></i>Informasi Laporan</h6>
                <span class="badge bg-{{ $laporan->status_badge }} fs-6">{{ $laporan->status_label }}</span>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Nomor Laporan</div>
                        <div class="fw-700 text-primary">{{ $laporan->nomor_laporan }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Tanggal Laporan</div>
                        <div class="fw-600">{{ $laporan->tanggal_laporan->isoFormat('D MMMM Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Nama Pelapor</div>
                        <div class="fw-600">{{ $laporan->nama_pelapor }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Jabatan</div>
                        <div class="fw-600">{{ $laporan->jabatan_pelapor }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Site / Lokasi</div>
                        <div class="fw-600">{{ $laporan->site->nama }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Tipe Radio</div>
                        <div class="fw-600">{{ $laporan->tipeRadio->nama }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Jenis Kerusakan</div>
                        <div class="fw-600">{{ $laporan->jenisKerusakan->nama }}</div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted mb-1">Deskripsi Kerusakan</div>
                        <div class="p-3 rounded-3" style="background:#f8f9ff;border:1px solid #e8eaf0">
                            {{ $laporan->deskripsi_kerusakan }}
                        </div>
                    </div>
                    @if($laporan->foto)
                    <div class="col-12">
                        <div class="small text-muted mb-2">Foto Kerusakan</div>
                        <img src="{{ Storage::url($laporan->foto) }}" class="img-fluid rounded-3" style="max-height:300px;object-fit:cover">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Status -->
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="bi bi-activity me-2"></i>Status Perkembangan</h6>
            </div>
            <div class="p-4">
                @php
                    $steps = [
                        ['key'=>'menunggu_verifikasi','label'=>'Laporan Dikirim','icon'=>'bi-send-fill','color'=>'warning'],
                        ['key'=>'diverifikasi','label'=>'Diverifikasi Admin','icon'=>'bi-shield-check','color'=>'info'],
                        ['key'=>'sedang_proses','label'=>'Sedang Diperbaiki','icon'=>'bi-tools','color'=>'primary'],
                        ['key'=>'selesai','label'=>'Selesai','icon'=>'bi-check-circle-fill','color'=>'success'],
                    ];
                    $order = ['menunggu_verifikasi'=>0,'diverifikasi'=>1,'sedang_proses'=>2,'selesai'=>3,'ditolak'=>-1];
                    $cur = $order[$laporan->status] ?? 0;
                @endphp

                @if($laporan->status === 'ditolak')
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#fdf2f2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;background:#dc3545;flex-shrink:0">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="fw-700 text-danger">Laporan Ditolak</div>
                        <div class="small text-muted">{{ $laporan->catatan_admin ?? '-' }}</div>
                        @if($laporan->tanggal_verifikasi)
                        <div class="small text-muted mt-1">
                            <i class="bi bi-calendar-x me-1"></i>
                            Ditolak pada: <strong>{{ $laporan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</strong>
                            @if($laporan->durasi_verifikasi)
                            &bull; Durasi peninjauan: <strong>{{ $laporan->durasi_verifikasi }}</strong>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                @else

                {{-- Banner estimasi selesai --}}
                @if(in_array($laporan->status, ['diverifikasi','sedang_proses']) && $laporan->estimasi_selesai)
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#e8f4ff;border:1px solid #b8daff">
                    <i class="bi bi-calendar-check-fill text-primary fs-5 flex-shrink-0"></i>
                    <div>
                        <div class="small text-muted">Estimasi Penyelesaian</div>
                        <div class="fw-700 text-primary">{{ $laporan->estimasi_selesai->isoFormat('D MMMM Y, HH:mm') }}</div>
                        <div class="small {{ $laporan->is_overdue_proses ? 'text-danger fw-600' : 'text-muted' }}">
                            {{ $laporan->sisa_waktu_proses }}
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex flex-column gap-3">
                    @foreach($steps as $i => $step)
                    @php $done = $cur >= $i; $active = $cur === $i; @endphp
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 {{ $done ? 'bg-'.$step['color'].'-subtle' : '' }}" style="opacity:{{ $done ? 1 : 0.4 }}">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $done ? 'text-white bg-'.$step['color'] : 'bg-light text-muted' }}" style="width:40px;height:40px;flex-shrink:0">
                            <i class="bi {{ $step['icon'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-700 {{ $done ? 'text-'.$step['color'] : 'text-muted' }}">{{ $step['label'] }}</div>

                            {{-- Tanggal realisasi tiap step --}}
                            @if($step['key'] === 'menunggu_verifikasi' && $done)
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar-event me-1"></i>
                                Dikirim: <strong>{{ $laporan->created_at->isoFormat('D MMMM Y, HH:mm') }}</strong>
                            </div>
                            @endif

                            @if($step['key'] === 'diverifikasi' && $done && $laporan->tanggal_verifikasi)
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar-event me-1"></i>
                                Diverifikasi: <strong>{{ $laporan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</strong>
                            </div>
                            @if($laporan->durasi_verifikasi)
                            <div class="small text-muted">
                                <i class="bi bi-stopwatch me-1"></i>Durasi verifikasi: <strong>{{ $laporan->durasi_verifikasi }}</strong>
                            </div>
                            @endif
                            @endif

                            @if($step['key'] === 'sedang_proses' && $done)
                            @if($active && $laporan->catatan_teknisi)
                            <div class="small text-muted mt-1">Catatan: {{ $laporan->catatan_teknisi }}</div>
                            @endif
                            @if($laporan->tanggal_verifikasi)
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar-event me-1"></i>
                                Mulai diproses: <strong>{{ $laporan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</strong>
                            </div>
                            @endif
                            @endif

                            @if($step['key'] === 'selesai' && $done && $laporan->tanggal_selesai)
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar-check me-1"></i>
                                Selesai: <strong>{{ $laporan->tanggal_selesai->isoFormat('D MMMM Y, HH:mm') }}</strong>
                            </div>
                            @if($laporan->durasi_proses)
                            <div class="small text-muted">
                                <i class="bi bi-stopwatch me-1"></i>Durasi proses: <strong>{{ $laporan->durasi_proses }}</strong>
                            </div>
                            @endif
                            @if($laporan->durasi_total)
                            <div class="small fw-600 text-success mt-1">
                                <i class="bi bi-trophy-fill me-1"></i>Total penanganan: <strong>{{ $laporan->durasi_total }}</strong>
                            </div>
                            @endif
                            @endif

                            {{-- Tenggat + sisa waktu untuk step aktif yang belum selesai --}}
                            @if($active && $laporan->status !== 'selesai')
                                @if($step['key'] === 'menunggu_verifikasi')
                                <div class="mt-2 p-2 rounded-2 small {{ $laporan->is_overdue_verifikasi ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning-emphasis' }}" style="border:1px dashed {{ $laporan->is_overdue_verifikasi ? '#dc3545' : '#ffc107' }}">
                                    <i class="bi bi-alarm me-1"></i>
                                    Tenggat verifikasi: <strong>{{ $laporan->deadline_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</strong>
                                    <div class="fw-600 mt-1">{{ $laporan->sisa_waktu_verifikasi }}</div>
                                </div>
                                @endif
                                @if(in_array($step['key'], ['diverifikasi','sedang_proses']) && $laporan->deadline_proses)
                                <div class="mt-2 p-2 rounded-2 small {{ $laporan->is_overdue_proses ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning-emphasis' }}" style="border:1px dashed {{ $laporan->is_overdue_proses ? '#dc3545' : '#ffc107' }}">
                                    <i class="bi bi-alarm me-1"></i>
                                    Tenggat selesai: <strong>{{ $laporan->deadline_proses->isoFormat('D MMMM Y, HH:mm') }}</strong>
                                    <div class="fw-600 mt-1">{{ $laporan->sisa_waktu_proses }}</div>
                                </div>
                                @endif
                            @endif
                        </div>
                        @if($done)<i class="bi bi-check-lg ms-auto text-{{ $step['color'] }} fw-700 flex-shrink-0"></i>@endif
                    </div>
                    @endforeach
                </div>

                {{-- Info tenggat di bawah --}}
                @if($laporan->status === 'menunggu_verifikasi' && !$laporan->is_overdue_verifikasi)
                <div class="mt-3 p-3 rounded-3 small" style="background:#fffbeb;border:1px solid #fde68a">
                    <i class="bi bi-info-circle me-1 text-warning"></i>
                    Laporan dinyatakan <strong>gagal</strong> jika belum diverifikasi dalam <strong>1×24 jam</strong>
                    sejak dikirim &mdash; paling lambat <strong>{{ $laporan->deadline_verifikasi->isoFormat('D MMMM Y, HH:mm') }}</strong>.
                </div>
                @endif
                @if(in_array($laporan->status, ['diverifikasi','sedang_proses']) && $laporan->deadline_proses && !$laporan->is_overdue_proses)
                <div class="mt-3 p-3 rounded-3 small" style="background:#fffbeb;border:1px solid #fde68a">
                    <i class="bi bi-info-circle me-1 text-warning"></i>
                    Perbaikan dijadwalkan selesai dalam <strong>2×24 jam</strong> sejak diverifikasi &mdash;
                    paling lambat <strong>{{ $laporan->deadline_proses->isoFormat('D MMMM Y, HH:mm') }}</strong>.
                </div>
                @endif

                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($laporan->teknisi)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-person-gear me-2"></i>Teknisi Bertugas</h6></div>
            <div class="p-4 text-center">
                <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px">
                    <i class="bi bi-person-fill fs-2 text-primary"></i>
                </div>
                <div class="fw-700">{{ $laporan->teknisi->name }}</div>
                <div class="small text-muted">{{ $laporan->teknisi->jabatan }}</div>
                <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>{{ $laporan->teknisi->no_hp }}</div>
            </div>
        </div>
        @endif

        {{-- Ringkasan tanggal --}}
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-calendar3 me-2"></i>Ringkasan Tanggal</h6></div>
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
            </div>
        </div>

        @if($laporan->catatan_admin)
        <div class="table-card mb-4">
            <div class="table-card-header"><h6><i class="bi bi-chat-left-text me-2"></i>Catatan Admin</h6></div>
            <div class="p-4">
                <p class="small mb-0">{{ $laporan->catatan_admin }}</p>
            </div>
        </div>
        @endif

        <div class="d-grid gap-2">
            @if($laporan->status === 'menunggu_verifikasi')
            <a href="{{ route('pelapor.laporan.edit', $laporan->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-fill me-2"></i>Edit Laporan
            </a>
            <button onclick="confirmDelete('del-{{ $laporan->id }}','Hapus laporan ini?')" class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i>Hapus Laporan
            </button>
            <form id="del-{{ $laporan->id }}" action="{{ route('pelapor.laporan.destroy', $laporan->id) }}" method="POST" class="d-none">
                @csrf @method('DELETE')
            </form>
            @endif
            <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection
