@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css')}}">

<style>
    /* Style untuk card */
    .card {
        height: auto !important;
        min-height: 0 !important;
    }
    
    /* Card body sembunyikan overflow */
    .card-body {
        overflow: hidden;
    }
    
    /* Style untuk form button container */
    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
    }
</style>

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Tambah Pengguna Baru</h5>
        </div>
        <ul class="breadcrumb d-none d-md-flex">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Admin SI-JAKI</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage-users.index')}}">Manajemen Pengguna</a></li>
            <li class="breadcrumb-item">Tambah Pengguna Baru</li>
        </ul>
    </div>
    <!-- Tombol mobile dengan posisi absolute di pojok kanan -->
    <div class="d-block d-md-none position-absolute" style="right: 20px; top: 20px;">
        <a href="{{ route('manage-users.index') }}">
            <i class="feather-arrow-left"></i>
        </a>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center">
                <!-- Tombol untuk desktop -->
                <div class="d-none d-md-block">
                    <a href="{{ route('manage-users.index') }}" class="btn btn-icon btn-light-brand">
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
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Tambah Pengguna Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('manage-users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        <small class="text-muted">Username akan digunakan untuk login ke SI-JAKI</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        <small class="text-muted">Email tidak wajib diisi</small>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="akses" class="form-label">Hak Akses <span class="text-danger">*</span></label>
                        <select name="akses" id="akses" class="form-select @error('akses') is-invalid @enderror" data-select2-selector="default" required>
                            @if(auth()->user()->isDev())
                                <option value="">Pilih Hak Akses</option>
                                <option value="Dev" {{ old('akses') == 'Dev' ? 'selected' : '' }}>Developer</option>
                                <option value="Admin" {{ old('akses') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="User" {{ old('akses') == 'User' ? 'selected' : '' }}>User</option>
                            @else
                                <option value="">Pilih Hak Akses</option>
                                <option value="User" {{ old('akses') == 'User' ? 'selected' : '' }}>User</option>
                                <option value="Admin" {{ old('akses') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                        </select>
                        @error('akses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="pokja_input" class="form-label">Tim Kerja</label>
                        <input type="text" name="pokja_input" id="pokja_input" class="form-control" value="{{ old('pokja_input') }}">
                    </div>
                    
                    <div class="form-actions">
                        <a href="{{ route('manage-users.index') }}" class="text-danger">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>
                            <span>Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
@endpush

@endsection