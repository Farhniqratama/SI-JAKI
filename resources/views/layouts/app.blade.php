<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI-JAKI &mdash; @yield('title', 'Dashboard')</title>

   <script>
        (function() {
            function applyTheme() {
                try {
                    var theme = localStorage.getItem('theme');
                    var skin = localStorage.getItem('skin');
                    var bsTheme = localStorage.getItem('data-bs-theme');
                    
                    // Cek apakah benar-benar mode malam
                    var isDark = (theme === 'dark' || skin === 'dark' || bsTheme === 'dark');
                    var html = document.documentElement;

                    // 1. Atur tag <html> langsung saat inisialisasi
                    if (isDark) {
                        html.classList.add('app-skin-dark', 'dark');
                        html.setAttribute('data-bs-theme', 'dark');
                    } else {
                        // Tuntaskan pembersihan class gelap di HTML agar tidak mempengaruhi mode siang
                        html.classList.remove('app-skin-dark', 'dark', 'dark-mode');
                        html.setAttribute('data-bs-theme', 'light');
                    }
                } catch (e) {}
            }

            // Eksekusi secepat mungkin sebelum render
            applyTheme();

            // 2. Bersihkan ulang tag <html> dan <body> setelah elemen tersedia 
            // (Mencegah script eksternal/customizer menimpa pengaturan)
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    var theme = localStorage.getItem('theme');
                    var skin = localStorage.getItem('skin');
                    var bsTheme = localStorage.getItem('data-bs-theme');
                    var isDark = (theme === 'dark' || skin === 'dark' || bsTheme === 'dark');
                    
                    var html = document.documentElement;
                    var body = document.body;

                    if (!isDark) {
                        // Sapu bersih SEMUA class gelap yang mungkin "nyangkut"
                        html.classList.remove('app-skin-dark', 'dark', 'dark-mode');
                        html.setAttribute('data-bs-theme', 'light');
                        
                        body.classList.remove('app-skin-dark', 'dark', 'dark-mode', 'bg-dark');
                        body.setAttribute('data-bs-theme', 'light');
                    } else {
                        // Pastikan class gelap terpasang sempurna jika mode malam
                        html.classList.add('app-skin-dark', 'dark');
                        html.setAttribute('data-bs-theme', 'dark');
                        body.classList.add('app-skin-dark', 'dark');
                    }
                } catch (e) {}
            });
        })();
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo.png')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/dataTables.bs5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css')}}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            /* Warna Biru Baru Sesuai Permintaan */
            --primary-blue: #2B79B4;
            --secondary-blue: #3F96CD;

            --sijaki-page-bg: #ffffff;
            --sijaki-card-bg: #ffffff;
            --sijaki-soft-bg: #f8fafc;
            --sijaki-border-color: rgba(15, 23, 42, 0.08);
            --sijaki-text-color: #0f172a;
            --sijaki-muted-color: #64748b;
            --sijaki-pattern-opacity: 0.045;
            --sijaki-pattern-filter: grayscale(1) brightness(3.2);

            --sijaki-primary: var(--primary-blue);
            --sijaki-primary-2: var(--secondary-blue);
            --sijaki-primary-soft: rgba(43, 121, 180, 0.10);
            --sijaki-primary-border: rgba(43, 121, 180, 0.14);
            --sijaki-table-hover: #f1f7ff;
        }

        html.app-skin-dark,
        body.app-skin-dark,
        html[data-bs-theme="dark"],
        body[data-bs-theme="dark"],
        html.dark,
        body.dark,
        body.dark-mode {
            --sijaki-page-bg: #020617;
            --sijaki-card-bg: #0f172a;
            --sijaki-soft-bg: #111827;
            --sijaki-border-color: rgba(148, 163, 184, 0.18);
            --sijaki-text-color: #e5e7eb;
            --sijaki-muted-color: #94a3b8;
            --sijaki-pattern-opacity: 0.035;
            --sijaki-pattern-filter: grayscale(1) brightness(0.75) invert(1);

            --sijaki-primary-soft: rgba(63, 150, 205, 0.16);
            --sijaki-primary-border: rgba(63, 150, 205, 0.24);
            --sijaki-table-hover: #111827;
        }

        html,
        body {
            min-height: 100%;
            background-color: var(--sijaki-page-bg) !important;
        }

        body {
            position: relative;
            background: var(--sijaki-page-bg) !important;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: var(--sijaki-page-bg);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("{{ asset('logo/tutwuri.png') }}");
            background-repeat: repeat;
            background-size: 80px auto;
            background-position: top left;
            background-attachment: fixed;
            opacity: var(--sijaki-pattern-opacity);
            filter: var(--sijaki-pattern-filter);
            pointer-events: none;
            z-index: 0;
        }

        .nx-wrapper,
        .nx-main-container,
        .nxl-container,
        .nxl-content {
            position: relative;
            z-index: 1;
            background: transparent !important;
        }

        .card,
        .modal-content,
        .dropdown-menu,
        .customizer-sidebar-wrapper {
            background-color: var(--sijaki-card-bg) !important;
            border-color: var(--sijaki-border-color) !important;
        }

        .theme-options-set,
        .options-label {
            background-color: var(--sijaki-card-bg) !important;
            border-color: var(--sijaki-border-color) !important;
            color: var(--sijaki-text-color) !important;
        }

        .app-skin-dark .text-dark,
        [data-bs-theme="dark"] .text-dark,
        .dark .text-dark,
        .dark-mode .text-dark {
            color: var(--sijaki-text-color) !important;
        }

        .app-skin-dark .text-muted,
        [data-bs-theme="dark"] .text-muted,
        .dark .text-muted,
        .dark-mode .text-muted {
            color: var(--sijaki-muted-color) !important;
        }

        .loader-bg {
            z-index: 99999;
            background: var(--sijaki-page-bg) !important;
        }

        /* ==========================================================
           Tabel Styles (Biru Custom: #2B79B4 & #3F96CD)
           ========================================================== */

        .table,
        .dataTable {
            color: var(--sijaki-text-color);
        }

        .main-content .table-responsive {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
        }

        .main-content .table,
        .main-content .dataTable {
            margin-bottom: 0;
        }

        .main-content .table thead th,
        .main-content .dataTable thead th {
            color: #ffffff !important;
            background: var(--primary-blue) !important;
            border-color: var(--secondary-blue) !important;
            border-bottom: 1px solid var(--secondary-blue) !important;
            font-size: 0.73rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            white-space: nowrap;
        }

        .main-content .table tbody td,
        .main-content .dataTable tbody td {
            border-color: #e2e8f0 !important;
            vertical-align: middle;
        }

        .main-content .table-striped > tbody > tr:nth-of-type(odd) > *,
        .main-content .dataTable.stripe > tbody > tr:nth-of-type(odd) > * {
            background-color: #f8fafc !important;
        }

        .main-content .table-hover > tbody > tr:hover > *,
        .main-content .dataTable.hover > tbody > tr:hover > * {
            color: #0f172a !important;
            background-color: #f1f5f9 !important;
        }

        .main-content .table .badge,
        .main-content .dataTable .badge {
            border: 1px solid #cbd5e1;
            background: #ffffff !important;
            color: var(--primary-blue) !important;
            font-weight: 800;
        }

        .main-content .table-responsive,
        .main-content .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        html.app-skin-dark .table,
        html.app-skin-dark .dataTable,
        html.app-skin-dark .table > :not(caption) > * > * {
            color: #e2e8f0 !important;
            border-color: #1e293b !important;
            background-color: transparent !important;
        }

        html.app-skin-dark .main-content .table-responsive {
            border-color: var(--primary-blue) !important;
            background: #0f172a !important;
        }

        html.app-skin-dark .main-content .table thead th,
        html.app-skin-dark .main-content .dataTable thead th {
            color: #dbeafe !important;
            background: var(--primary-blue) !important;
            border-color: var(--secondary-blue) !important;
        }

        html.app-skin-dark .main-content .table tbody td,
        html.app-skin-dark .main-content .dataTable tbody td {
            border-color: #1e293b !important;
        }

        html.app-skin-dark .main-content .table-striped > tbody > tr:nth-of-type(odd) > *,
        html.app-skin-dark .main-content .dataTable.stripe > tbody > tr:nth-of-type(odd) > * {
            background-color: #0b1224 !important;
        }

        html.app-skin-dark .main-content .table-hover > tbody > tr:hover > *,
        html.app-skin-dark .main-content .dataTable.hover > tbody > tr:hover > * {
            color: #f8fafc !important;
            background-color: #111827 !important;
        }

        html.app-skin-dark .main-content .table .badge,
        html.app-skin-dark .main-content .dataTable .badge {
            border-color: var(--primary-blue) !important;
            background: #073b6f !important; /* Warna fallback opsional, bisa diganti soft blue gelap */
            color: #dbeafe !important;
        }

        /* Fresh app-wide visual refresh overrides */
        .main-content .table-responsive {
            border-color: #dbe7ef;
            border-radius: 14px;
        }

        .main-content .table thead th,
        .main-content .dataTable thead th {
            background: var(--primary-blue) !important;
            border-color: var(--secondary-blue) !important;
        }

        .main-content .table .badge,
        .main-content .dataTable .badge {
            border-color: #b9dcff;
            background: #eef7ff !important;
            color: var(--primary-blue) !important;
        }

        html.app-skin-dark .main-content .table thead th,
        html.app-skin-dark .main-content .dataTable thead th {
            border-color: var(--secondary-blue) !important;
            background: var(--primary-blue) !important;
            color: #f5fbff !important;
        }

        .main-content .table-responsive,
        .main-content .dataTables_wrapper,
        .main-content .dashboard-table-card,
        .main-content table,
        .main-content .table,
        .main-content .dataTable,
        .main-content .table thead th,
        .main-content .dataTable thead th {
            border-radius: 0 !important;
        }

        /* Background overrides for tables */
        .page-header,
        .card,
        .main-content .table-responsive,
        .main-content .modal-content,
        .swal2-popup,
        .dropdown-menu {
            background-color: #ffffff !important;
        }

        .card,
        .main-content .table-responsive,
        .main-content .modal-content,
        .swal2-popup,
        .dropdown-menu {
            background: #ffffff !important;
        }

        html.app-skin-dark .page-header,
        html.app-skin-dark .card,
        html.app-skin-dark .main-content .table-responsive,
        html.app-skin-dark .main-content .modal-content,
        html.app-skin-dark .swal2-popup,
        html.app-skin-dark .dropdown-menu {
            background-color: #0f172a !important;
        }

        .btn-primary {
    background-color: #2B79B4 !important;
    border-color: #2B79B4 !important;
    color: #ffffff !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active {
    background-color: #226292 !important; /* Warna sedikit lebih gelap saat di-hover/klik */
    border-color: #1d527a !important;
    color: #ffffff !important;
}

