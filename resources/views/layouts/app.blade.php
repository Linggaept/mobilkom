<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pelaporan Radio Trunking') – PT Mobilkom</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --sidebar-w: 260px;
            --header-h: 64px;
            --primary: #0d3b8e;
            --primary-dark: #092d6b;
            --accent: #e8a020;
            --success: #198754;
            --sidebar-bg: #0d3b8e;
            --sidebar-text: rgba(255,255,255,0.85);
            --sidebar-active: rgba(255,255,255,0.18);
            --body-bg: #f0f4f8;
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

        body { background: var(--body-bg); margin: 0; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1030;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            transition: transform .3s ease;
        }
        .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex; align-items: center; gap: 10px;
            min-height: var(--header-h);
        }
        .sidebar-header .brand-text { color: #fff; font-weight: 800; font-size: .95rem; line-height: 1.2; }
        .sidebar-header .brand-sub  { color: rgba(255,255,255,.6); font-size: .72rem; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }
        .sidebar-section { padding: 10px 20px 4px; color: rgba(255,255,255,.4); font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 20px; color: var(--sidebar-text);
            text-decoration: none; font-size: .875rem; font-weight: 500;
            border-radius: 0; transition: all .2s; margin: 1px 8px; border-radius: 8px;
        }
        .sidebar-link:hover  { background: var(--sidebar-active); color: #fff; }
        .sidebar-link.active { background: var(--accent); color: #fff; font-weight: 700; }
        .sidebar-link .bi    { font-size: 1.1rem; flex-shrink: 0; }
        .sidebar-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,.1); }
        .sidebar-footer .user-info { color: #fff; font-size: .82rem; font-weight: 600; }
        .sidebar-footer .user-role { color: rgba(255,255,255,.5); font-size: .73rem; }

        /* ── Header ── */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--header-h);
            background: #fff;
            display: flex; align-items: center;
            padding: 0 12px;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            z-index: 1020;
        }
        .topbar-left  { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; overflow: hidden; }
        .topbar-center { display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .topbar-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: auto; }
        .topbar-logos { display: flex; align-items: center; gap: 8px; min-width: 0; overflow: hidden; }
        .topbar-logos img { height: 34px; max-width: 110px; object-fit: contain; flex-shrink: 0; }
        .topbar-logos .divider { width: 1px; height: 26px; background: #e0e0e0; flex-shrink: 0; }
        .topbar-title { font-weight: 700; color: var(--primary); font-size: .85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: none; }
        @media (min-width: 992px) { .topbar-title { display: block; } }
        .topbar-center img { height: 34px; max-width: 120px; object-fit: contain; }
        @media (max-width: 640px) { .topbar-center { display: none; } }

        /* ── Notification Bell ── */
        .notif-btn {
            position: relative; background: none; border: none; padding: 8px;
            border-radius: 50%; cursor: pointer; color: #555; font-size: 1.3rem;
            transition: background .2s;
        }
        .notif-btn:hover { background: #f0f4f8; }
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            background: #dc3545; color: #fff;
            font-size: .6rem; font-weight: 700;
            min-width: 16px; height: 16px;
            border-radius: 8px; padding: 0 4px;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }
        .notif-dropdown {
            position: fixed; right: 16px; top: 70px;
            width: 360px; max-height: 480px;
            background: #fff; border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,.15);
            z-index: 9999; overflow: hidden;
            display: none;
        }
        .notif-dropdown.show { display: block; }
        .notif-dropdown-header {
            padding: 14px 18px; border-bottom: 1px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
            font-weight: 700; font-size: .9rem; color: var(--primary);
        }
        .notif-list { overflow-y: auto; max-height: 380px; }
        .notif-item {
            padding: 12px 18px; border-bottom: 1px solid #f7f7f7;
            cursor: pointer; transition: background .15s;
        }
        .notif-item:hover  { background: #f8f9ff; }
        .notif-item.unread { background: #eff5ff; }
        .notif-item .notif-msg  { font-size: .82rem; font-weight: 600; color: #333; margin-bottom: 2px; }
        .notif-item .notif-time { font-size: .72rem; color: #999; }
        .notif-empty { padding: 32px; text-align: center; color: #bbb; font-size: .85rem; }

        /* ── Main content ── */
        .main-content {
            margin-left: var(--sidebar-w);
            padding-top: calc(var(--header-h) + 24px);
            padding-left: 24px; padding-right: 24px; padding-bottom: 32px;
            min-height: 100vh;
        }

        /* ── Cards & stats ── */
        .stat-card {
            border: none; border-radius: 14px; padding: 22px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
        .stat-card .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-card .stat-val   { font-size: 2rem; font-weight: 800; }
        .stat-card .stat-label { font-size: .8rem; color: #666; font-weight: 500; }

        /* ── Tables ── */
        .table-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
        .table-card-header { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
        .table-card-header h6 { margin: 0; font-weight: 700; font-size: .95rem; color: var(--primary); }
        .table > :not(:first-child) { border-top: none; }
        .table th { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #888; font-weight: 700; }
        .table td { font-size: .855rem; vertical-align: middle; }

        /* ── Badges ── */
        .badge { font-size: .72rem; font-weight: 600; padding: 5px 10px; border-radius: 6px; }

        /* ── Breadcrumb ── */
        .page-header { margin-bottom: 20px; }
        .page-header h4 { font-weight: 800; color: var(--primary); margin: 0; }
        .page-header .breadcrumb { margin: 0; font-size: .8rem; }

        /* ── Sidebar toggle for mobile ── */
        .sidebar-toggle { display: none; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: flex; }
        }

        /* ── Overlay for mobile ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1025;
        }
        .sidebar-overlay.show { display: block; }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="brand-text">Radio Trunking</div>
            <div class="brand-sub">PT Mobilkom – PHR</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->isPelapor())
            <div class="sidebar-section">Menu</div>
            <a href="{{ route('pelapor.dashboard') }}" class="sidebar-link {{ request()->routeIs('pelapor.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a href="{{ route('pelapor.laporan.create') }}" class="sidebar-link {{ request()->routeIs('pelapor.laporan.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i> Buat Laporan
            </a>
            <a href="{{ route('pelapor.laporan.index') }}" class="sidebar-link {{ request()->routeIs('pelapor.laporan.index') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Laporan Saya
            </a>
        @elseif(auth()->user()->isTeknisi())
            <div class="sidebar-section">Menu</div>
            <a href="{{ route('teknisi.dashboard') }}" class="sidebar-link {{ request()->routeIs('teknisi.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a href="{{ route('teknisi.laporan.index') }}" class="sidebar-link {{ request()->routeIs('teknisi.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Laporan Tugas
            </a>
        @elseif(auth()->user()->isAdmin())
            <div class="sidebar-section">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-data-fill"></i> Kelola Laporan
            </a>
            <div class="sidebar-section">Manajemen</div>
            <a href="{{ route('admin.users.index', ['role'=>'pelapor']) }}" class="sidebar-link {{ request()->routeIs('admin.users.*') && request('role','pelapor') === 'pelapor' ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Kelola Pelapor
            </a>
            <a href="{{ route('admin.users.index', ['role'=>'teknisi']) }}" class="sidebar-link {{ request()->routeIs('admin.users.*') && request('role') === 'teknisi' ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Kelola Teknisi
            </a>
            <a href="{{ route('admin.users.index', ['role'=>'pimpinan']) }}" class="sidebar-link {{ request()->routeIs('admin.users.*') && request('role') === 'pimpinan' ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Kelola Pimpinan
            </a>
        @elseif(auth()->user()->isPimpinan())
            <div class="sidebar-section">Menu</div>
            <a href="{{ route('pimpinan.dashboard') }}" class="sidebar-link {{ request()->routeIs('pimpinan.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>
            <a href="{{ route('pimpinan.laporan.index') }}" class="sidebar-link {{ request()->routeIs('pimpinan.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Monitoring Laporan
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                <i class="bi bi-person-fill text-white"></i>
            </div>
            <div class="overflow-hidden">
                <div class="user-info text-truncate">{{ auth()->user()->name }}</div>
                <div class="user-role text-capitalize">{{ auth()->user()->role }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm btn-link p-0 text-white opacity-60" title="Logout">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Topbar -->
<header class="topbar">
    {{-- Left --}}
    <div class="topbar-left">
        <button class="btn btn-sm btn-outline-secondary sidebar-toggle" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-logos">
            <img src="{{ asset('logo/mobilkom.png') }}" alt="Mobilkom" onerror="this.style.display='none'">
            <div class="divider d-none d-md-block"></div>
            <span class="topbar-title">Sistem Pelaporan Radio Trunking</span>
        </div>
    </div>

    {{-- Center --}}
    <div class="topbar-center">
        <img src="{{ asset('logo/pertaminahulurokan.png') }}" alt="Pertamina Hulu Rokan" onerror="this.style.display='none'">
    </div>

    {{-- Right --}}
    <div class="topbar-right">
        <button class="notif-btn" id="notifBtn" onclick="toggleNotif(event)" title="Notifikasi">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-badge d-none" id="notifCount">0</span>
        </button>

        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-md-inline"> {{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted">{{ ucfirst(auth()->user()->role) }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Notification Dropdown -->
<div class="notif-dropdown" id="notifDropdown">
    <div class="notif-dropdown-header">
        <span><i class="bi bi-bell-fill me-2"></i>Notifikasi</span>
        <button class="btn btn-sm btn-link p-0 text-muted" onclick="markAllRead()">Tandai semua dibaca</button>
    </div>
    <div class="notif-list" id="notifList">
        <div class="notif-empty"><i class="bi bi-bell-slash d-block fs-2 mb-2"></i>Tidak ada notifikasi</div>
    </div>
</div>

<!-- Main Content -->
<main class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ── Sidebar toggle ──
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

// ── Notification system ──
const _notifDropdown = document.getElementById('notifDropdown');
const _notifCount    = document.getElementById('notifCount');
const _notifList     = document.getElementById('notifList');

function toggleNotif(e) {
    e.stopPropagation();
    if (_notifDropdown) _notifDropdown.classList.toggle('show');
}
document.addEventListener('click', () => {
    if (_notifDropdown) _notifDropdown.classList.remove('show');
});
if (_notifDropdown) _notifDropdown.addEventListener('click', e => e.stopPropagation());

async function loadNotifications() {
    if (!_notifCount || !_notifList) return;
    try {
        const res = await fetch('{{ route("notifications.unread") }}');
        if (!res.ok || res.headers.get('content-type')?.indexOf('application/json') === -1) return;
        const data = await res.json();

        if (data.count > 0) {
            _notifCount.textContent = data.count > 99 ? '99+' : data.count;
            _notifCount.classList.remove('d-none');
        } else {
            _notifCount.classList.add('d-none');
        }

        if (data.notifications.length === 0) {
            _notifList.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash d-block fs-2 mb-2"></i>Tidak ada notifikasi baru</div>';
            return;
        }

        _notifList.innerHTML = data.notifications.map(n => `
            <div class="notif-item unread" onclick="readNotif('${n.id}', '${n.data.url || '#'}')">
                <div class="notif-msg">${n.data.pesan}</div>
                <div class="notif-time"><i class="bi bi-clock me-1"></i>${n.created_at}</div>
            </div>
        `).join('');
    } catch(e) { /* session expired or network error, silently ignore */ }
}

async function readNotif(id, url) {
    await fetch('{{ route("notifications.read") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ id })
    });
    if (url && url !== '#') window.location.href = url;
    loadNotifications();
}

async function markAllRead() {
    await fetch('{{ route("notifications.read") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({})
    });
    loadNotifications();
    document.getElementById('notifDropdown').classList.remove('show');
}

// Load on start and poll every 30s
loadNotifications();
setInterval(loadNotifications, 30000);

// ── SweetAlert confirm delete ──
function confirmDelete(formId, msg) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: msg || 'Data ini akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>

@stack('scripts')
</body>
</html>