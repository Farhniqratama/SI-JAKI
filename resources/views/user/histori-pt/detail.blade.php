@extends('layouts.app')

@section('title', 'Detail Histori PT')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css')}}">

<style>
    /* Tombol navigasi bulan */
    .datepicker-days .prev,
    .datepicker-days .next {
        font-size: 18px;
        color: #666;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0 10px;
        font-weight: bold;
    }
    
    .datepicker-days .prev:hover,
    .datepicker-days .next:hover {
        color: #333;
    }
    
    /* Header hari */
    .datepicker-days th {
        text-align: center;
        font-weight: 500;
        color: #6c757d;
        padding: 8px 0;
        width: 40px;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Sel tanggal */
    .datepicker-days td {
        text-align: center;
        padding: 8px 0;
        width: 40px;
        height: 40px;
        cursor: pointer;
        border-radius: 0;
    }
    
    /* Tanggal aktif/terpilih */
    .datepicker-days td.active {
        background-color: #3639DF !important;
        border-color: #3639DF !important;
        border-radius: 4px;
        color: white !important;
    }
    
    /* Tanggal hari ini */
    .datepicker-days td.today {
        background-color: #f8f9fa;
        position: relative;
    }
    
    .datepicker-days td.today:after {
        content: '';
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        background-color: #3639DF;
        border-radius: 50%;
    }
    
    /* Hover efek tanggal */
    .datepicker-days td:hover {
        background-color: #e9ecef;
        border-radius: 4px;
    }
    
    /* Footer datepicker */
    .datepicker-footer {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid #dee2e6;
        margin-top: 5px;
    }
    
    .datepicker-footer button {
        padding: 5px 15px;
        background: none;
        border: none;
        cursor: pointer;
        color: #3639DF;
        font-weight: 500;
    }
    
    .datepicker-footer button:hover {
        text-decoration: underline;
    }
    
    /* Memastikan hari-hari tersusun dengan jarak yang sama */
    .datepicker-days th,
    .datepicker-days td {
        width: 40px !important;
    }
    
    /* Container untuk header bulan */
    .datepicker-month-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    /* Memperbaiki tampilan Select2 dalam dropdown filter */
    .filter-dropdown .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        height: 38px;
        display: flex;
        align-items: center;
        border-radius: 0.25rem;
    }
    
    .filter-dropdown .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 12px;
        color: #212529;
    }
    
    .filter-dropdown .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Menyembunyikan tombol clear (X) pada select2 */
    .filter-dropdown .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }
    
    /* Mengatur tampilan dropdown dari select2 */
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Mengatur tampilan hasil pencarian di select2 */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3639DF;
    }
    
    /* Memperbaiki tampilan input datepicker */
    .filter-dropdown .datepicker {
        background-color: #fff;
    }
    
    /* Cursor pointer untuk input datepicker */
    .filter-dropdown input[readonly] {
        cursor: pointer;
        background-color: #fff;
    }
    /* Style untuk select2 di dalam dropdown */
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Fix dropdown menu tidak menutup */
    .dropdown-menu.show {
        z-index: 1050;
    }
    
    /* Fix datepicker di dalam dropdown */
    .datepicker-dropdown {
        z-index: 1060 !important;
    }

    .filter-dropdown .dropdown-menu {
        min-width: 300px;
    }
    
    /* Tambahkan CSS berikut di bagian <style> dalam layout */
    @media (max-width: 576px) {
        .filter-dropdown .dropdown-menu {
            width: 65vw;
            max-width: 65vw;
            margin-right: 2.5vw;
            padding: 15px !important;
            min-width: auto;
        }
        
        .filter-dropdown .dropdown-menu .form-label {
            font-size: 14px;
        }
        
        .filter-dropdown .dropdown-menu .form-select,
        .filter-dropdown .dropdown-menu .form-control {
            height: 40px;
            font-size: 14px;
        }
    }
</style>

