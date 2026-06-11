<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar – Sistem Pelaporan Radio Trunking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3b8e 0%, #1a5fbf 50%, #0a2d6e 100%);
            display: flex; align-items: center; justify-content: center;
            padding: 24px 16px;
        }
        .register-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            width: 100%; max-width: 500px; overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #0d3b8e, #1a5fbf);
            padding: 22px 32px 20px; text-align: center;
        }
        .register-header .logos { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 12px; }
        .register-header .logos img { height: 42px; object-fit: contain; filter: brightness(0) invert(1); }
        .register-header .logo-divider { width: 1px; height: 36px; background: rgba(255,255,255,.4); }
        .register-header h5 { color: #fff; font-weight: 800; margin: 0; font-size: 1rem; }
        .register-header p  { color: rgba(255,255,255,.7); margin: 3px 0 0; font-size: .78rem; }
        .register-body { padding: 28px 32px; }
        .form-control, .form-select {
            border-radius: 9px; padding: 9px 13px;
            border: 1.5px solid #e0e0e0; font-size: .855rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d3b8e; box-shadow: 0 0 0 3px rgba(13,59,142,.1);
        }
        .input-group-text { background: #f8f9fa; border: 1.5px solid #e0e0e0; font-size:.9rem; }
        .btn-primary {
            background: #0d3b8e; border: none; border-radius: 10px;
            padding: 11px; font-weight: 700; font-size: .9rem;
        }
        .btn-primary:hover { background: #092d6b; }
        .form-label { font-size: .82rem; font-weight: 700; color: #444; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="register-header">
        <div class="logos">
            <img src="{{ asset('logo/mobilkom.png') }}" alt="Mobilkom" onerror="this.style.display='none'">
            <div class="logo-divider"></div>
            <img src="{{ asset('logo/pertaminahulurokan.png') }}" alt="Pertamina Hulu Rokan" onerror="this.style.display='none'">
        </div>
        <h5>Daftar Akun Pelapor</h5>
        <p>Sistem Pelaporan Radio Trunking – PT Mobilkom</p>
    </div>
    <div class="register-body">
        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill text-muted"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap"
                               value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill text-muted"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="email@perusahaan.com"
                               value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone-fill text-muted"></i></span>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_hp') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Jabatan Anda"
                           value="{{ old('jabatan') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Site / Lokasi / Unit Kerja <span class="text-danger">*</span></label>
                    <select name="site" class="form-select" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach(['Duri','Minas','Rumbai','Petapahan','Libo','Rangau','Batang','Bangko','Pager','Pinang','Dumai'] as $s)
                            <option value="{{ $s }}" {{ old('site') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" name="password" id="pw1" class="form-control" placeholder="Min. 6 karakter" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePass('pw1','ico1')">
                            <i class="bi bi-eye-fill" id="ico1"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" name="password_confirmation" id="pw2" class="form-control" placeholder="Ulangi password" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePass('pw2','ico2')">
                            <i class="bi bi-eye-fill" id="ico2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-4">
                <i class="bi bi-person-plus-fill me-2"></i>Daftar Sekarang
            </button>
        </form>
        <hr class="my-3">
        <p class="text-center small text-muted mb-0">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-decoration-none fw-700" style="color:#0d3b8e">Masuk disini</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, ico) {
    const inp = document.getElementById(id);
    const ic  = document.getElementById(ico);
    if (inp.type === 'password') { inp.type = 'text'; ic.className = 'bi bi-eye-slash-fill'; }
    else { inp.type = 'password'; ic.className = 'bi bi-eye-fill'; }
}
</script>
</body>
</html>