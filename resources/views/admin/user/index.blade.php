@extends('layouts.app')
@section('title','Kelola User')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Kelola {{ ucfirst($role) }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelola {{ ucfirst($role) }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-person-plus me-1"></i>Tambah User
    </a>
</div>

<!-- Role Tabs -->
<ul class="nav nav-pills mb-4 gap-1">
    @foreach(['pelapor','teknisi','admin','pimpinan'] as $r)
    <li class="nav-item">
        <a class="nav-link {{ $role === $r ? 'active' : '' }}" href="{{ route('admin.users.index', ['role'=>$r]) }}">
            {{ ucfirst($r) }}
        </a>
    </li>
    @endforeach
</ul>

<!-- Search -->
<div class="table-card mb-4">
    <div class="p-3">
        <form method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="role" value="{{ $role }}">
            <div class="col-md-6"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / email..." value="{{ request('search') }}"></div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('admin.users.index', ['role'=>$role]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-list-ul me-2"></i>Daftar {{ ucfirst($role) }}</h6>
        <small class="text-muted">Total: {{ $users->total() }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nama</th><th>Email</th><th>No HP</th><th>Jabatan</th><th>Site</th><th>Status</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td><small>{{ $users->firstItem() + $i }}</small></td>
                    <td><span class="fw-700 small">{{ $u->name }}</span></td>
                    <td><small class="text-muted">{{ $u->email }}</small></td>
                    <td><small>{{ $u->no_hp ?? '-' }}</small></td>
                    <td><small>{{ $u->jabatan ?? '-' }}</small></td>
                    <td><small>{{ $u->site ?? '-' }}</small></td>
                    <td>
                        <span class="badge bg-{{ $u->is_active ? 'success' : 'secondary' }}">
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-{{ $u->is_active ? 'secondary' : 'success' }} py-0 px-2"
                                    title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi bi-{{ $u->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                            @if($u->id !== auth()->id())
                            <button onclick="confirmDelete('del-{{ $u->id }}','Hapus user {{ $u->name }}?')"
                                    class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus"><i class="bi bi-trash"></i></button>
                            <form id="del-{{ $u->id }}" action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox d-block fs-3 mb-2"></i>Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-3 border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection