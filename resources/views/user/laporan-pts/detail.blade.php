@extends('layouts.app')

@section('title', 'Detail Laporan PT ' . $laporan->perguruanTinggi->nama_pt)

@section('content')

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;

        /* 1. Tambahkan warna biru muda di sini */
        background-color: #dbeafe;

        /* 2. Gabungkan warna dan gambar */
        background-image:
            linear-gradient(rgba(191, 219, 254, 0.7), rgba(191, 219, 254, 0.7)),
            url("{{ asset('logo/bg_login2.jpeg') }}");

        /* 3. Efek pencampuran warna */
        background-blend-mode: overlay;

        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    .page-header {
        background: rgba(255, 255, 255, 0.919) !important; /* Menggunakan RGBA untuk transparansi sesungguhnya */
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;

        /* Menghapus border bawah */
        border-bottom: none !important;
        box-shadow: none !important; /* Opsional: Menghapus bayangan jika ada agar benar-benar flat */
    }

    /* =========================================
       FIX MODE MALAM (DARK MODE)
       ========================================= */

    /* Hapus background gambar dan ganti warna dasar di mode malam */
    html.app-skin-dark body,
    html.app-skin-dark {
        background-color: #020617 !important;
        background-image: none !important;
    }

    /* Sesuaikan transparansi header untuk mode malam */
    html.app-skin-dark .page-header {
        background: rgba(15, 23, 42, 0.9) !important;
    }

    /* Paksa CKEditor transparan dan teks jadi terang */
    html.app-skin-dark .ck-content,
    html.app-skin-dark .ck-content * {
        color: #e2e8f0 !important;
        background-color: transparent !important;
    }
</style>

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Detail Laporan PT</h5>
        </div>
        <ul class="breadcrumb d-none d-md-flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Menu Utama</a></li>
            <li class="breadcrumb-item">
                @if($laporan->perguruanTinggi->jenis_pt == 'PTN')
                <a href="{{ route('user.laporan-ptn.index')}}">Laporan PTN</a>
                @else
                <a href="{{ route('user.laporan-pts.index')}}">Laporan PTS</a>
                @endif
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('user.laporan-pts.list', $laporan->perguruanTinggi->uuid) }}">Daftar Laporan</a>
            </li>
            <li class="breadcrumb-item">Detail</li>
        </ul>
    </div>
    <div class="d-block d-md-none position-absolute" style="right: 20px; top: 20px;">
        <a href="{{ route('user.laporan-pts.list', $laporan->perguruanTinggi->uuid) }}">
            <i class="feather-arrow-left"></i>
        </a>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center">
                <div class="d-none d-md-block">
                    <a href="{{ route('user.laporan-pts.list', $laporan->perguruanTinggi->uuid) }}" class="btn btn-icon btn-light-brand">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Laporan Pembinaan</h5>
                    <span class="badge
                        @switch($laporan->jenis_kegiatan)
                            @case('Rapat/Audiensi') bg-secondary @break
                            @case('Visitasi') bg-info @break
                            @case('Monitoring & Evaluasi') bg-primary @break
                            @case('Aduan/Laporan') bg-warning @break
                            @case('Teguran/Sanksi') bg-danger @break
                            @default bg-secondary
                        @endswitch">
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Perguruan Tinggi</div>
                        <div class="col-md-8">{{ $laporan->perguruanTinggi->nama_pt }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kode Perguruan Tinggi</div>
                        <div class="col-md-8">{{ $laporan->perguruanTinggi->kode_pt }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Jenis Perguruan Tinggi</div>
                        <div class="col-md-8">{{ $laporan->perguruanTinggi->jenis_pt }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Jenis Kegiatan</div>
                        <div class="col-md-8">{{ $laporan->jenis_kegiatan }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Kegiatan</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($laporan->tanggal_kegiatan)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tempat Kegiatan</div>
                        <div class="col-md-8">{{ $laporan->tempat_kegiatan }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Dibuat Oleh</div>
                        <div class="col-md-8">{{ $laporan->created_by }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Dibuat</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($laporan->created_at)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY | HH:mm') }} WIB</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Terakhir Diubah</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($laporan->updated_at)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY | HH:mm') }} WIB</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resume Kegiatan</h5>
                </div>
                <div class="card-body">
                    <div class="ck-content">
                        {!! $laporan->resume !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dokumen Undangan</label>
                        <div class="d-grid gap-2">
                            @if($laporan->dokumen_undangan)
                            {{-- <a href="{{ route('laporan-pt.download-undangan', $laporan->uuid) }}" class="btn btn-outline-primary">
                                <i class="feather-download me-2"></i> Download Undangan
                            </a> --}}
                            <a href="{{ asset('storage/dokumen_laporan_pt/'.$laporan->dokumen_undangan) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="feather-eye me-2"></i> Lihat Dokumen Undangan
                            </a>
                            @else
                            <button class="btn btn-outline-secondary" disabled>
                                <i class="feather-x-circle me-2"></i> Tidak Ada Dokumen
                            </button>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-bold">Dokumen Notula</label>
                        <div class="d-grid">
                            @if($laporan->dokumen_notula)
                            {{-- <a href="{{ route('laporan-pt.download-notula', $laporan->uuid) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="feather-download me-2"></i> Download Notula
                            </a> --}}
                            <a href="{{ asset('storage/dokumen_laporan_pt/'.$laporan->dokumen_notula) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="feather-eye me-2"></i> Lihat Dokumen Notula
                            </a>
                            @else
                            <button class="btn btn-outline-secondary" disabled>
                                <i class="feather-x-circle me-2"></i> Tidak Ada Dokumen
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lingkup Tim Kerja</h5>
                </div>
                <div class="card-body">
                    @if(!empty($laporan->pokja) && count($laporan->pokja) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($laporan->pokja as $userId)
                                @php
                                    $pokjaUser = App\Models\User::find($userId);
                                @endphp
                                @if($pokjaUser)
                                <li class="list-group-item px-0 py-2">
                                    <div>
                                        <span>{{ $pokjaUser->pokja }}</span>
                                    </div>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-3">
                            <i class="feather-users text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0">Tidak ada anggota tim</p>
                        </div>
                    @endif
                </div>
            </div>

            @if((Auth::id() == $laporan->user_id && $canEditDelete) || Auth::user()->akses == 'Admin' || Auth::user()->akses == 'Dev')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.laporan-pts.edit', $laporan->uuid) }}" class="btn btn-primary">
                            <i class="feather-edit me-2"></i> Edit Laporan
                        </a>
                        <form action="{{ route('user.laporan-pts.destroy', $laporan->uuid) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 delete-btn">
                                <i class="feather-trash-2 me-2"></i> Hapus Laporan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @elseif(Auth::id() == $laporan->user_id && !$canEditDelete)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <span style="display: inline-block; text-align: justify;">Laporan hanya dapat diedit atau dihapus dalam waktu 3 hari setelah pembuatan. Untuk perubahan atau penghapusan setelah batas waktu tersebut, silakan menghubungi Tim Kerja Kelembagaan dan Kemitraan.</span>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi Hapus
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data laporan tersebut akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
