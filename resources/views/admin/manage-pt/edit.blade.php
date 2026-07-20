@extends('layouts.app')

@section('title', 'Edit PT ' . $pt->nama_pt) 

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css')}}">

<style>
    /* Style kustom untuk datepicker */
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
    
    /* Datepicker settings */
    .datepicker-days th,
    .datepicker-days td {
        width: 40px !important;
    }
    
    /* Card body sembunyikan overflow */
    .card-body {
        overflow: hidden;
    }
    
    /* Style untuk form dan action buttons */
    .form-actions {
        margin-top: 10px; 
    }
    
    /* Transition untuk additional section */
    .additional-info-section {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease;
        opacity: 0;
    }
    
    .additional-info-section.show {
        max-height: 500px; /* Setingan tinggi maksimal */
        opacity: 1;
    }
    
    /* Untuk card */
    .card.stretch {
        height: auto !important;
        min-height: 0 !important;
    }
</style>

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Manajemen PT</h5>
        </div>
        <ul class="breadcrumb d-none d-md-flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Admin SI-JAKI</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage-pt.index')}}">Daftar PT</a></li>
            <li class="breadcrumb-item">Edit PT</li>
        </ul>
    </div>
    <!-- Tombol mobile dengan posisi absolute di pojok kanan -->
    <div class="d-block d-md-none position-absolute" style="right: 20px; top: 20px;">
        <a href="{{ route('manage-pt.index') }}">
            <i class="feather-arrow-left"></i>
        </a>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center">
                <!-- Tombol untuk desktop -->
                <div class="d-none d-md-block">
                    <a href="{{ route('manage-pt.index') }}" class="btn btn-icon btn-light-brand">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Perguruan Tinggi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('manage-pt.update', $pt->uuid) }}" method="POST" enctype="multipart/form-data" id="editPtForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Bagian dasar form yang selalu muncul -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="kode_pt" class="form-label">Kode PT <span class="text-danger">*</span></label>
                                <input type="text" name="kode_pt" id="kode_pt" class="form-control @error('kode_pt') is-invalid @enderror" value="{{ old('kode_pt', $pt->kode_pt) }}" required>
                                @error('kode_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="nama_pt" class="form-label">Nama PT Sesuai PDDikti <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pt" id="nama_pt" class="form-control @error('nama_pt') is-invalid @enderror" value="{{ old('nama_pt', $pt->nama_pt) }}" required>
                                @error('nama_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label for="nama_pt_sk" class="form-label">Nama PT Sesuai SK Izin</label>
                                <input type="text" name="nama_pt_sk" id="nama_pt_sk" class="form-control @error('nama_pt_sk') is-invalid @enderror" value="{{ old('nama_pt_sk', $pt->nama_pt_sk) }}">
                                @error('nama_pt_sk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="jenis_pt" class="form-label">Jenis PT <span class="text-danger">*</span></label>
                                <select name="jenis_pt" id="jenis_pt" class="form-select @error('jenis_pt') is-invalid @enderror" data-select2-selector="default" required>
                                    <option value="">-- Pilih Jenis PT --</option>
                                    <option value="PTN" {{ old('jenis_pt', $pt->jenis_pt) == 'PTN' ? 'selected' : '' }}>PTN</option>
                                    <option value="PTS" {{ old('jenis_pt', $pt->jenis_pt) == 'PTS' ? 'selected' : '' }}>PTS</option>
                                </select>
                                @error('jenis_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="status_pt" class="form-label">Status PT <span class="text-danger">*</span></label>
                                <select name="status_pt" id="status_pt" class="form-select @error('status_pt') is-invalid @enderror" data-select2-selector="default" required>
                                    <option value="">-- Pilih Status PT --</option>
                                    <option value="Aktif" {{ old('status_pt', $pt->status_pt) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tutup" {{ old('status_pt', $pt->status_pt) == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                                    <option value="Merger" {{ old('status_pt', $pt->status_pt) == 'Merger' ? 'selected' : '' }}>Merger</option>
                                    <option value="Berubah Bentuk" {{ old('status_pt', $pt->status_pt) == 'Berubah Bentuk' ? 'selected' : '' }}>Berubah Bentuk</option>
                                    <option value="Berubah Nama" {{ old('status_pt', $pt->status_pt) == 'Berubah Nama' ? 'selected' : '' }}>Berubah Nama</option>
                                    <option value="Pindah Lokasi" {{ old('status_pt', $pt->status_pt) == 'Pindah Lokasi' ? 'selected' : '' }}>Pindah Lokasi</option>
                                    <option value="Penegerian" {{ old('status_pt', $pt->status_pt) == 'Penegerian' ? 'selected' : '' }}>Penegerian</option>
                                    <option value="Pembinaan" {{ old('status_pt', $pt->status_pt) == 'Pembinaan' ? 'selected' : '' }}>Pembinaan</option>
                                    <option value="Tidak Terdata" {{ old('status_pt', $pt->status_pt) == 'Tidak Terdata' ? 'selected' : '' }}>Tidak Terdata</option>
                                </select>
                                @error('status_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Kelembagaan PT (hanya muncul jika Status PT = Aktif atau Pembinaan) -->
                    <div id="statusKelembagaanSection" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label for="status_kelembagaan_pt" class="form-label">Status Kelembagaan PT</label>
                                <select name="status_kelembagaan_pt" id="status_kelembagaan_pt" class="form-select @error('status_kelembagaan_pt') is-invalid @enderror" data-select2-selector="default">
                                    <option value="">-- Pilih Status Kelembagaan --</option>
                                    <option value="Proses pencabutan izin PT" {{ $pt->status_kelembagaan_pt == 'Proses pencabutan izin PT' ? 'selected' : '' }}>Proses pencabutan izin PT</option>
                                    <option value="Proses alih kelola" {{ $pt->status_kelembagaan_pt == 'Proses alih kelola' ? 'selected' : '' }}>Proses alih kelola</option>
                                    <option value="Proses pindah lokasi/binaan" {{ $pt->status_kelembagaan_pt == 'Proses pindah lokasi/binaan' ? 'selected' : '' }}>Proses pindah lokasi/binaan</option>
                                    <option value="Proses penggabungan/penyatuan" {{ $pt->status_kelembagaan_pt == 'Proses penggabungan/penyatuan' ? 'selected' : '' }}>Proses penggabungan/penyatuan</option>
                                    <option value="Proses perubahan nama PT" {{ $pt->status_kelembagaan_pt == 'Proses perubahan nama PT' ? 'selected' : '' }}>Proses perubahan nama PT</option>
                                    <option value="Proses perubahan bentuk" {{ $pt->status_kelembagaan_pt == 'Proses perubahan bentuk' ? 'selected' : '' }}>Proses perubahan bentuk</option>
                                    <option value="Alamat kampus tidak ditemukan" {{ $pt->status_kelembagaan_pt == 'Alamat kampus tidak ditemukan' ? 'selected' : '' }}>Alamat kampus tidak ditemukan</option>
                                    <option value="Alamat kampus berbeda dengan PDDikti" {{ $pt->status_kelembagaan_pt == 'Alamat kampus berbeda dengan PDDikti' ? 'selected' : '' }}>Alamat kampus berbeda dengan PDDikti</option>
                                    <option value="Alamat kampus utama di luar Jakarta" {{ $pt->status_kelembagaan_pt == 'Alamat kampus utama di luar Jakarta' ? 'selected' : '' }}>Alamat kampus utama di luar Jakarta</option>
                                    <option value="Tidak aktivitas perkuliahan" {{ $pt->status_kelembagaan_pt == 'Tidak aktivitas perkuliahan' ? 'selected' : '' }}>Tidak aktivitas perkuliahan</option>
                                    <option value="Dikenai sanksi berat berupa pembinaan" {{ $pt->status_kelembagaan_pt == 'Dikenai sanksi berat berupa pembinaan' ? 'selected' : '' }}>Dikenai sanksi berat berupa pembinaan</option>
                                    @php
                                        $isCustomValue = !in_array($pt->status_kelembagaan_pt, [
                                            'Proses pencabutan izin PT', 'Proses alih kelola', 'Proses pindah lokasi/binaan',
                                            'Proses penggabungan/penyatuan', 'Proses perubahan nama PT', 'Proses perubahan bentuk',
                                            'Alamat kampus tidak ditemukan', 'Alamat kampus berbeda dengan PDDikti',
                                            'Alamat kampus utama di luar Jakarta', 'Tidak aktivitas perkuliahan',
                                            'Dikenai sanksi berat berupa pembinaan'
                                        ]) && !empty($pt->status_kelembagaan_pt);
                                    @endphp
                                    <option value="Lainnya" {{ $isCustomValue ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('status_kelembagaan_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Input khusus untuk "Lainnya" -->
                    <div id="statusKelembagaanLainnyaSection" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label for="status_kelembagaan_lainnya" class="form-label">Status Kelembagaan Lainnya</label>
                                <input type="text" name="status_kelembagaan_lainnya" id="status_kelembagaan_lainnya" class="form-control @error('status_kelembagaan_lainnya') is-invalid @enderror" value="{{ $isCustomValue ? $pt->status_kelembagaan_pt : '' }}" maxlength="50" placeholder="Masukkan status kelembagaan lainnya">
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-muted"><span id="status-lainnya-count">0</span>/50 karakter</small>
                                </div>
                                @error('status_kelembagaan_lainnya')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kontak Pemimpin PT -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="nama_pemimpin_pt" class="form-label">Nama Pemimpin PT</label>
                                <input type="text" name="nama_pemimpin_pt" id="nama_pemimpin_pt" class="form-control @error('nama_pemimpin_pt') is-invalid @enderror" value="{{ old('nama_pemimpin_pt', $pt->nama_pemimpin_pt) }}" maxlength="30" placeholder="Masukkan nama pemimpin PT">
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-muted"><span id="nama-pemimpin-count">0</span>/30 karakter</small>
                                </div>
                                @error('nama_pemimpin_pt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="nomor_kontak_pemimpin" class="form-label">Nomor Kontak Pemimpin</label>
                                <input type="text" name="nomor_kontak_pemimpin" id="nomor_kontak_pemimpin" class="form-control @error('nomor_kontak_pemimpin') is-invalid @enderror" value="{{ old('nomor_kontak_pemimpin', $pt->nomor_kontak_pemimpin) }}" maxlength="25" placeholder="Masukkan nomor kontak">
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-muted"><span id="nomor-kontak-count">0</span>/25 karakter</small>
                                </div>
                                @error('nomor_kontak_pemimpin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Alamat PT -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Alamat Kampus Utama</label>
                                <div id="alamat-utama-container">
                                    @php
                                        $alamatUtama = $pt->addressesByType('utama')->get();
                                    @endphp
                                    @if($alamatUtama->count() > 0)
                                        @foreach($alamatUtama as $index => $alamat)
                                            <div class="address-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="alamat_kampus_utama[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus utama" value="{{ old('alamat_kampus_utama.' . $index, $alamat->address) }}">
                                                    @if($index == 0)
                                                        <button type="button" class="btn btn-success btn-add-address" data-type="utama">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-address">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_utama[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus utama">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="utama">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('alamat_kampus_utama')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Alamat Kampus Perluasan</label>
                                <div id="alamat-perluasan-container">
                                    @php
                                        $alamatPerluasan = $pt->addressesByType('perluasan')->get();
                                    @endphp
                                    @if($alamatPerluasan->count() > 0)
                                        @foreach($alamatPerluasan as $index => $alamat)
                                            <div class="address-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="alamat_kampus_perluasan[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus perluasan" value="{{ old('alamat_kampus_perluasan.' . $index, $alamat->address) }}">
                                                    @if($index == 0)
                                                        <button type="button" class="btn btn-success btn-add-address" data-type="perluasan">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-address">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_perluasan[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus perluasan">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="perluasan">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('alamat_kampus_perluasan')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Alamat Kampus PSDKU</label>
                                <div id="alamat-psdku-container">
                                    @php
                                        $alamatPsdku = $pt->addressesByType('psdku')->get();
                                    @endphp
                                    @if($alamatPsdku->count() > 0)
                                        @foreach($alamatPsdku as $index => $alamat)
                                            <div class="address-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="alamat_kampus_psdku[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PSDKU" value="{{ old('alamat_kampus_psdku.' . $index, $alamat->address) }}">
                                                    @if($index == 0)
                                                        <button type="button" class="btn btn-success btn-add-address" data-type="psdku">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-address">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_psdku[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PSDKU">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="psdku">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('alamat_kampus_psdku')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Alamat Kampus PBJJ</label>
                                <div id="alamat-pbjj-container">
                                    @php
                                        $alamatPbjj = $pt->addressesByType('pbjj')->get();
                                    @endphp
                                    @if($alamatPbjj->count() > 0)
                                        @foreach($alamatPbjj as $index => $alamat)
                                            <div class="address-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="alamat_kampus_pbjj[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PBJJ" value="{{ old('alamat_kampus_pbjj.' . $index, $alamat->address) }}">
                                                    @if($index == 0)
                                                        <button type="button" class="btn btn-success btn-add-address" data-type="pbjj">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-address">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_pbjj[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PBJJ">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="pbjj">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('alamat_kampus_pbjj')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bagian additional yang muncul/hilang -->
                    <div id="additionalInfoSection" class="additional-info-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="tanggal" class="form-label">Tanggal SK</label>
                                    <input type="text" name="tanggal" id="tanggal" class="form-control datepicker @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $pt->tanggal ? date('d-m-Y', strtotime($pt->tanggal)) : '') }}" placeholder="Pilih tanggal">
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="file_sk" class="form-label">File SK (PDF)</label>
                                    <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf">
                                    @if($pt->file_sk)
                                        <small class="text-muted">
                                            File SK saat ini: 
                                            <a href="{{ route('manage-pt.download', $pt->uuid) }}" target="_blank">
                                                {{ $pt->file_sk }}
                                            </a>
                                        </small>
                                    @endif
                                    @error('file_sk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" maxlength="500">{{ old('keterangan', $pt->keterangan) }}</textarea>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Maksimal 500 karakter</small>
                                <small id="charCount" class="text-muted">0 / 500</small>
                            </div>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Tombol aksi -->
                    <div class="form-actions d-flex justify-content-end align-items-center gap-3 mt-4">
                        <a href="{{ route('manage-pt.index') }}" class="text-danger">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>

<script>
    $(document).ready(function() { 
        // Inisialisasi datepicker untuk tanggal SK
        $('#tanggal').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
            language: 'id'
        });

        const additionalInfoSection = document.getElementById('additionalInfoSection');
        const statusKelembagaanSection = document.getElementById('statusKelembagaanSection');
        const statusKelembagaanLainnyaSection = document.getElementById('statusKelembagaanLainnyaSection');
        const keteranganTextarea = document.getElementById('keterangan');
        const charCount = document.getElementById('charCount');
        const tanggalInput = document.getElementById('tanggal');
        
        // Fungsi untuk menampilkan/menyembunyikan bagian tambahan
        function toggleAdditionalInfo() {
            const selectedValue = $('#status_pt').val();

            if (selectedValue === 'Aktif' || selectedValue === 'Pembinaan') {
                // Tampilkan Status Kelembagaan PT
                statusKelembagaanSection.style.display = 'block';

                // Trigger select2 untuk refresh agar dropdown muncul dengan benar
                setTimeout(function() {
                    $('#status_kelembagaan_pt').select2('open');
                    $('#status_kelembagaan_pt').select2('close');
                }, 50);

                // Sembunyikan dan reset input di section additional
                $(additionalInfoSection).removeClass('show');
                setTimeout(function() {
                    $('#tanggal').val('');
                    $('#keterangan').val('');
                    updateCharCount();
                }, 300);
            } else {
                // Sembunyikan Status Kelembagaan PT
                statusKelembagaanSection.style.display = 'none';
                statusKelembagaanLainnyaSection.style.display = 'none';
                $('#status_kelembagaan_pt').val('').trigger('change');
                $('#status_kelembagaan_lainnya').val('');

                // Tampilkan section additional
                $(additionalInfoSection).addClass('show');
            }
        }

        // Fungsi untuk menampilkan/menyembunyikan input lainnya
        function toggleStatusKelembagaanLainnya() {
            const selectedValue = $('#status_kelembagaan_pt').val();

            if (selectedValue === 'Lainnya') {
                statusKelembagaanLainnyaSection.style.display = 'block';
            } else {
                statusKelembagaanLainnyaSection.style.display = 'none';
                $('#status_kelembagaan_lainnya').val('');
                updateStatusLainnyaCount();
            }
        }

        // Fungsi untuk update character count status lainnya
        function updateStatusLainnyaCount() {
            const input = document.getElementById('status_kelembagaan_lainnya');
            const counter = document.getElementById('status-lainnya-count');
            const length = input.value.length;
            counter.textContent = length;

            if (length > 45) {
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-danger');
            }
        }

        // Fungsi untuk menghitung karakter
        function updateCharCount() {
            const maxLength = 500;
            const currentLength = keteranganTextarea.value.length;
            $('#charCount').text(`${currentLength} / ${maxLength}`);

            if (currentLength > maxLength) {
                keteranganTextarea.value = keteranganTextarea.value.slice(0, maxLength);
            }
        }

        // Fungsi untuk update character count kontak pemimpin
        function updateNamaPemimpinCount() {
            const input = document.getElementById('nama_pemimpin_pt');
            const counter = document.getElementById('nama-pemimpin-count');
            const length = input.value.length;
            counter.textContent = length;

            if (length > 25) {
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-danger');
            }
        }

        function updateNomorKontakCount() {
            const input = document.getElementById('nomor_kontak_pemimpin');
            const counter = document.getElementById('nomor-kontak-count');
            const length = input.value.length;
            counter.textContent = length;

            if (length > 20) {
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-danger');
            }
        }

        // Gunakan event listener untuk perubahan status PT
        $('#status_pt').on('change', toggleAdditionalInfo);
        $('#status_kelembagaan_pt').on('change', toggleStatusKelembagaanLainnya);
        $('#status_kelembagaan_lainnya').on('input', updateStatusLainnyaCount);

        // Event listener untuk menghitung karakter
        $('#keterangan').on('input', updateCharCount);
        $('#nama_pemimpin_pt').on('input', updateNamaPemimpinCount);
        $('#nomor_kontak_pemimpin').on('input', updateNomorKontakCount);

        // Dynamic address add/remove functionality
        $(document).on('click', '.btn-add-address', function() {
            const type = $(this).data('type');
            const container = $(`#alamat-${type}-container`);
            const newItem = `
                <div class="address-item mb-2">
                    <div class="input-group">
                        <input type="text" name="alamat_kampus_${type}[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus ${type}">
                        <button type="button" class="btn btn-danger btn-remove-address">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.append(newItem);
        });

        $(document).on('click', '.btn-remove-address', function() {
            $(this).closest('.address-item').remove();
        });

        // Set kondisi awal
        setTimeout(function() {
            toggleAdditionalInfo();
            toggleStatusKelembagaanLainnya();
            updateCharCount();
            updateStatusLainnyaCount();
            updateNamaPemimpinCount();
            updateNomorKontakCount();
        }, 100);

        // Validasi form sebelum submit
        $('#editPtForm').on('submit', function(event) {
            if ($('#status_pt').val() !== 'Aktif') {
                // Validasi jika diperlukan
                if (tanggalInput.hasAttribute('required') && !tanggalInput.value) {
                    event.preventDefault();
                    alert('Tanggal SK harus diisi untuk status selain Aktif');
                    tanggalInput.focus();
                    return false;
                }
            }
        });
    });
</script>
@endpush
@endsection