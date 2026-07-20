<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SI-JAKI &mdash; @yield('title', 'Login')</title>

    <script>
        (function() {
            try {
                const appSkin = localStorage.getItem('app-skin');
                const appSkinDark = localStorage.getItem('app-skin-dark');
                const theme = localStorage.getItem('theme');
                const shouldUseDark = appSkin === 'app-skin-dark' || appSkinDark === 'app-skin-dark' || theme === 'dark';

                document.documentElement.classList.toggle('app-skin-dark', shouldUseDark);
            } catch (error) {}
        })();
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo.png')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css')}}" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        :root {
            /* Updated Color Palette to use your custom blues */
            --primary-blue: #2B79B4;
            --secondary-blue: #3F96CD;
            
            --login-blue-900: #1a4d74; /* Darker variant of primary */
            --login-blue-800: var(--primary-blue);
            --login-blue-700: var(--primary-blue);
            --login-blue-600: var(--secondary-blue);
            --login-blue-500: var(--secondary-blue);
            --login-blue-soft: #eaf3f8; /* Soft version of primary */
            --login-blue-focus: rgba(43, 121, 180, 0.15);
            --login-text: #0f172a;
            --login-muted: #64748b;
            --login-border: #e2e8f0;
            --login-card: #ffffff;
            --login-field: #f8fafc;
            --login-shadow: 0 20px 40px -10px rgba(43, 121, 180, 0.1);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            color: var(--login-text);
        }

        .auth-cover-wrapper {
            background:
                radial-gradient(circle at 90% 10%, rgba(43, 121, 180, 0.07), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%);
        }

        /* ========================================================
           BAGIAN KIRI (BACKGROUND BIRU + PATTERN TUT WURI PUTIH)
           ======================================================== */
        .auth-cover-content-inner {
            background-color: var(--login-blue-800) !important;
            position: relative;
            overflow: hidden;
        }

        /* Gradient gelap agar pattern tidak terlalu menyilaukan */
        .auth-cover-content-inner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(26, 77, 116, 0.85) 0%, rgba(43, 121, 180, 0.70) 100%);
            z-index: 1;
        }

        /* Cetakan Logo Tut Wuri Putih */
        .auth-cover-content-inner::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            background-color: #ffffff !important;

            -webkit-mask-image: url("{{ asset('logo/tutwuri.png') }}");
            -webkit-mask-repeat: repeat;
            -webkit-mask-size: 80px;

            mask-image: url("{{ asset('logo/tutwuri.png') }}");
            mask-repeat: repeat;
            mask-size: 80px;

            opacity: 0.05; /* Transparansi pattern agar elegan */
        }

        .auth-cover-content-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
            z-index: 3; /* Pastikan teks ada di atas pattern */
        }

        /* Modern Glassmorphism Box */
        .glass-hero-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 50px 40px;
            border-radius: 24px;
            max-width: 480px;
            box-shadow: 0 24px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .glass-hero-box::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-20deg);
            animation: shine 6s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 200%; }
            100% { left: 200%; }
        }

        /* ========================================================
           BAGIAN KANAN (KOTAK LOGIN)
           ======================================================== */
        .auth-cover-sidebar-inner {
            background: transparent !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-cover-card-wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
        }

        /* Soft Glow Behind Card */
        .auth-cover-card-wrapper::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 90%;
            height: 90%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: rgba(43, 121, 180, 0.15);
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        .auth-cover-card {
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: var(--login-shadow) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px !important;
            padding: 3rem 2.5rem !important;
            display: flex;
            flex-direction: column;
            align-items: stretch !important;
            text-align: left !important;
        }

        .login-title {
            font-size: 1.75rem;
            line-height: 1.2;
            font-weight: 800;
            color: var(--login-text);
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--login-muted);
            font-weight: 500;
            margin-bottom: 2.5rem;
        }

        /* Modern Input Fields */
        .input-group.field {
            min-height: 56px;
            border: 1.5px solid var(--login-border);
            border-radius: 14px;
            overflow: hidden;
            background: var(--login-field);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-group.field:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .input-group.field:focus-within {
            border-color: var(--login-blue-600);
            background: #ffffff;
            box-shadow: 0 0 0 4px var(--login-blue-focus);
            transform: translateY(-2px);
        }

        .input-group.field .input-group-text {
            background: transparent !important;
            border: none !important;
            color: #94a3b8;
            padding-left: 20px;
            padding-right: 12px;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .input-group.field:focus-within .input-group-text {
            color: var(--login-blue-700);
        }

        .input-group.field .form-control {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 14px 20px 14px 0;
            color: var(--login-text) !important;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .input-group.field .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .input-group.field .show-pass {
            padding-right: 20px;
            padding-left: 12px;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .input-group.field .show-pass:hover {
            color: var(--login-blue-700);
            transform: scale(1.1);
        }

        /* Modern Button */
        .btn-login {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--login-blue-600) 0%, var(--login-blue-800) 100%) !important;
            border: none !important;
            color: #ffffff !important;
            padding: 16px 24px !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            border-radius: 14px !important;
            box-shadow: 0 10px 20px -5px rgba(43, 121, 180, 0.4) !important;
            transition: all 0.3s ease !important;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(43, 121, 180, 0.5) !important;
            filter: brightness(1.1);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 5px 10px -5px rgba(43, 121, 180, 0.4) !important;
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--login-muted) !important;
        }

        .auth-footer a {
            color: var(--login-blue-600);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: var(--login-blue-800);
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .developer-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: var(--login-muted) !important;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            background: var(--login-field);
            transition: all 0.2s ease;
        }

        .developer-link:hover {
            color: var(--login-blue-700) !important;
            background: var(--login-blue-soft);
            transform: translateY(-2px);
        }

        /* =========================================================
           FIX THEME SETTINGS (AGAR MUNCUL & TIDAK TERCROP)
        ========================================================= */
        .theme-customizer {
            z-index: 99999 !important;
            display: block !important;
        }
        .theme-customizer .customizer-handle {
            z-index: 999999 !important;
        }
        .theme-customizer .customizer-handle a {
            background-color: var(--primary-blue) !important;
            color: white !important;
        }
        .theme-customizer .customizer-sidebar-wrapper {
            z-index: 999999 !important;
        }
        .theme-customizer .theme-options-set {
            overflow: visible !important; 
            margin-top: 24px !important; 
        }
        .theme-customizer .theme-options-set label.options-label {
            z-index: 10 !important;
            white-space: nowrap !important;
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            html, body { height: 100%; margin: 0; overflow: hidden; }
            .auth-cover-wrapper {
                display: flex; flex-direction: column; height: 100vh;
                background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%);
            }
            .auth-cover-content-inner { display: none; }
            .auth-cover-sidebar-inner {
                flex-grow: 1; display: flex; align-items: center; justify-content: center;
                padding: 20px;
            }
            .auth-cover-card-wrapper { max-width: 420px; }
            .auth-cover-card {
                padding: 2.5rem 2rem !important;
                border-radius: 24px !important;
            }
        }

        @media (max-width: 575.98px) {
            .auth-cover-sidebar-inner { padding: 16px; }
            .auth-cover-card { padding: 2rem 1.5rem !important; border-radius: 20px !important; }
            .input-group.field { min-height: 50px; border-radius: 12px; }
            .btn-login { padding: 14px 20px !important; border-radius: 12px !important; }
            .login-title { font-size: 1.5rem; }
        }

        /* ========================================================
           DARK MODE CONFIGURATION
           ======================================================== */
        html.app-skin-dark, html.app-skin-dark body {
            background: #0f172a !important; color: #f8fafc !important;
        }

        html.app-skin-dark .auth-cover-wrapper {
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%) !important;
        }

        html.app-skin-dark .auth-cover-content-inner {
            background-color: #020617 !important;
        }

        html.app-skin-dark .auth-cover-content-inner::before {
            background: linear-gradient(135deg, rgba(2, 6, 23, 0.9) 0%, rgba(15, 23, 42, 0.8) 100%);
        }

        html.app-skin-dark .auth-cover-content-inner::after {
            opacity: 0.05; 
        }

        html.app-skin-dark .glass-hero-box {
            background: rgba(15, 23, 42, 0.4);
            border-color: rgba(255, 255, 255, 0.08);
        }

        html.app-skin-dark .auth-cover-card {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.5) !important;
        }

        html.app-skin-dark .login-title { color: #f8fafc !important; }
        html.app-skin-dark .login-subtitle, html.app-skin-dark .auth-footer { color: #94a3b8 !important; }

        html.app-skin-dark .input-group.field {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        html.app-skin-dark .input-group.field:hover { border-color: #475569 !important; background: #2dd4bf05 !important; }
        html.app-skin-dark .input-group.field:focus-within {
            background: #0f172a !important;
            border-color: var(--login-blue-500) !important;
            box-shadow: 0 0 0 4px rgba(43, 121, 180, 0.15);
        }

        html.app-skin-dark .input-group.field .form-control { color: #f8fafc !important; }
        html.app-skin-dark .input-group.field .form-control::placeholder { color: #64748b !important; }
        html.app-skin-dark .developer-link { background: #1e293b; border: 1px solid #334155; color: #cbd5e1 !important;}
        html.app-skin-dark .developer-link:hover { background: #334155; color: #ffffff !important;}
    </style>
</head>

<body>
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper text-center px-4">
                <div class="glass-hero-box">
                    <div class="d-flex justify-content-center mb-4">
                        <img src="{{ asset('logo/Logo-LLDikti-Wilayah-III-08.png') }}" style="height: 70px; object-fit: contain;" alt="LLDIKTI 3">
                    </div>
                    <h1 class="fw-black text-white mb-2" style="font-size: 2.5rem; letter-spacing: 2px; font-weight: 900;">SI-JAKI</h1>
                    <p class="fs-15 fw-semibold text-white mb-2" style="letter-spacing: 0.5px;">Sistem Informasi Jejak Pembinaan Perguruan Tinggi</p>
                    <div class="mt-4 pt-3 border-top border-light border-opacity-25">
                        <p class="fs-13 text-white-50 mb-0 fw-medium">LLDIKTI Wilayah III DKI Jakarta</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card">
                    <h2 class="login-title">Selamat Datang!</h2>
                    <p class="login-subtitle">Silakan masuk untuk mengelola data pembinaan perguruan tinggi Anda.</p>

                    <form action="{{ route('login.submit') }}" method="POST" class="w-100">
                        @csrf
                        <div class="mb-4">
                            <div class="input-group field">
                                <div class="input-group-text"><i class="feather-user"></i></div>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       id="nameInput"
                                       placeholder="Username"
                                       required
                                       value="{{ old('name') }}">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block mt-2 fs-12 text-danger fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="input-group field">
                                <div class="input-group-text"><i class="feather-lock"></i></div>
                                <input type="password" name="password" class="form-control password @error('password') is-invalid @enderror"
                                       id="passwordInput"
                                       placeholder="Password"
                                       required>
                                <div class="input-group-text c-pointer show-pass" id="togglePassword"><i class="feather-eye-off" id="passwordToggleIcon"></i></div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-2 fs-12 text-danger fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-login w-100">MASUK APLIKASI</button>
                        </div>
                    </form>

                    <div class="auth-footer">
                        <span>Lupa Password? </span><a href="#">Hubungi Admin</a>
                        <br>
                        <a href="{{ route('developer') }}" class="developer-link mt-4">
                            <i class="feather-code"></i> Halaman Pengembang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="theme-customizer">
        <div class="customizer-handle">
            <a href="javascript:void(0);" class="cutomizer-open-trigger">
                <i class="feather-settings"></i>
            </a>
        </div>
        <div class="customizer-sidebar-wrapper">
            <div class="customizer-sidebar-header px-4 ht-80 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Theme Settings</h5>
                <a href="javascript:void(0);" class="cutomizer-close-trigger d-flex">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="customizer-sidebar-body position-relative p-4" data-scrollbar-target="#psScrollbarInit">
                
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Skins</label>
                    <div class="row g-2 theme-options-items app-skin" id="appSkinList">
                        <div class="col-6 text-center position-relative single-option light-button active">
                            <input type="radio" class="btn-check" id="app-skin-light" name="app-skin" value="1" data-app-skin="app-skin-light">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-light">Light</label>
                        </div>
                        <div class="col-6 text-center position-relative single-option dark-button">
                            <input type="radio" class="btn-check" id="app-skin-dark" name="app-skin" value="2" data-app-skin="app-skin-dark">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-0 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Typography</label>
                    <div class="row g-2 theme-options-items font-family" id="fontFamilyList">
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-lato" name="font-family" value="1" data-font-family="app-font-family-lato">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-lato">Lato</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-rubik" name="font-family" value="2" data-font-family="app-font-family-rubik">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-rubik">Rubik</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-inter" name="font-family" value="3" data-font-family="app-font-family-inter" checked>
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-inter">Inter</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-cinzel" name="font-family" value="4" data-font-family="app-font-family-cinzel">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-cinzel">Cinzel</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-nunito" name="font-family" value="6" data-font-family="app-font-family-nunito">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-nunito">Nunito</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto" name="font-family" value="7" data-font-family="app-font-family-roboto">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto">Roboto</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ubuntu" name="font-family" value="8" data-font-family="app-font-family-ubuntu">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ubuntu">Ubuntu</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-poppins" name="font-family" value="9" data-font-family="app-font-family-poppins">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-poppins">Poppins</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-raleway" name="font-family" value="10" data-font-family="app-font-family-raleway">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-raleway">Raleway</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-system-ui" name="font-family" value="11" data-font-family="app-font-family-system-ui">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-system-ui">System UI</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-noto-sans" name="font-family" value="12" data-font-family="app-font-family-noto-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-noto-sans">Noto Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-fira-sans" name="font-family" value="13" data-font-family="app-font-family-fira-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-fira-sans">Fira Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-work-sans" name="font-family" value="14" data-font-family="app-font-family-work-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-work-sans">Work Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-open-sans" name="font-family" value="15" data-font-family="app-font-family-open-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-open-sans">Open Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-maven-pro" name="font-family" value="16" data-font-family="app-font-family-maven-pro">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-maven-pro">Maven Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-quicksand" name="font-family" value="17" data-font-family="app-font-family-quicksand">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-quicksand">Quicksand</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat" name="font-family" value="18" data-font-family="app-font-family-montserrat">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat">Montserrat</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-josefin-sans" name="font-family" value="19" data-font-family="app-font-family-josefin-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-josefin-sans">Josefin Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-ibm-plex-sans" name="font-family" value="20" data-font-family="app-font-family-ibm-plex-sans">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ibm-plex-sans">IBM Plex Sans</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-source-sans-pro" name="font-family" value="5" data-font-family="app-font-family-source-sans-pro">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-source-sans-pro">Source Sans Pro</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-montserrat-alt" name="font-family" value="21" data-font-family="app-font-family-montserrat-alt">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat-alt">Montserrat Alt</label>
                        </div>
                        <div class="col-6 text-center single-option">
                            <input type="radio" class="btn-check" id="app-font-family-roboto-slab" name="font-family" value="22" data-font-family="app-font-family-roboto-slab">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto-slab">Roboto Slab</label>
                        </div>
                    </div>
                </div>
                </div>
            <div class="customizer-sidebar-footer px-4 ht-60 border-top d-flex align-items-center gap-2">
                <div class="flex-fill w-50">
                    <a href="javascript:void(0);" class="btn btn-danger" data-style="reset-all-common-style">Reset</a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{ asset('assets/js/common-init.min.js')}}"></script>
    <script src="{{ asset('assets/js/theme-customizer-init.min.js')}}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');

            togglePasswordButton.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordToggleIcon.classList.remove('feather-eye-off');
                    passwordToggleIcon.classList.add('feather-eye');
                } else {
                    passwordInput.type = 'password';
                    passwordToggleIcon.classList.remove('feather-eye');
                    passwordToggleIcon.classList.add('feather-eye-off');
                }
                passwordInput.focus();
            });
        });
    </script>
</body>
</html>