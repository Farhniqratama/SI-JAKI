@extends('layouts.app')

@section('title', 'Histori Perguruan Tinggi')

@section('content')

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Histori PT</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Menu Utama</a></li>
            <li class="breadcrumb-item">Histori PT</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex d-md-none">
                <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <div class="dropdown">
                    <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                        <i class="feather-download me-2"></i>
                        <span>Unduh Data</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('histori-pt.export-excel') }}" class="dropdown-item">
                            <i class="bi bi-filetype-exe me-3"></i>
                            <span>Excel</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-md-none d-flex align-items-center">
            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
            </a>
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
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover" id="historiPtTable">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">No</th>
                                    <th style="white-space: nowrap;">Kode PT</th>
                                    <th style="white-space: nowrap;">Nama PT Sesuai PDDikti</th>
                                    <th style="white-space: nowrap;">Nama PT Sesuai SK Izin</th>
                                    <th style="white-space: nowrap;">Jenis PT</th>
                                    <th style="white-space: nowrap;">Status PT</th>
                                    <th style="white-space: nowrap;">Status Kelembagaan PT</th>
                                    <th class="text-end" style="white-space: nowrap;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perguruanTinggi as $index => $pt)
                                <tr class="single-item">
                                    <td>
                                        <div class="avatar-text bg-gray-200">{{ $index + 1 }}</div>
                                    </td>
                                    <td class="text-dark">{{ $pt->kode_pt }}</td>
                                    <td class="text-dark">
                                        @if(strlen($pt->nama_pt) > 35)
                                            @php
                                                // Pisahkan teks menjadi 2 bagian di karakter ke-35 pada spasi terdekat
                                                $nama = $pt->nama_pt;
                                                $pos = strrpos(substr($nama, 0, 35), ' ');
                                                if ($pos === false) $pos = 35;
                                                $baris1 = substr($nama, 0, $pos);
                                                $baris2 = trim(substr($nama, $pos));
                                            @endphp
                                            <span class="d-block">{{ $baris1 }}</span>
                                            <span class="d-block">{{ $baris2 }}</span>
                                        @else
                                            {{ $pt->nama_pt }}
                                        @endif
                                    </td>
                                    <td class="text-dark">
                                        @if($pt->nama_pt_sk && strlen($pt->nama_pt_sk) > 35)
                                            @php
                                                // Pisahkan teks menjadi 2 bagian di karakter ke-35 pada spasi terdekat
                                                $nama_sk = $pt->nama_pt_sk;
                                                $pos_sk = strrpos(substr($nama_sk, 0, 35), ' ');
                                                if ($pos_sk === false) $pos_sk = 35;
                                                $baris1_sk = substr($nama_sk, 0, $pos_sk);
                                                $baris2_sk = trim(substr($nama_sk, $pos_sk));
                                            @endphp
                                            <span class="d-block">{{ $baris1_sk }}</span>
                                            <span class="d-block">{{ $baris2_sk }}</span>
                                        @else
                                            {{ $pt->nama_pt_sk ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="text-dark">{{ $pt->jenis_pt }}</td>
                                    <td>
                                        @switch($pt->status_pt)
                                            @case('Aktif')
                                                <div class="badge bg-soft-success text-success">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Tutup')
                                                <div class="badge bg-soft-danger text-danger">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Merger')
                                                <div class="badge bg-soft-warning text-warning">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Berubah Bentuk')
                                                <div class="badge bg-soft-info text-info">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Berubah Nama')
                                                <div class="badge bg-soft-teal text-teal">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Pindah Lokasi')
                                                <div class="badge bg-soft-danger text-danger">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Penegerian')
                                                <div class="badge bg-soft-danger text-danger">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Pembinaan')
                                                <div class="badge bg-soft-teal text-teal">{{ $pt->status_pt }}</div>
                                                @break
                                            @case('Tidak Terdata')
                                                <div class="badge bg-soft-dark text-dark">{{ $pt->status_pt }}</div>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-dark">
                                        @if($pt->status_kelembagaan_pt)
                                            @if(strlen($pt->status_kelembagaan_pt) > 35)
                                                @php
                                                    // Pisahkan teks menjadi 2 bagian di karakter ke-35 pada spasi terdekat
                                                    $status_kel = $pt->status_kelembagaan_pt;
                                                    $pos_kel = strrpos(substr($status_kel, 0, 35), ' ');
                                                    if ($pos_kel === false) $pos_kel = 35;
                                                    $baris1_kel = substr($status_kel, 0, $pos_kel);
                                                    $baris2_kel = trim(substr($status_kel, $pos_kel));
                                                @endphp
                                                <span class="d-block">{{ $baris1_kel }}</span>
                                                <span class="d-block">{{ $baris2_kel }}</span>
                                            @else
                                                {{ $pt->status_kelembagaan_pt }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            @if($pt->status_pt == 'Aktif' || $pt->status_pt == 'Pembinaan')
                                                @if($pt->jenis_pt == 'PTN')
                                                    <a href="{{ route('user.laporan-ptn.list', $pt->uuid) }}" class="avatar-text avatar-md ms-auto" title="Lihat Laporan">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('user.laporan-pts.list', $pt->uuid) }}" class="avatar-text avatar-md ms-auto" title="Lihat Laporan">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            <a href="{{ route('histori-pt.detail', $pt->uuid) }}" class="avatar-text avatar-md ms-auto" title="Lihat Detail">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="feather-alert-circle fs-2 mb-3"></i>
                                            <p class="mb-2">Belum ada data Perguruan Tinggi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi DataTables
        const historiPtTable = $('#historiPtTable').DataTable({
            responsive: true,
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Cari Perguruan Tinggi...',
                lengthMenu: '_MENU_',
                paginate: {
                    previous: '<i class="feather-chevron-left"></i>',
                    next: '<i class="feather-chevron-right"></i>'
                }
            },
            columnDefs: [
                { 
                    targets: [-1], 
                    orderable: false 
                }
            ]
        });
    });
</script>
@endpush
@endsection