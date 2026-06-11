@extends('layouts.app')
@section('title','Edit User')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i>Edit User</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="table-card">
    <div class="table-card-header"><h6><i class="bi bi-person-fill me-2"></i>Edit: {{ $user->name }}</h6></div>
    <div class="p-4">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        @if($errors->any())
        <div class="alert alert-danger small py-2"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-700 small">Nama *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">No HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Role *</label>
                <select name="role" class="form-select" required>
                    @foreach(['pelapor','teknisi','admin','pimpinan'] as $r)
                    <option value="{{ $r }}" {{ old('role', $user->role) == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Site</label>
                <select name="site" class="form-select">
                    <option value="">-- Pilih --</option>
                    @foreach(['Duri','Minas','Rumbai','Petapahan','Libo','Rangau','Batang','Bangko','Pager','Pinang','Dumai'] as $s)
                    <option value="{{ $s }}" {{ old('site', $user->site) == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-700 small">Password Baru <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $user->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-700 small" for="isActive">Akun Aktif</label>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
            <a href="{{ route('admin.users.index', ['role'=>$user->role]) }}" class="btn btn-outline-secondary px-4">Batal</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>
@endsection