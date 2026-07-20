@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    :root {
        --primary-blue: #2B79B4;
        --secondary-blue: #3F96CD;
    }

    #tim-kerja-chart {
        min-height: 350px;
    }

    #histori-pt-donut {
        min-height: 400px;
    }

    /* ===============================
       HERO / WELCOME PANEL (SOLID CUSTOM BLUE THEME)
    =============================== */
    .dashboard-hero {
        position: relative;
        overflow: hidden;
        background: var(--primary-blue); 
        border: none;
        border-radius: 18px;
        padding: 26px;
        margin-bottom: 22px;
        box-shadow: 0 12px 35px rgba(43, 121, 180, 0.25);
    }

    .dashboard-hero::before {
        content: "";
        position: absolute;
        width: 320px;
        height: 320px;
        right: -80px;
        top: -100px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12), transparent 70%);
        pointer-events: none;
    }

    .dashboard-hero::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        left: -60px;
        bottom: -80px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08), transparent 70%);
        pointer-events: none;
    }

    .dashboard-hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        backdrop-filter: blur(4px);
    }

    .hero-title {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        margin: 14px 0 8px;
        line-height: 1.25;
    }

    .hero-title span {
        color: #bae6fd; /* Soft blue highlight for contrast */
    }

    .hero-desc {
        color: rgba(255, 255, 255, 0.85);
        font-size: 14px;
        max-width: 780px;
        margin-bottom: 0;
        line-height: 1.7;
    }

    .hero-side-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 18px;
        backdrop-filter: blur(12px);
        height: 100%;
        color: #ffffff;
    }

    .hero-side-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        color: var(--primary-blue);
        font-size: 20px;
        margin-bottom: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* ===============================
       STAT CARD
    =============================== */
    .dashboard-stat-card {
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        transition: all 0.25s ease;
        overflow: hidden;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        border-color: var(--secondary-blue) !important;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        margin-bottom: 14px;
    }

    .stat-icon-primary {
        background: rgba(43, 121, 180, 0.10);
        color: var(--primary-blue);
    }

    .stat-icon-success {
        background: rgba(12, 206, 107, 0.12);
        color: #0c8f51;
    }

    .stat-icon-warning {
        background: rgba(254, 176, 25, 0.16);
        color: #b7791f;
    }

    .stat-icon-info {
        background: rgba(0, 188, 212, 0.12);
        color: #0891b2;
    }

    .stat-icon-purple {
        background: rgba(156, 39, 176, 0.12);
        color: #7e22ce;
    }

    .stat-number {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.45px;
        margin-bottom: 8px;
    }

    .stat-desc {
        color: #94a3b8;
        font-size: 12px;
        margin-bottom: 0;
        line-height: 1.5;
    }

    /* ===============================
       CHART INFO
    =============================== */
    .chart-info-box {
        background: #f8fafc;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 14px 16px;
        margin: 16px;
    }

    .chart-info-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 7px;
    }

    .chart-info-text {
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* ===============================
       TABLE
    =============================== */
    .table-dashboard thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
    }

    .empty-state i {
        font-size: 34px;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    /* ===============================
       DARK MODE
    =============================== */

    body.dark-theme .stat-number,
    body.dark-theme .chart-info-title,
    body.app-skin-dark .stat-number,
    body.app-skin-dark .chart-info-title,
    body.dark .stat-number,
    body.dark .chart-info-title,
    body.dark-mode .stat-number,
    body.dark-mode .chart-info-title,
    body[data-bs-theme="dark"] .stat-number,
    body[data-bs-theme="dark"] .chart-info-title {
        color: #e5e7eb !important;
    }

    body.dark-theme .stat-label,
    body.dark-theme .stat-desc,
    body.dark-theme .chart-info-text,
    body.app-skin-dark .stat-label,
    body.app-skin-dark .stat-desc,
    body.app-skin-dark .chart-info-text,
    body.dark .stat-label,
    body.dark .stat-desc,
    body.dark .chart-info-text,
    body.dark-mode .stat-label,
    body.dark-mode .stat-desc,
    body.dark-mode .chart-info-text,
    body[data-bs-theme="dark"] .stat-label,
    body[data-bs-theme="dark"] .stat-desc,
    body[data-bs-theme="dark"] .chart-info-text {
        color: #94a3b8 !important;
    }

    body.dark-theme .dashboard-stat-card,
    body.dark-theme .chart-info-box,
    body.app-skin-dark .dashboard-stat-card,
    body.app-skin-dark .chart-info-box,
    body.dark .dashboard-stat-card,
    body.dark .chart-info-box,
    body.dark-mode .dashboard-stat-card,
    body.dark-mode .chart-info-box,
    body[data-bs-theme="dark"] .dashboard-stat-card,
    body[data-bs-theme="dark"] .chart-info-box {
        background: #0f172a !important;
        border-color: rgba(148, 163, 184, 0.16) !important;
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.22);
    }

    body.dark-theme .table-dashboard thead th,
    body.app-skin-dark .table-dashboard thead th,
    body.dark .table-dashboard thead th,
    body.dark-mode .table-dashboard thead th,
    body[data-bs-theme="dark"] .table-dashboard thead th {
        background: #111827;
        color: #94a3b8;
        border-color: rgba(148, 163, 184, 0.16);
    }

    @media (max-width: 767.98px) {
        .dashboard-hero {
            padding: 20px;
        }

        .hero-title {
            font-size: 22px;
        }

        .stat-number {
            font-size: 23px;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalPTNValue = $totalPTN ?? 0;
    $totalPTSValue = $totalPTS ?? 0;
    $historiValue = $historiSelesai ?? 0;
    $totalLaporanValue = $totalLaporan ?? 0;
    $totalUserValue = $totalUser ?? 0;

    $ptTutupValue = $ptTutup ?? 0;
    $ptMergerValue = $ptMerger ?? 0;
    $ptPerubahanNamaValue = $ptPerubahanNama ?? 0;
    $ptBerubahBentukValue = $ptBerubahBentuk ?? 0;
    $ptPenegerianValue = $ptPenegerian ?? 0;
    $ptPindahLokasiValue = $ptPindahLokasi ?? 0;
    $ptTidakTerdataValue = $ptTidakTerdata ?? 0;

    $pokjaUser = Auth::check() ? (Auth::user()->pokja ?? 'Tim Kerja') : 'Tim Kerja';
    $namaUser = Auth::check() ? (Auth::user()->name ?? 'Pengguna') : 'Pengguna';
@endphp

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Beranda</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="">Menu Utama</a></li>
            <li class="breadcrumb-item">Beranda</li>
        </ul>
    </div>
</div>

<div class="main-content">

    <div class="dashboard-hero">
        <div class="dashboard-hero-content">
            <div class="row align-items-center g-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i>
                        Dashboard SI-JAKI
                    </div>

                    <h3 class="hero-title">
                        Selamat Datang, <span>{{ $namaUser }}</span>
                    </h3>

                    <p class="hero-desc">
                        Sistem Informasi Jejak Pembinaan Perguruan Tinggi membantu pemantauan data perguruan tinggi,
                        laporan pembinaan, histori perubahan kelembagaan, serta aktivitas tim kerja secara lebih cepat,
                        rapi, dan terukur.
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                        <span class="badge bg-white border-0 shadow-sm" style="color: var(--primary-blue);">
                            <i class="bi bi-building me-1"></i> {{ $pokjaUser }}
                        </span>
                        <span class="badge text-white border border-light" style="background: rgba(255,255,255,0.15);">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </span>
                        <span class="badge text-white border border-light" style="background: rgba(12, 206, 107, 0.5);">
                            <i class="bi bi-check-circle me-1"></i> Sistem Aktif
                        </span>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="hero-side-card">
                        <div class="hero-side-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h6 class="fw-bold text-white mb-2">Ringkasan Dashboard</h6>
                        <p class="fs-12 text-white-50 mb-3">
                            Pantau data PTN, PTS, histori PT, laporan pembinaan, dan pengguna sistem dari halaman ini.
                        </p>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white border-0 shadow-sm" style="color: var(--primary-blue);">
                                <i class="bi bi-database-check me-1"></i> Data Monitoring
                            </span>
                            <span class="badge text-white border border-light" style="background: rgba(255,255,255,0.15);">
                                v2.2
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-xxl-5 row-cols-xl-5 row-cols-lg-3 row-cols-md-2 row-cols-1 g-3 mb-4">
        <div class="col">
            <div class="card dashboard-stat-card stretch stretch-full">
                <div class="card-body">
                    <div class="stat-icon stat-icon-primary">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div class="stat-number">{{ $totalPTNValue }}</div>
                    <div class="stat-label">PTN Aktif</div>
                    <p class="stat-desc">Perguruan Tinggi Negeri aktif yang tercatat pada sistem.</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card dashboard-stat-card stretch stretch-full">
                <div class="card-body">
                    <div class="stat-icon stat-icon-success">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div class="stat-number">{{ $totalPTSValue }}</div>
                    <div class="stat-label">PTS Aktif</div>
                    <p class="stat-desc">Perguruan Tinggi Swasta aktif dalam wilayah pembinaan.</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card dashboard-stat-card stretch stretch-full">
                <div class="card-body">
                    <div class="stat-icon stat-icon-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-number">{{ $historiValue }}</div>
                    <div class="stat-label">Histori PT</div>
                    <p class="stat-desc">Riwayat perubahan dan status kelembagaan perguruan tinggi.</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card dashboard-stat-card stretch stretch-full">
                <div class="card-body">
                    <div class="stat-icon stat-icon-info">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-number">{{ $totalLaporanValue }}</div>
                    <div class="stat-label">Laporan Pembinaan</div>
                    <p class="stat-desc">Jumlah laporan pembinaan perguruan tinggi yang telah dibuat.</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card dashboard-stat-card stretch stretch-full">
                <div class="card-body">
                    <div class="stat-icon stat-icon-purple">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-number">{{ $totalUserValue }}</div>
                    <div class="stat-label">Users</div>
                    <p class="stat-desc">Pengguna yang memiliki akses ke aplikasi SI-JAKI.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-8">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <div>
                        <h5 class="card-title mb-1">Statistik Laporan Per Tim Kerja</h5>
                        <span class="fs-12 text-muted">Menampilkan jumlah laporan pembinaan berdasarkan tim kerja.</span>
                    </div>

                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Delete">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Refresh">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                <div data-bs-toggle="tooltip" title="Options">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('user.laporan-pts.index') }}" class="dropdown-item">
                                    <i class="feather-file"></i>Laporan PTS
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('user.laporan-ptn.index') }}" class="dropdown-item">
                                    <i class="feather-file-text"></i>Laporan PTN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body custom-card-action p-0">
                    <div id="tim-kerja-chart"></div>

                    <div class="chart-info-box">
                        <div class="chart-info-title">
                            <i class="bi bi-info-circle" style="color: var(--primary-blue);"></i>
                            Keterangan Chart
                        </div>
                        <p class="chart-info-text">
                            Grafik batang ini memperlihatkan jumlah laporan pembinaan yang dibuat oleh setiap tim kerja.
                            Tim kerja dengan batang tertinggi memiliki aktivitas pelaporan paling banyak pada data yang tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <div>
                        <h5 class="card-title mb-1">Statistik Histori PT</h5>
                        <span class="fs-12 text-muted">Komposisi histori status perguruan tinggi.</span>
                    </div>

                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Delete">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Refresh">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                <div data-bs-toggle="tooltip" title="Options">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('histori-pt.index') }}" class="dropdown-item">
                                    <i class="feather-clock"></i>Semua Histori PT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body custom-card-action">
                    <div id="histori-pt-donut"></div>

                    <div class="chart-info-box m-0 mt-3">
                        <div class="chart-info-title">
                            <i class="bi bi-pie-chart" style="color: var(--primary-blue);"></i>
                            Keterangan Donut
                        </div>
                        <p class="chart-info-text">
                            Donut chart menampilkan proporsi kategori histori PT seperti tutup, merger,
                            berubah nama, berubah bentuk, penegerian, pindah lokasi, dan tidak terdata.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <div>
                        <h5 class="card-title mb-1">Laporan Pembinaan PT Terbaru</h5>
                        <span class="fs-12 text-muted">Daftar laporan pembinaan terbaru yang masuk ke sistem.</span>
                    </div>

                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Delete">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Refresh">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                <div data-bs-toggle="tooltip" title="Options">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('user.laporan-pts.index') }}" class="dropdown-item">
                                    <i class="feather-file"></i>Laporan PTS
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('user.laporan-ptn.index') }}" class="dropdown-item">
                                    <i class="feather-file-text"></i>Laporan PTN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body custom-card-action p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dashboard">
                            <thead>
                                <tr class="border-b">
                                    <th scope="row">Perguruan Tinggi</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Tanggal Kegiatan</th>
                                    <th>Tanggal Dibuat Laporan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($laporanTerbaru) && count($laporanTerbaru) > 0)
                                    @foreach($laporanTerbaru as $laporan)
                                        <tr>
                                            <td>
                                                <div>
                                                    <span class="d-block fw-semibold">
                                                        {{ $laporan->perguruanTinggi->nama_pt ?? 'Perguruan Tinggi' }}
                                                    </span>
                                                    <span class="fs-12 d-block fw-normal text-muted">
                                                        {{ $laporan->perguruanTinggi->kode_pt ?? 'Kode PT' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gray-200 text-dark">
                                                    {{ $laporan->jenis_kegiatan ?? 'Kegiatan' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($laporan->tanggal_kegiatan ?? now())->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($laporan->created_at ?? now())->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('user.laporan-pts.detail', $laporan->uuid ?? '0') }}" data-bs-toggle="tooltip" title="Lihat Detail">
                                                    <i class="feather-eye" style="color: var(--primary-blue);"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <div class="fw-bold">Belum ada laporan yang dibuat</div>
                                                <div class="fs-12">Data laporan terbaru akan tampil di tabel ini.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendors/js/apexcharts.min.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = () => {
            return document.body.classList.contains('dark-theme') ||
                   document.body.classList.contains('app-skin-dark') ||
                   document.body.classList.contains('dark') ||
                   document.body.classList.contains('dark-mode') ||
                   document.body.getAttribute('data-bs-theme') === 'dark' ||
                   document.documentElement.getAttribute('data-bs-theme') === 'dark';
        };

        const chartTextColor = () => isDarkMode() ? '#e5e7eb' : '#0f172a';
        const chartMutedColor = () => isDarkMode() ? '#94a3b8' : '#64748b';
        const chartGridColor = () => isDarkMode() ? 'rgba(148, 163, 184, 0.16)' : '#f1f1f1';

        let timKerjaChart = null;
        let historiPtDonut = null;

        function renderTimKerjaChart() {
            const chartElement = document.querySelector("#tim-kerja-chart");

            if (!chartElement || typeof ApexCharts === 'undefined') {
                return;
            }

            const timKerjaData = @json($laporanPerTimKerja ?? []);

            if (timKerjaChart) {
                timKerjaChart.destroy();
                timKerjaChart = null;
            }

            if (timKerjaData.length > 0) {
                const timKerjaNames = timKerjaData.map(item => item.nama);
                const timKerjaValues = timKerjaData.map(item => item.jumlah);

                const displayLimit = 15;
                const displayNames = timKerjaNames.slice(0, displayLimit);
                const displayValues = timKerjaValues.slice(0, displayLimit);

                // Fetch our primary blue variable for the chart color
                const rootStyles = getComputedStyle(document.documentElement);
                const primaryBlueColor = rootStyles.getPropertyValue('--primary-blue').trim() || '#2B79B4';

                const timKerjaChartOptions = {
                    series: [{
                        name: 'Jumlah Laporan',
                        data: displayValues
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        },
                        foreColor: chartMutedColor()
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 6
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: displayNames,
                        labels: {
                            style: {
                                colors: chartMutedColor(),
                                fontSize: '12px',
                                fontFamily: 'Inter, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Laporan',
                            style: {
                                color: chartMutedColor()
                            }
                        },
                        labels: {
                            style: {
                                colors: chartMutedColor()
                            },
                            formatter: function (val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: isDarkMode() ? 'dark' : 'light',
                        y: {
                            formatter: function (val) {
                                return val + " laporan";
                            }
                        }
                    },
                    colors: [primaryBlueColor],
                    grid: {
                        borderColor: chartGridColor(),
                        padding: {
                            bottom: 10
                        }
                    }
                };

                timKerjaChart = new ApexCharts(chartElement, timKerjaChartOptions);
                timKerjaChart.render();
            } else {
                chartElement.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center" style="height: 350px;">
                        <div class="text-center text-muted">
                            <i class="bi bi-info-circle fs-3 mb-2 d-block"></i>
                            <p class="mb-0">Belum ada data laporan per tim kerja</p>
                        </div>
                    </div>
                `;
            }
        }

        function renderHistoriPtDonut() {
            const chartElement = document.querySelector("#histori-pt-donut");

            if (!chartElement || typeof ApexCharts === 'undefined') {
                return;
            }

            if (historiPtDonut) {
                historiPtDonut.destroy();
                historiPtDonut = null;
            }

            const historiPtDonutOptions = {
                series: [
                    {{ $ptTutupValue }},
                    {{ $ptMergerValue }},
                    {{ $ptPerubahanNamaValue }},
                    {{ $ptBerubahBentukValue }},
                    {{ $ptPenegerianValue }},
                    {{ $ptPindahLokasiValue }},
                    {{ $ptTidakTerdataValue }}
                ],
                chart: {
                    type: 'donut',
                    height: 400,
                    foreColor: chartMutedColor()
                },
                labels: [
                    'PT Tutup',
                    'PT Merger',
                    'PT Berubah Nama',
                    'PT Berubah Bentuk',
                    'PT Penegerian',
                    'PT Pindah Lokasi',
                    'Tidak Terdata'
                ],
                colors: [
                    '#FF4560',
                    '#4361EE',
                    '#0CCE6B',
                    '#FEB019',
                    '#9C27B0',
                    '#00BCD4',
                    '#607D8B'
                ],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '12px',
                                    fontFamily: 'Inter, sans-serif',
                                    fontWeight: 600,
                                    color: chartMutedColor(),
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontFamily: 'Inter, sans-serif',
                                    fontWeight: 700,
                                    color: chartTextColor(),
                                    offsetY: 10,
                                    formatter: function (val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: chartTextColor(),
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 500,
                    labels: {
                        colors: chartMutedColor()
                    },
                    markers: {
                        width: 12,
                        height: 12,
                        strokeWidth: 0,
                        radius: 12,
                        offsetX: -3
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 5
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    theme: isDarkMode() ? 'dark' : 'light',
                    enabled: true,
                    y: {
                        formatter: function(value) {
                            return value + " Perguruan Tinggi";
                        }
                    }
                },
                responsive: [{
                    breakpoint: 991.98,
                    options: {
                        chart: {
                            width: '100%',
                            height: 400
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            fontSize: '12px',
                            offsetY: 10,
                            itemMargin: {
                                horizontal: 5,
                                vertical: 5
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%'
                                }
                            }
                        }
                    }
                }]
            };

            historiPtDonut = new ApexCharts(chartElement, historiPtDonutOptions);
            historiPtDonut.render();
        }

        renderTimKerjaChart();
        renderHistoriPtDonut();

        const darkButton = document.querySelector('.dark-button');
        const lightButton = document.querySelector('.light-button');

        if (darkButton && lightButton) {
            const currentTheme = localStorage.getItem('theme');

            if (currentTheme === 'dark') {
                document.body.classList.add('dark-theme');
                darkButton.style.display = 'none';
                lightButton.style.display = 'block';
            }

            darkButton.addEventListener('click', function() {
                document.body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
                darkButton.style.display = 'none';
                lightButton.style.display = 'block';

                setTimeout(function () {
                    renderTimKerjaChart();
                    renderHistoriPtDonut();
                }, 150);
            });

            lightButton.addEventListener('click', function() {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
                lightButton.style.display = 'none';
                darkButton.style.display = 'block';

                setTimeout(function () {
                    renderTimKerjaChart();
                    renderHistoriPtDonut();
                }, 150);
            });
        }
    });
</script>
@endpush