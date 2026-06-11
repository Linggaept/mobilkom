<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Sistem Pelaporan Radio Trunking PT Mobilkom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3b8e 0%, #1a5fbf 50%, #0a2d6e 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            width: 100%; max-width: 440px; overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #0d3b8e, #1a5fbf);
            padding: 28px 32px 24px; text-align: center;
        }
        .login-header .logos { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 16px; }
        .login-header .logos img { height: 48px; object-fit: contain; filter: brightness(0) invert(1); }
        .login-header .logo-divider { width: 1px; height: 40px; background: rgba(255,255,255,.4); }
        .login-header h5 { color: #fff; font-weight: 800; margin: 0; font-size: 1.1rem; }
        .login-header p  { color: rgba(255,255,255,.75); margin: 4px 0 0; font-size: .82rem; }
        .login-body { padding: 32px; }
        .login-body h6 { font-weight: 800; color: #0d3b8e; margin-bottom: 6px; font-size: 1.15rem; }
        .login-body .sub { color: #888; font-size: .83rem; margin-bottom: 24px; }
        .form-control, .form-select {
            border-radius: 10px; padding: 10px 14px;
            border: 1.5px solid #e0e0e0; font-size: .875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d3b8e; box-shadow: 0 0 0 3px rgba(13,59,142,.1);
        }
        .input-group-text { background: #f8f9fa; border-radius: 10px 0 0 10px; border: 1.5px solid #e0e0e0; }
        .btn-primary {
            background: #0d3b8e; border: none; border-radius: 10px;
            padding: 11px; font-weight: 700; font-size: .9rem;
            transition: background .2s, transform .15s;
        }
        .btn-primary:hover { background: #092d6b; transform: translateY(-1px); }
        .alert { border-radius: 10px; font-size: .85rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="logos">
            <img src="{{ asset('logo/mobilkom.png') }}" alt="Mobilkom" onerror="this.style.display='none'">
            <div class="logo-divider"></div>
            <img src="{{ asset('logo/pertaminahulurokan.png') }}" alt="Pertamina Hulu Rokan" onerror="this.style.display='none'">
        </div>
        <h5>Sistem Pelaporan Radio Trunking</h5>
        <p>PT Mobilkom – Pertamina Hulu Rokan</p>
    </div>
    <div class="login-body">
        <h6>Masuk ke Akun</h6>
        <p class="sub">Silakan masukkan email dan password Anda</p>

        @if($errors->any())
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-600 small">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill text-muted"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@mobilkom.com"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600 small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                        <i class="bi bi-eye-fill" id="passIcon"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <hr class="my-3">
        <p class="text-center small text-muted mb-0">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-decoration-none fw-600" style="color:#0d3b8e">Daftar disini</a>
        </p>

        <div class="mt-4 p-3 rounded-3 small" style="background:#f8f9ff; font-size:.75rem;">
            <div class="fw-700 text-muted mb-1">Akun Demo:</div>
            <div>Admin: <code>admin@mobilkom.com</code> / <code>admin123</code></div>
            <div>Teknisi: <code>teknisi1@mobilkom.com</code> / <code>teknisi123</code></div>
            <div>Pimpinan: <code>pimpinan@mobilkom.com</code> / <code>pimpinan123</code></div>
            <div>Pelapor: <code>pelapor@mobilkom.com</code> / <code>pelapor123</code></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
    const inp = document.getElementById('passwordInput');
    const ico = document.getElementById('passIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash-fill'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye-fill'; }
}
</script>
</body>
</html>