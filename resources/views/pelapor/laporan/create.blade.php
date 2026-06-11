@extends('layouts.app')
@section('title', 'Buat Laporan')

@push('styles')
    <style>
        .sig-pad-wrap {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            background: #fafafa;
            position: relative;
        }

        .sig-pad-wrap canvas {
            display: block;
            border-radius: 8px;
            cursor: crosshair;
            touch-action: none;
        }

        .sig-clear {
            position: absolute;
            top: 8px;
            right: 8px;
        }

        .preview-img {
            max-height: 180px;
            border-radius: 10px;
            object-fit: cover;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h4><i class="bi bi-plus-circle-fill me-2"></i>Buat Laporan Kerusakan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pelapor.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Buat Laporan</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="bi bi-file-earmark-plus me-2"></i>Form Laporan Kerusakan Radio Trunking</h6>
                    <span class="badge bg-primary">No: {{ $nomorLaporan }}</span>
                </div>
                <div class="p-4">
                    <form action="{{ route('pelapor.laporan.store') }}" method="POST" enctype="multipart/form-data"
                        id="laporanForm">
                        @csrf

                        <div class="row g-3">
                            <!-- Nomor Laporan (read only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Nomor Laporan</label>
                                <input type="text" class="form-control bg-light" value="{{ $nomorLaporan }}" readonly>
                            </div>

                            <!-- Nama Pelapor (read only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Nama Pelapor</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}"
                                    readonly>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan_pelapor"
                                    class="form-control @error('jabatan_pelapor') is-invalid @enderror"
                                    value="{{ old('jabatan_pelapor', auth()->user()->jabatan) }}" placeholder="Jabatan Anda"
                                    required>
                                @error('jabatan_pelapor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Tanggal Laporan <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_laporan"
                                    class="form-control @error('tanggal_laporan') is-invalid @enderror"
                                    value="{{ old('tanggal_laporan', date('Y-m-d')) }}" required>
                                @error('tanggal_laporan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Site -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Site / Lokasi <span
                                        class="text-danger">*</span></label>
                                <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($sites as $s)
                                        <option value="{{ $s->id }}"
                                            {{ old('site_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipe Radio -->
                            <div class="col-md-6">
                                <label class="form-label fw-700 small">Tipe Radio <span class="text-danger">*</span></label>
                                <select name="tipe_radio_id"
                                    class="form-select @error('tipe_radio_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tipe Radio --</option>
                                    @foreach ($tipeRadios as $t)
                                        <option value="{{ $t->id }}"
                                            {{ old('tipe_radio_id') == $t->id ? 'selected' : '' }}>{{ $t->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe_radio_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jenis Kerusakan -->
                            <div class="col-12">
                                <label class="form-label fw-700 small">Jenis Kerusakan <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_kerusakan_id"
                                    class="form-select @error('jenis_kerusakan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Kerusakan --</option>
                                    @foreach ($jenisKerusakans as $j)
                                        <option value="{{ $j->id }}"
                                            {{ old('jenis_kerusakan_id') == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kerusakan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label fw-700 small">Deskripsi Kerusakan (Detail) <span
                                        class="text-danger">*</span></label>
                                <textarea name="deskripsi_kerusakan" class="form-control @error('deskripsi_kerusakan') is-invalid @enderror"
                                    rows="4" placeholder="Jelaskan detail kerusakan radio..." required>{{ old('deskripsi_kerusakan') }}</textarea>
                                @error('deskripsi_kerusakan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div class="col-12">
                                <label class="form-label fw-700 small">Foto Kerusakan</label>
                                <input type="file" name="foto" id="fotoInput"
                                    class="form-control @error('foto') is-invalid @enderror"
                                    accept="image/jpeg,image/png,image/jpg" onchange="previewFoto(this)">
                                <div class="form-text">Format: JPG/PNG, Maks 5MB</div>
                                @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <img id="fotoPreview" class="preview-img mt-2" src="" alt="Preview">
                            </div>

                            <!-- Tanda Tangan Pelapor -->
                            <div class="col-12">
                                <label class="form-label fw-700 small">Tanda Tangan Pelapor</label>
                                <div class="sig-pad-wrap">
                                    <canvas id="sigCanvas" width="500" height="150"></canvas>
                                    <button type="button" class="btn btn-sm btn-outline-danger sig-clear"
                                        onclick="clearSig()">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                                <input type="hidden" name="ttd_pelapor" id="ttdPelapor">
                                <div class="form-text">Tanda tangan menggunakan mouse / jari di atas kotak</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4" onclick="saveSig()">
                                <i class="bi bi-send-fill me-2"></i>Kirim Laporan
                            </button>
                            <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-lg me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Foto Preview ──
        function previewFoto(input) {
            const preview = document.getElementById('fotoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ── Signature Pad ──
        const canvas = document.getElementById('sigCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.style.width = '100%';
        canvas.width = canvas.offsetWidth || 500;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const src = e.touches ? e.touches[0] : e;
            return {
                x: (src.clientX - rect.left) * (canvas.width / rect.width),
                y: (src.clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        canvas.addEventListener('mousedown', e => {
            drawing = true;
            const p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
        });
        canvas.addEventListener('mousemove', e => {
            if (!drawing) return;
            const p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.strokeStyle = '#0d3b8e';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();
        });
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('mouseleave', () => drawing = false);
        canvas.addEventListener('touchstart', e => {
            e.preventDefault();
            drawing = true;
            const p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
        }, {
            passive: false
        });
        canvas.addEventListener('touchmove', e => {
            e.preventDefault();
            if (!drawing) return;
            const p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.strokeStyle = '#0d3b8e';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();
        }, {
            passive: false
        });
        canvas.addEventListener('touchend', () => drawing = false);

        function clearSig() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveSig() {
            document.getElementById('ttdPelapor').value = canvas.toDataURL();
        }
    </script>
@endpush
