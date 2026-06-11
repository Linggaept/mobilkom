@extends('layouts.app')
@section('title','Edit Laporan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i>Edit Laporan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pelapor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pelapor.laporan.index') }}">Laporan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-pencil me-2"></i>Edit Laporan: {{ $laporan->nomor_laporan }}</h6>
    </div>
    <div class="p-4">
    <form action="{{ route('pelapor.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-700 small">Nomor Laporan</label>
                <input type="text" class="form-control bg-light" value="{{ $laporan->nomor_laporan }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Nama Pelapor</label>
                <input type="text" class="form-control bg-light" value="{{ $laporan->nama_pelapor }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan_pelapor" class="form-control @error('jabatan_pelapor') is-invalid @enderror"
                       value="{{ old('jabatan_pelapor', $laporan->jabatan_pelapor) }}" required>
                @error('jabatan_pelapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Tanggal Laporan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_laporan" class="form-control @error('tanggal_laporan') is-invalid @enderror"
                       value="{{ old('tanggal_laporan', $laporan->tanggal_laporan->format('Y-m-d')) }}" required>
                @error('tanggal_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Site / Lokasi <span class="text-danger">*</span></label>
                <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($sites as $s)
                        <option value="{{ $s->id }}" {{ old('site_id',$laporan->site_id) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
                @error('site_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Tipe Radio <span class="text-danger">*</span></label>
                <select name="tipe_radio_id" class="form-select @error('tipe_radio_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($tipeRadios as $t)
                        <option value="{{ $t->id }}" {{ old('tipe_radio_id',$laporan->tipe_radio_id) == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                    @endforeach
                </select>
                @error('tipe_radio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-700 small">Jenis Kerusakan <span class="text-danger">*</span></label>
                <select name="jenis_kerusakan_id" class="form-select @error('jenis_kerusakan_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($jenisKerusakans as $j)
                        <option value="{{ $j->id }}" {{ old('jenis_kerusakan_id',$laporan->jenis_kerusakan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
                @error('jenis_kerusakan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-700 small">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                <textarea name="deskripsi_kerusakan" class="form-control @error('deskripsi_kerusakan') is-invalid @enderror" rows="4" required>{{ old('deskripsi_kerusakan', $laporan->deskripsi_kerusakan) }}</textarea>
                @error('deskripsi_kerusakan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-700 small">Foto Kerusakan (opsional, kosongkan jika tidak diubah)</label>
                @if($laporan->foto)
                <div class="mb-2">
                    <img src="{{ Storage::url($laporan->foto) }}" class="img-thumbnail" style="max-height:120px">
                    <div class="small text-muted mt-1">Foto saat ini</div>
                </div>
                @endif
                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('pelapor.laporan.show', $laporan->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>
@endsection