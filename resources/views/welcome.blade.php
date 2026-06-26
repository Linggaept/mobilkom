<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelaporan Kerusakan Radio Trunking – PT Mobilkom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Slideshow */
        .bg-slideshow {
            position: fixed; inset: 0; z-index: 0;
        }
        .bg-slide {
            position: absolute; inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
        }
        .bg-slide.active { opacity: 1; }
        .bg-slide:nth-child(1) { background-image: url('{{ asset('bg-1.jpeg') }}'); }
        .bg-slide:nth-child(2) { background-image: url('{{ asset('bg-2.jpeg') }}'); }

        /* Overlay */
        .bg-overlay {
            position: fixed; inset: 0; z-index: 1;
            background: linear-gradient(
                135deg,
                rgba(8, 30, 80, 0.82) 0%,
                rgba(10, 45, 110, 0.75) 50%,
                rgba(5, 20, 60, 0.88) 100%
            );
        }

        /* Content */
        .landing-wrap {
            position: relative; z-index: 2;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }

        .landing-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 52px 48px;
            max-width: 560px;
            width: 100%;
            text-align: center;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.4);
        }

        /* Logos */
        .logo-row {
            display: flex; align-items: center; justify-content: center;
            gap: 20px; margin-bottom: 36px;
        }
        .logo-row img {
            height: 52px; object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.95;
        }
        .logo-divider {
            width: 1px; height: 44px;
            background: rgba(255, 255, 255, 0.35);
        }

        /* Badge */
        .org-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 100px;
            padding: 5px 16px;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 0.4px;
            font-weight: 500;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .org-badge i { font-size: 0.7rem; }

        /* Title */
        .landing-title {
            color: #fff;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }
        .landing-title span { color: #60a5fa; }

        .landing-sub {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        /* Divider */
        .section-divider {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 28px;
        }
        .section-divider hr { flex: 1; border-color: rgba(255,255,255,0.15); }
        .section-divider span { color: rgba(255,255,255,0.4); font-size: 0.75rem; }

        /* Buttons */
        .btn-landing-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #fff;
            color: #0d3b8e;
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            width: 100%;
            margin-bottom: 12px;
        }
        .btn-landing-primary:hover {
            background: #e8f0fe;
            color: #0a2d6e;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255,255,255,0.15);
        }

        .btn-landing-secondary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s, transform 0.15s;
            width: 100%;
        }
        .btn-landing-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.7);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Footer note */
        .landing-footer {
            margin-top: 32px;
            color: rgba(255,255,255,0.35);
            font-size: 0.72rem;
        }

        /* Slide indicator */
        .slide-dots {
            position: fixed; bottom: 28px; left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex; gap: 8px;
        }
        .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        .dot.active {
            background: #fff;
            transform: scale(1.3);
        }

        @media (max-width: 480px) {
            .landing-card { padding: 36px 28px; }
            .landing-title { font-size: 1.45rem; }
            .logo-row img { height: 40px; }
        }
    </style>
</head>
<body>

    <!-- Background Slideshow -->
    <div class="bg-slideshow">
        <div class="bg-slide active"></div>
        <div class="bg-slide"></div>
    </div>
    <div class="bg-overlay"></div>

    <!-- Content -->
    <div class="landing-wrap">
        <div class="landing-card">

            <div class="logo-row">
                <img src="{{ asset('logo/mobilkom.png') }}" alt="Mobilkom" onerror="this.style.display='none'">
                <div class="logo-divider"></div>
                <img src="{{ asset('logo/pertaminahulurokan.png') }}" alt="Pertamina Hulu Rokan" onerror="this.style.display='none'">
            </div>

            <div class="org-badge">
                <i class="bi bi-broadcast-pin"></i>
                PT Mobilkom &middot; Pertamina Hulu Rokan
            </div>

            <h1 class="landing-title">
                Sistem Pelaporan<br>
                Kerusakan <span>Radio Trunking</span>
            </h1>

            <p class="landing-sub">
                Selamat datang di platform pelaporan kerusakan perangkat radio trunking.
                Laporkan gangguan, pantau status perbaikan, dan kelola penugasan teknisi secara digital.
            </p>

            <div class="section-divider">
                <hr><span>Akses Sistem</span><hr>
            </div>

            <a href="{{ route('login') }}" class="btn-landing-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk ke Sistem
            </a>

            <a href="{{ route('register') }}" class="btn-landing-secondary">
                <i class="bi bi-person-plus"></i>
                Daftar Akun Baru
            </a>

            <p class="landing-footer">
                &copy; {{ date('Y') }} PT Mobilkom &ndash; Pertamina Hulu Rokan. Hak cipta dilindungi.
            </p>

        </div>
    </div>

    <!-- Slide Dots -->
    <div class="slide-dots">
        <div class="dot active" onclick="goSlide(0)"></div>
        <div class="dot" onclick="goSlide(1)"></div>
    </div>

    <script>
        const slides = document.querySelectorAll('.bg-slide');
        const dots   = document.querySelectorAll('.dot');
        let current  = 0;
        let timer;

        function goSlide(n) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = n;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
            resetTimer();
        }

        function nextSlide() {
            goSlide((current + 1) % slides.length);
        }

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, 5000);
        }

        resetTimer();
    </script>
</body>
</html>