.btn-primary:focus {
    box-shadow: 0 0 0 0.25rem rgba(43, 121, 180, 0.5) !important; /* Efek ring shadow saat fokus */
}

    </style>

    @stack('styles')
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <div class="nx-wrapper">

        @include('partials.sidebar')

        <div class="nx-main-container">

            @include('partials.header')

            <main class="nxl-container">
                <div class="nxl-content">
                    @yield('content')
                </div>

                @include('partials.footer')

            </main>

            <div class="theme-customizer">
                <div class="customizer-handle">
                    <a href="javascript:void(0);" class="cutomizer-open-trigger" style="background-color: var(--primary-blue); color: white;">
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
                        <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-0 border border-gray-2 theme-options-set">
                            <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Typography</label>

                            <div class="row g-2 theme-options-items font-family" id="fontFamilyList">
                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-lato" name="font-family" value="1" data-font-family="app-font-family-lato" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-lato">Lato</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-rubik" name="font-family" value="2" data-font-family="app-font-family-rubik" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-rubik">Rubik</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-inter" name="font-family" value="3" data-font-family="app-font-family-inter" checked />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-inter">Inter</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-cinzel" name="font-family" value="4" data-font-family="app-font-family-cinzel" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-cinzel">Cinzel</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-nunito" name="font-family" value="6" data-font-family="app-font-family-nunito" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-nunito">Nunito</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-roboto" name="font-family" value="7" data-font-family="app-font-family-roboto" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-roboto">Roboto</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-ubuntu" name="font-family" value="8" data-font-family="app-font-family-ubuntu" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ubuntu">Ubuntu</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-poppins" name="font-family" value="9" data-font-family="app-font-family-poppins" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-poppins">Poppins</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-raleway" name="font-family" value="10" data-font-family="app-font-family-raleway" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-raleway">Raleway</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-system-ui" name="font-family" value="11" data-font-family="app-font-family-system-ui" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-system-ui">System UI</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-noto-sans" name="font-family" value="12" data-font-family="app-font-family-noto-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-noto-sans">Noto Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-fira-sans" name="font-family" value="13" data-font-family="app-font-family-fira-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-fira-sans">Fira Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-work-sans" name="font-family" value="14" data-font-family="app-font-family-work-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-work-sans">Work Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-open-sans" name="font-family" value="15" data-font-family="app-font-family-open-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-open-sans">Open Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-maven-pro" name="font-family" value="16" data-font-family="app-font-family-maven-pro" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-maven-pro">Maven Pro</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-quicksand" name="font-family" value="17" data-font-family="app-font-family-quicksand" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-quicksand">Quicksand</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-montserrat" name="font-family" value="18" data-font-family="app-font-family-montserrat" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat">Montserrat</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-josefin-sans" name="font-family" value="19" data-font-family="app-font-family-josefin-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-josefin-sans">Josefin Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-ibm-plex-sans" name="font-family" value="20" data-font-family="app-font-family-ibm-plex-sans" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-ibm-plex-sans">IBM Plex Sans</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-source-sans-pro" name="font-family" value="5" data-font-family="app-font-family-source-sans-pro" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-source-sans-pro">Source Sans Pro</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-montserrat-alt" name="font-family" value="21" data-font-family="app-font-family-montserrat-alt" />
                                    <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-font-family-montserrat-alt">Montserrat Alt</label>
                                </div>

                                <div class="col-6 text-center single-option">
                                    <input type="radio" class="btn-check" id="app-font-family-roboto-slab" name="font-family" value="22" data-font-family="app-font-family-roboto-slab" />
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
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js')}}"></script>
    <script src="{{ asset('assets/js/common-init.min.js')}}"></script>

    <script>
        (function($) {
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert-success, .alert-danger').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            });
        })(jQuery);
    </script>

    <script src="{{ asset('assets/js/theme-customizer-init.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/js/dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/js/dataTables.bs5.min.js')}}"></script>
    <script src="{{ asset('assets/js/payment-init.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        if ($.fn.dataTable) {
            $.fn.dataTable.ext.errMode = 'none';
        }

        (function($) {
            $(document).ready(function() {
                if ($.fn.dataTable) {
                    $.fn.dataTable.ext.errMode = 'none';
                }

                setTimeout(function() {
                    $('.alert-success, .alert-danger').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            });
        })(jQuery);
    </script>

    <script>
        window.addEventListener('unhandledrejection', function(event) {
            if (
                event.reason &&
                event.reason.message &&
                event.reason.message.includes('Element not found')
            ) {
                event.preventDefault();
            }
        });

        (function($) {
            $(document).ready(function() {
                if (typeof ApexCharts !== 'undefined') {
                    const originalApexCharts = ApexCharts;

                    window.ApexCharts = function(element, options) {
                        if (!element) {
                            return {
                                render: function() { return Promise.resolve(); },
                                updateOptions: function() { return Promise.resolve(); },
                                updateSeries: function() { return Promise.resolve(); },
                                destroy: function() { return Promise.resolve(); }
                            };
                        }

                        return new originalApexCharts(element, options);
                    };

                    window.ApexCharts.prototype = originalApexCharts.prototype;
                }
            });
        })(jQuery);
    </script>
   <script>
        (function() {
            // 1. Fungsi untuk menerapkan class dan atribut sesuai mode
            function forceSyncTheme() {
                var theme = localStorage.getItem('theme');
                var skin = localStorage.getItem('skin');
                var bsTheme = localStorage.getItem('data-bs-theme');
                
                var isDark = (theme === 'dark' || skin === 'dark' || bsTheme === 'dark');
                
                var html = document.documentElement;
                var body = document.body;

                if (!isDark) {
                    html.classList.remove('app-skin-dark', 'dark', 'dark-mode');
                    html.setAttribute('data-bs-theme', 'light');
                    body.classList.remove('app-skin-dark', 'dark', 'dark-mode', 'bg-dark');
                    body.setAttribute('data-bs-theme', 'light');
                } else {
                    html.classList.add('app-skin-dark', 'dark');
                    html.setAttribute('data-bs-theme', 'dark');
                    body.classList.add('app-skin-dark', 'dark');
                }
                
                // Atur visibilitas ikon matahari/bulan di header
                var darkBtn = document.querySelector('.dark-button');
                var lightBtn = document.querySelector('.light-button');
                
                if (darkBtn && lightBtn) {
                    if (isDark) {
                        darkBtn.style.display = 'none';
                        lightBtn.style.display = 'block';
                    } else {
                        darkBtn.style.display = 'block';
                        lightBtn.style.display = 'none';
                    }
                }
            }

            // 2. Pasang event listener saat DOM sudah siap
            document.addEventListener('DOMContentLoaded', function() {
                // Jalankan sinkronisasi awal untuk mengatur ikon dengan benar
                forceSyncTheme();

                // Ambil alih tombol dark/light agar tidak bergantung pada script template
                var darkBtn = document.querySelector('.dark-button');
                var lightBtn = document.querySelector('.light-button');

                if (darkBtn) {
                    darkBtn.addEventListener('click', function(e) {
                        e.preventDefault(); // Cegah fungsi bawaan jika ada
                        localStorage.setItem('theme', 'dark');
                        localStorage.setItem('skin', 'dark');
                        localStorage.setItem('data-bs-theme', 'dark');
                        forceSyncTheme();
                    });
                }

                if (lightBtn) {
                    lightBtn.addEventListener('click', function(e) {
                        e.preventDefault(); // Cegah fungsi bawaan jika ada
                        localStorage.setItem('theme', 'light');
                        localStorage.setItem('skin', 'light');
                        localStorage.setItem('data-bs-theme', 'light');
                        forceSyncTheme();
                    });
                }
            });

            // 3. Tetap pantau klik di Sidebar Customizer (jika ada pengaturan tema lain)
            document.addEventListener('click', function(e) {
                // Jika yang diklik BUKAN tombol matahari/bulan, beri jeda untuk customizer bawaan
                if (!e.target.closest('.dark-light-theme')) {
                    setTimeout(forceSyncTheme, 50);
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>