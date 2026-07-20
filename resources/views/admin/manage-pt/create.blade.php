@extends('layouts.app')

@section('title', 'Tambah Perguruan Tinggi')

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

    /* Tambahan style untuk tab input method */
    .input-method-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .input-method-tabs .tab {
        padding: 10px 20px;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 5px 5px 0 0;
        margin-right: 10px;
        margin-bottom: -1px;
    }
    
    .input-method-tabs .tab.active {
        background-color: #fff;
        border-color: #dee2e6;
        border-bottom-color: #fff;
        font-weight: 500;
    }
    
    .input-method-tabs .tab:hover:not(.active) {
        background-color: #f8f9fa;
    }
    
    .input-method-content {
        display: none;
    }
    
    .input-method-content.active {
        display: block;
    }
    
    .csv-upload-zone {
        border: 2px dashed #ddd;
        padding: 30px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .csv-upload-zone:hover {
        border-color: #3639DF;
        background-color: #f0f0ff;
    }
    
    .csv-upload-zone i {
        font-size: 16px;
        color: #6c757d;
        margin-bottom: 5px;
        display: block;
    }
    
    .csv-upload-zone.dragover {
        border-color: #3639DF;
        background-color: #f0f0ff;
    }
    
    #csvFileInfo {
        display: none;
        margin-top: 15px;
        padding: 5px;
        border-radius: 5px;
        background-color: #e9ecef;
    }
    
    /* Style untuk mengatur section tambahan */
    .additional-info-section {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease, opacity 0.3s ease;
        opacity: 0;
        margin: 0;
        padding: 0;
    }
    
    .additional-info-section.show {
        max-height: 500px;
        opacity: 1;
        margin-bottom: 20px;
    }
    
    /* Style untuk card */
    .card {
        height: auto !important;
        min-height: 0 !important;
    }
    
    /* Style untuk form button container */
    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
    }
    
    /* Card body sembunyikan overflow */
    .card-body {
        overflow: hidden;
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
            <li class="breadcrumb-item"><a href="{{ route('manage-pt.index')}}">Tambah Data PT</a></li>
            <li class="breadcrumb-item">Manajemen PT</li>
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
                <h5 class="card-title">Tambah Perguruan Tinggi Baru</h5>
            </div>
            <div class="card-body">
                <!-- Tab selector untuk metode input -->
                <div class="input-method-tabs">
                    <div class="tab active" id="manualInputTab">Input Manual</div>
                    @if(auth()->user()->hasRole('Dev'))
                    <div class="tab" id="csvUploadTab">Upload File CSV</div>
                    @endif
                </div>
                
                <!-- Form input manual -->
                <div class="input-method-content active" id="manualInputContent">
                    <form action="{{ route('manage-pt.store') }}" method="POST" enctype="multipart/form-data" id="createPtForm">
                        @csrf
                        <input type="hidden" name="input_method" value="manual">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="kode_pt" class="form-label">Kode PT <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_pt" id="kode_pt" class="form-control @error('kode_pt') is-invalid @enderror" value="{{ old('kode_pt') }}" required>
                                    @error('kode_pt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="nama_pt" class="form-label">Nama PT Sesuai PDDikti <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pt" id="nama_pt" class="form-control @error('nama_pt') is-invalid @enderror" value="{{ old('nama_pt') }}" required>
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
                                    <input type="text" name="nama_pt_sk" id="nama_pt_sk" class="form-control @error('nama_pt_sk') is-invalid @enderror" value="{{ old('nama_pt_sk') }}">
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
                                        <option value="PTN" {{ old('jenis_pt') == 'PTN' ? 'selected' : '' }}>PTN</option>
                                        <option value="PTS" {{ old('jenis_pt') == 'PTS' ? 'selected' : '' }}>PTS</option>
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
                                        <option value="Aktif" {{ old('status_pt') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tutup" {{ old('status_pt') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                                        <option value="Merger" {{ old('status_pt') == 'Merger' ? 'selected' : '' }}>Merger</option>
                                        <option value="Berubah Bentuk" {{ old('status_pt') == 'Berubah Bentuk' ? 'selected' : '' }}>Berubah Bentuk</option>
                                        <option value="Berubah Nama" {{ old('status_pt') == 'Berubah Nama' ? 'selected' : '' }}>Berubah Nama</option>
                                        <option value="Pindah Lokasi" {{ old('status_pt') == 'Pindah Lokasi' ? 'selected' : '' }}>Pindah Lokasi</option>
                                        <option value="Penegerian" {{ old('status_pt') == 'Penegerian' ? 'selected' : '' }}>Penegerian</option>
                                        <option value="Pembinaan" {{ old('status_pt') == 'Pembinaan' ? 'selected' : '' }}>Pembinaan</option>
                                        <option value="Tidak Terdata" {{ old('status_pt') == 'Tidak Terdata' ? 'selected' : '' }}>Tidak Terdata</option>
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
                                        <option value="Proses pencabutan izin PT" {{ old('status_kelembagaan_pt') == 'Proses pencabutan izin PT' ? 'selected' : '' }}>Proses pencabutan izin PT</option>
                                        <option value="Proses alih kelola" {{ old('status_kelembagaan_pt') == 'Proses alih kelola' ? 'selected' : '' }}>Proses alih kelola</option>
                                        <option value="Proses pindah lokasi/binaan" {{ old('status_kelembagaan_pt') == 'Proses pindah lokasi/binaan' ? 'selected' : '' }}>Proses pindah lokasi/binaan</option>
                                        <option value="Proses penggabungan/penyatuan" {{ old('status_kelembagaan_pt') == 'Proses penggabungan/penyatuan' ? 'selected' : '' }}>Proses penggabungan/penyatuan</option>
                                        <option value="Proses perubahan nama PT" {{ old('status_kelembagaan_pt') == 'Proses perubahan nama PT' ? 'selected' : '' }}>Proses perubahan nama PT</option>
                                        <option value="Proses perubahan bentuk" {{ old('status_kelembagaan_pt') == 'Proses perubahan bentuk' ? 'selected' : '' }}>Proses perubahan bentuk</option>
                                        <option value="Alamat kampus tidak ditemukan" {{ old('status_kelembagaan_pt') == 'Alamat kampus tidak ditemukan' ? 'selected' : '' }}>Alamat kampus tidak ditemukan</option>
                                        <option value="Alamat kampus berbeda dengan PDDikti" {{ old('status_kelembagaan_pt') == 'Alamat kampus berbeda dengan PDDikti' ? 'selected' : '' }}>Alamat kampus berbeda dengan PDDikti</option>
                                        <option value="Alamat kampus utama di luar Jakarta" {{ old('status_kelembagaan_pt') == 'Alamat kampus utama di luar Jakarta' ? 'selected' : '' }}>Alamat kampus utama di luar Jakarta</option>
                                        <option value="Tidak aktivitas perkuliahan" {{ old('status_kelembagaan_pt') == 'Tidak aktivitas perkuliahan' ? 'selected' : '' }}>Tidak aktivitas perkuliahan</option>
                                        <option value="Dikenai sanksi berat berupa pembinaan" {{ old('status_kelembagaan_pt') == 'Dikenai sanksi berat berupa pembinaan' ? 'selected' : '' }}>Dikenai sanksi berat berupa pembinaan</option>
                                        <option value="Lainnya" {{ old('status_kelembagaan_pt') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                    <input type="text" name="status_kelembagaan_lainnya" id="status_kelembagaan_lainnya" class="form-control @error('status_kelembagaan_lainnya') is-invalid @enderror" value="{{ old('status_kelembagaan_lainnya') }}" maxlength="50" placeholder="Masukkan status kelembagaan lainnya">
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
                                    <input type="text" name="nama_pemimpin_pt" id="nama_pemimpin_pt" class="form-control @error('nama_pemimpin_pt') is-invalid @enderror" value="{{ old('nama_pemimpin_pt') }}" maxlength="30" placeholder="Masukkan nama pemimpin PT">
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
                                    <input type="text" name="nomor_kontak_pemimpin" id="nomor_kontak_pemimpin" class="form-control @error('nomor_kontak_pemimpin') is-invalid @enderror" value="{{ old('nomor_kontak_pemimpin') }}" maxlength="25" placeholder="Masukkan nomor kontak">
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
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_utama[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus utama">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="utama">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
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
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_perluasan[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus perluasan">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="perluasan">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
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
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_psdku[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PSDKU">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="psdku">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
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
                                        <div class="address-item mb-2">
                                            <div class="input-group">
                                                <input type="text" name="alamat_kampus_pbjj[]" class="form-control" maxlength="255" placeholder="Masukkan alamat kampus PBJJ">
                                                <button type="button" class="btn btn-success btn-add-address" data-type="pbjj">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @error('alamat_kampus_pbjj')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="additionalInfoSection" class="additional-info-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                                        <input type="text" name="tanggal" id="tanggal_sk" class="form-control datepicker @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}" placeholder="Pilih tanggal">
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="file_sk" class="form-label">File SK (PDF)</label>
                                        <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf">
                                        <small class="text-muted">Unggah file SK dalam format PDF (maksimal 2MB)</small>
                                        @error('file_sk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" maxlength="500">{{ old('keterangan') }}</textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Maksimal 500 karakter</small>
                                    <small id="charCount" class="text-muted">0 / 500</small>
                                </div>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('manage-pt.index') }}" class="text-danger">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>
                                <span>Simpan Data</span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Form upload CSV -->
                @if(auth()->user()->hasRole('Dev'))
                <div class="input-method-content" id="csvUploadContent">
                    <form action="{{ route('manage-pt.import') }}" method="POST" enctype="multipart/form-data" id="importCsvForm">
                        @csrf
                        <input type="hidden" name="input_method" value="csv">
                        
                        <div class="alert alert-info">
                            <i class="feather-info me-2"></i>
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                <li>Format kolom CSV (header persis): <strong>Kode PT, Nama PT, Jenis PT, Status PT, Tanggal SK, Keterangan</strong>.</li>
                                <li>Header tidak peka huruf besar-kecil; urutan kolom harus sama.</li>
                                <li><strong>Jenis PT</strong> valid: <code>PTN</code> atau <code>PTS</code> (boleh tulis <em>Negeri</em>→PTN, <em>Swasta</em>→PTS).</li>
                                <li><strong>Status PT</strong> valid: Aktif, Tutup, Merger, Berubah Bentuk, Berubah Nama, Pindah Lokasi, Penegerian, Pembinaan, Tidak Terdata.</li>
                                <li>Format tanggal: <code>DD-MM-YYYY</code>, <code>YYYY-MM-DD</code>, <code>DD/MM/YYYY</code>, atau serial Excel; gunakan <code>-</code> bila tidak ada.</li>
                                <li>Anda bisa memakai file dari <strong>Export</strong> langsung (tanpa ubah struktur) atau unduh <strong>Template</strong>.</li>
                            </ul>
                        </div>
                        
                        <div class="csv-upload-zone" id="csvDropZone" onclick="document.getElementById('csvFileInput').click()">
                            <input type="file" name="csv_file" id="csvFileInput" class="d-none" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <i class="feather-upload-cloud"></i>
                            <h5>Drag & drop file CSV di sini</h5>
                            <p class="mb-3">atau</p>
                            <div class="mb-3">
                                <i class="feather-file me-2"></i>
                                <span>Pilih File</span>
                            </div>
                            <p class="mt-2 mb-0 text-muted">Format file: .csv (maksimal 5MB)</p>
                        </div>
                        
                        <div id="csvFileInfo" class="mb-2">
                            <div class="d-flex align-items-center mb-2">
                                <i class="feather-file-text me-2"></i>
                                <span id="csvFileName">filename.csv</span>
                                <span class="badge bg-primary ms-2" id="csvFileSize">0 KB</span>
                                <button type="button" class="btn btn-sm text-danger ms-auto" id="removeCsvBtn">
                                    <i class="feather-x"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-check my-2">
                            <input class="form-check-input" type="checkbox" value="1" id="allow_duplicates" name="allow_duplicates">
                            <label class="form-check-label" for="allow_duplicates">
                                Simpan duplikasi Kode PT (masukkan semua baris meski Kode PT sama)
                            </label>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('manage-pt.index') }}" class="text-danger">Batal</a>
                            <button type="submit" class="btn btn-primary" id="importCsvBtn" disabled>
                                <i class="feather-upload me-2"></i>
                                <span>Import Data</span>
                            </button>
                            <a href="{{ route('manage-pt.template') }}" class="btn btn-outline-secondary">
                                <i class="feather-download me-2"></i>
                                <span>Download Template</span>
                            </a>
                        </div>
                    </form>
                </div>
                @endif
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
        $('#tanggal_sk').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
            language: 'id'
        });

        // Tab switching logic
        $('.input-method-tabs .tab').click(function() {
            $('.input-method-tabs .tab').removeClass('active');
            $(this).addClass('active');
            
            const targetId = $(this).attr('id').replace('Tab', 'Content');
            $('.input-method-content').removeClass('active');
            $('#' + targetId).addClass('active');
        });
        
        // Manual input form logic
        const statusPtSelect = document.getElementById('status_pt');
        const additionalInfoSection = document.getElementById('additionalInfoSection');
        const statusKelembagaanSection = document.getElementById('statusKelembagaanSection');
        const statusKelembagaanLainnyaSection = document.getElementById('statusKelembagaanLainnyaSection');
        const keteranganTextarea = document.getElementById('keterangan');
        const charCount = document.getElementById('charCount');
        const tanggalSkInput = document.getElementById('tanggal_sk');
        const fileSKInput = document.getElementById('file_sk');

        // Fungsi untuk menampilkan/menyembunyikan bagian tambahan
        function toggleAdditionalInfo() {
            // Perhatikan bahwa kita perlu menggunakan nilai dari Select2
            const selectedValue = $('#status_pt').val();

            if (selectedValue === 'Aktif' || selectedValue === 'Pembinaan') {
                // Tampilkan Status Kelembagaan PT
                statusKelembagaanSection.style.display = 'block';

                // Trigger select2 untuk refresh agar dropdown muncul dengan benar
                setTimeout(function() {
                    $('#status_kelembagaan_pt').select2('open');
                    $('#status_kelembagaan_pt').select2('close');
                }, 50);

                // Sembunyikan bagian tambahan dengan animasi CSS
                $(additionalInfoSection).removeClass('show');
                setTimeout(function() {
                    // Reset input setelah animasi selesai
                    $('#tanggal_sk').val('');
                    tanggalSkInput.removeAttribute('required');
                    $('#file_sk').val('');
                    $('#keterangan').val('');
                    updateCharCount();
                }, 300);
            } else if (selectedValue && selectedValue !== '') {
                // Hanya tampilkan additional section jika status PT sudah dipilih dan bukan Aktif/Pembinaan
                // Sembunyikan Status Kelembagaan PT
                statusKelembagaanSection.style.display = 'none';
                statusKelembagaanLainnyaSection.style.display = 'none';
                $('#status_kelembagaan_pt').val('').trigger('change');
                $('#status_kelembagaan_lainnya').val('');

                // Tampilkan bagian tambahan dengan animasi CSS
                $(additionalInfoSection).addClass('show');
                tanggalSkInput.setAttribute('required', 'required');
            } else {
                // Jika belum ada pilihan, sembunyikan semua
                statusKelembagaanSection.style.display = 'none';
                statusKelembagaanLainnyaSection.style.display = 'none';
                $(additionalInfoSection).removeClass('show');
                tanggalSkInput.removeAttribute('required');
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

            // Potong teks jika melebihi 500 karakter
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

        // Event listeners untuk form manual
        $('#status_pt').on('change', toggleAdditionalInfo);
        $('#status_kelembagaan_pt').on('change', toggleStatusKelembagaanLainnya);
        $('#status_kelembagaan_lainnya').on('input', updateStatusLainnyaCount);
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

        // Inisialisasi tampilan saat halaman dimuat
        setTimeout(function() {
            toggleAdditionalInfo();
            toggleStatusKelembagaanLainnya();
            updateCharCount();
            updateStatusLainnyaCount();
            updateNamaPemimpinCount();
            updateNomorKontakCount();
        }, 100);

        // Validasi form manual sebelum submit
        $('#createPtForm').on('submit', function(event) {
            if ($('#status_pt').val() !== 'Aktif' && $('#status_pt').val() !== 'Pembinaan') {
                // Validasi tanggal SK jika diperlukan
                if (tanggalSkInput.hasAttribute('required') && !tanggalSkInput.value) {
                    event.preventDefault();
                    alert('Tanggal SK harus diisi untuk status selain Aktif dan Pembinaan');
                    tanggalSkInput.focus();
                    return false;
                }
            }
        });
        
        // CSV Upload logic
        const csvDropZone = document.getElementById('csvDropZone');
        const csvFileInput = document.getElementById('csvFileInput');
        const csvFileInfo = document.getElementById('csvFileInfo');
        const csvFileName = document.getElementById('csvFileName');
        const csvFileSize = document.getElementById('csvFileSize');
        const removeCsvBtn = document.getElementById('removeCsvBtn');
        const importCsvBtn = document.getElementById('importCsvBtn');
        
        // Fungsi untuk format ukuran file
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else return (bytes / 1048576).toFixed(1) + ' MB';
        }
        
        // Event drag & drop untuk upload CSV
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            csvDropZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            csvDropZone.addEventListener(eventName, function() {
                csvDropZone.classList.add('dragover');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            csvDropZone.addEventListener(eventName, function() {
                csvDropZone.classList.remove('dragover');
            });
        });
        
        csvDropZone.addEventListener('drop', function(e) {
            const droppedFile = e.dataTransfer.files[0];
            if (isValidCsvFile(droppedFile)) {
                csvFileInput.files = e.dataTransfer.files;
                updateFileInfo(droppedFile);
            }
        });
        
        // Event ketika file dipilih melalui file input
        csvFileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                if (isValidCsvFile(file)) {
                    updateFileInfo(file);
                } else {
                    resetFileInput();
                }
            }
        });
        
        // Fungsi untuk memvalidasi file CSV/Excel
        function isValidCsvFile(file) {
            const validExtensions = ['.csv'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            // Validasi ekstensi file
            const fileName = file.name;
            const fileExt = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();
            
            if (!validExtensions.includes(fileExt)) {
                alert('File harus berformat CSV, XLS, atau XLSX.');
                return false;
            }
            
            // Validasi ukuran file
            if (file.size > maxSize) {
                alert('Ukuran file tidak boleh melebihi 5MB.');
                return false;
            }
            
            return true;
        }
        
        // Fungsi untuk update info file
        function updateFileInfo(file) {
            csvFileName.textContent = file.name;
            csvFileSize.textContent = formatFileSize(file.size);
            csvFileInfo.style.display = 'block';
            importCsvBtn.disabled = false;
        }
        
        // Fungsi untuk reset file input
        function resetFileInput() {
            csvFileInput.value = '';
            csvFileInfo.style.display = 'none';
            importCsvBtn.disabled = true;
        }
        
        // Event untuk menghapus file
        removeCsvBtn.addEventListener('click', resetFileInput);
        
        // Validasi form import CSV sebelum submit
        $('#importCsvForm').on('submit', function(event) {
            if (!csvFileInput.files.length) {
                event.preventDefault();
                alert('Silakan pilih file CSV terlebih dahulu.');
                return false;
            }
        });
    });

    // Aktifkan tab CSV jika datang dari redirect import
    (function(){
        var activeTab = @json(session('active_tab', old('input_method', 'manual')));
        if (activeTab === 'csv') {
            document.getElementById('manualInputTab')?.classList.remove('active');
            document.getElementById('manualInputContent')?.classList.remove('active');
            document.getElementById('csvUploadTab')?.classList.add('active');
            document.getElementById('csvUploadContent')?.classList.add('active');
        }
    })();

</script>
@endpush
@endsection