@endpush

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Detail Histori PT</h5>
        </div>
        <ul class="breadcrumb d-none d-md-flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Menu Utama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('histori-pt.index')}}">Histori PT</a></li>
            <li class="breadcrumb-item active">Detail Histori PT</li>
        </ul>
    </div>
    <!-- Tombol mobile dengan posisi absolute di pojok kanan -->
    <div class="d-block d-md-none position-absolute" style="right: 20px; top: 20px;">
        <a href="{{ route('histori-pt.index') }}">
            <i class="feather-arrow-left"></i>
        </a>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center">
                <!-- Tombol untuk desktop -->
                <div class="d-none d-md-block">
                    <a href="{{ route('histori-pt.index') }}" class="btn btn-icon btn-light-brand">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body lead-info">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <span class="d-block mb-2">Informasi Perguruan Tinggi</span>
                        <span class="fs-12 fw-normal text-muted d-block">Detail informasi lengkap perguruan tinggi</span>
                    </h5>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-2 fw-medium">Kode PT</div>
                    <div class="col-lg-10">
                        <span class="fw-bold">{{ $pt->kode_pt }}</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-2 fw-medium">Nama PT Sesuai PDDikti</div>
                    <div class="col-lg-10">
                        <span class="fw-bold">{{ $pt->nama_pt }}</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-2 fw-medium">Jenis PT</div>
                    <div class="col-lg-10">
                        <span class="badge bg-soft-primary text-primary">{{ $pt->jenis_pt }}</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-2 fw-medium">Status PT</div>
                    <div class="col-lg-10">
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
                    </div>
                </div>

                @if($pt->status_pt == 'Aktif' || $pt->status_pt == 'Pembinaan')
                    <div class="row mb-4">
                        <div class="col-lg-2 fw-medium">Status Kelembagaan PT</div>
                        <div class="col-lg-10">
                            <span>{{ $pt->status_kelembagaan_pt ?? 'Tidak ada data' }}</span>
                        </div>
                    </div>
                @endif

                @if(auth()->check() && in_array(auth()->user()->akses, ['Admin', 'Dev']))
                    @if($pt->nama_pemimpin_pt || $pt->nomor_kontak_pemimpin)
                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Kontak Pemimpin PT</div>
                            <div class="col-lg-10">
                                @if($pt->nama_pemimpin_pt)
                                    <div class="mb-2">
                                        <span class="fw-medium text-muted d-inline">Nama: </span>
                                        <span>{{ $pt->nama_pemimpin_pt }}</span>
                                    </div>
                                @endif
                                @if($pt->nomor_kontak_pemimpin)
                                    <div>
                                        <span class="fw-medium text-muted d-inline">Nomor Kontak: </span>
                                        <span>{{ $pt->nomor_kontak_pemimpin }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                @php
                    $alamatUtama = $pt->addressesByType('utama')->get();
                    $alamatPerluasan = $pt->addressesByType('perluasan')->get();
                    $alamatPsdku = $pt->addressesByType('psdku')->get();
                    $alamatPbjj = $pt->addressesByType('pbjj')->get();
                    $hasAnyAddress = $alamatUtama->count() > 0 || $alamatPerluasan->count() > 0 || $alamatPsdku->count() > 0 || $alamatPbjj->count() > 0;
                @endphp

                @if($hasAnyAddress)
                    <div class="row mb-4">
                        <div class="col-lg-2 fw-medium">Alamat PT</div>
                        <div class="col-lg-10">
                            @if($alamatUtama->count() > 0)
                                <div class="mb-3">
                                    <span class="fw-medium text-muted d-block mb-1">Kampus Utama:</span>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($alamatUtama as $index => $alamat)
                                            <li class="mb-1">{{ $index + 1 }}. {{ $alamat->address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if($alamatPerluasan->count() > 0)
                                <div class="mb-3">
                                    <span class="fw-medium text-muted d-block mb-1">Kampus Perluasan:</span>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($alamatPerluasan as $index => $alamat)
                                            <li class="mb-1">{{ $index + 1 }}. {{ $alamat->address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if($alamatPsdku->count() > 0)
                                <div class="mb-3">
                                    <span class="fw-medium text-muted d-block mb-1">Kampus PSDKU:</span>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($alamatPsdku as $index => $alamat)
                                            <li class="mb-1">{{ $index + 1 }}. {{ $alamat->address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if($alamatPbjj->count() > 0)
                                <div class="mb-3">
                                    <span class="fw-medium text-muted d-block mb-1">Kampus PBJJ:</span>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($alamatPbjj as $index => $alamat)
                                            <li class="mb-1">{{ $index + 1 }}. {{ $alamat->address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($pt->status_pt !== 'Aktif')
                    <div class="row mb-4">
                        <div class="col-lg-2 fw-medium">Tanggal SK</div>
                        <div class="col-lg-10">
                            <span>{{ $pt->tanggal ? $pt->tanggal->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY') : 'Tidak ada data' }}</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-2 fw-medium">Keterangan</div>
                        <div class="col-lg-10">
                            <span>{{ $pt->keterangan ?? 'Tidak ada keterangan' }}</span>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-lg-2 fw-medium">File SK</div>
                        <div class="col-lg-10">
                            @if($pt->file_sk)
                                <div class="d-flex gap-2 mb-3">
                                    <a href="{{ route('manage-pt.download', $pt->uuid) }}" class="btn btn-sm btn-primary">
                                        <i class="feather-download me-2"></i>Unduh SK
                                    </a>
                                    <a href="{{ asset('storage/sk_perguruan_tinggi/' . $pt->file_sk) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="feather-eye me-2"></i>Preview SK
                                    </a>
                                </div>
                            @else
                                <span class="text-muted">Tidak ada file SK</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Riwayat Laporan Pembinaan Section with Filtering -->
    @if(isset($laporan))
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Laporan Pembinaan</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown filter-dropdown">
                            <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                <i class="feather-filter"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-3">
                                <!-- Filter Jenis Kegiatan (Dropdown) -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Jenis Kegiatan</label>
                                    <select class="form-select" id="filter-jenis" name="filter-jenis">
                                        <option value="">Semua Jenis Kegiatan</option>
                                        <option value="Rapat/Audiensi">Rapat/Audiensi</option>
                                        <option value="Visitasi">Visitasi</option>
                                        <option value="Monitoring & Evaluasi">Monitoring & Evaluasi</option>
                                        <option value="Aduan/Laporan">Aduan/Laporan</option>
                                        <option value="Teguran/Sanksi">Teguran/Sanksi</option>
                                    </select>
                                </div>
                                
                                <!-- Filter Tahun (Input) -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Tahun</label>
                                    <input type="text" class="form-control" id="filter-tahun" name="filter-tahun" placeholder="Pilih tahun" readonly>
                                </div>
                                
                                <!-- Filter Bulan (Input) -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Bulan</label>
                                    <input type="text" class="form-control" id="filter-bulan" name="filter-bulan" placeholder="Pilih bulan" readonly>
                                </div>
                                
                                <!-- Filter Pembuat Laporan -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Pembuat Laporan (Pokja)</label>
                                    <select class="form-select" id="filter-creator" name="filter-creator">
                                        <option value="">Semua Pembuat</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->pokja ?? $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-50 me-2" id="reset-filter">Reset</button>
                                    <button type="button" class="btn btn-sm btn-primary w-50" id="apply-filter">Terapkan</button>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 12" data-bs-auto-close="outside">
                                <i class="feather-download"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item" id="export-pdf" 
                                   data-url="{{ route('histori-pt.export-pdf', ['uuid' => $pt->uuid]) }}">
                                    <i class="bi bi-filetype-pdf me-3"></i>
                                    <span>PDF</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" id="print-view"
                                   data-url="{{ route('histori-pt.print', ['uuid' => $pt->uuid]) }}">
                                    <i class="bi bi-printer me-3"></i>
                                    <span>Print</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover" id="laporanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Kegiatan</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Tempat</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <!-- Modifikasi pada bagian tbody di histori-pt/detail.blade.php -->
                            <tbody>
                                @forelse($laporan as $index => $item)
                                <tr class="single-item" data-user-id="{{ $item->user_id }}" data-creator-id="{{ $item->user_id }}">
                                    <td>
                                        <div class="avatar-text bg-gray-200">{{ $index + 1 }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY') }}</td>
                                    <td>
                                        @switch($item->jenis_kegiatan)
                                            @case('Rapat/Audiensi')
                                                <span class="badge bg-soft-primary text-primary">{{ $item->jenis_kegiatan }}</span>
                                                @break
                                            @case('Visitasi')
                                                <span class="badge bg-soft-info text-info">{{ $item->jenis_kegiatan }}</span>
                                                @break
                                            @case('Monitoring & Evaluasi')
                                                <span class="badge bg-soft-success text-success">{{ $item->jenis_kegiatan }}</span>
                                                @break
                                            @case('Aduan/Laporan')
                                                <span class="badge bg-soft-warning text-warning">{{ $item->jenis_kegiatan }}</span>
                                                @break
                                            @case('Teguran/Sanksi')
                                                <span class="badge bg-soft-danger text-danger">{{ $item->jenis_kegiatan }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-soft-secondary text-secondary">{{ $item->jenis_kegiatan }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $item->tempat_kegiatan }}</td>
                                    <td>{{ $item->created_by }}</td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0, 10">
                                                    <i class="feather-more-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route($pt->jenis_pt == 'PTN' ? 'user.laporan-ptn.detail' : 'user.laporan-pts.detail', $item->uuid) }}" class="dropdown-item">
                                                        <i class="feather-eye me-2"></i>Detail
                                                    </a>
                                                    
                                                    @if($item->dokumen_undangan)
                                                    <a href="{{ route($pt->jenis_pt == 'PTN' ? 'user.laporan-ptn.download-undangan' : 'user.laporan-pts.download-undangan', $item->uuid) }}" class="dropdown-item">
                                                        <i class="feather-download me-2"></i>Download Undangan
                                                    </a>
                                                    @endif
                                                    
                                                    @if($item->dokumen_notula)
                                                    <a href="{{ route($pt->jenis_pt == 'PTN' ? 'user.laporan-ptn.download-notula' : 'user.laporan-pts.download-notula', $item->uuid) }}" class="dropdown-item">
                                                        <i class="feather-download me-2"></i>Download Notula
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="d-flex align-items-center justify-content-center" style="height: 200px">
                                            <div class="text-center">
                                                <h2 class="fs-16 fw-semibold">Belum ada laporan untuk perguruan tinggi ini</h2>
                                            </div>
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
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
<script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('js/list.js')}}"></script>
@endpush
@endsection