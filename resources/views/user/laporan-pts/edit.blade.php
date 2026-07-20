@extends('layouts.app')

@section('title', 'Edit Laporan PT ' . $laporan->perguruanTinggi->nama_pt)

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/quill.min.css')}}">

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
</style>

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Edit Laporan PT</h5>
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
            <li class="breadcrumb-item">
                <a href="{{ route('user.laporan-pts.detail', $laporan->uuid) }}">Detail</a>
            </li>
            <li class="breadcrumb-item">Edit</li>
        </ul>
    </div>
    <!-- Tombol mobile dengan posisi absolute di pojok kanan -->
    <div class="d-block d-md-none position-absolute" style="right: 20px; top: 20px;">
        <a href="{{ route('user.laporan-pts.detail', $laporan->uuid) }}">
            <i class="feather-arrow-left"></i>
        </a>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center">
                <!-- Tombol untuk desktop -->
                <div class="d-none d-md-block">
                    <a href="{{ route('user.laporan-pts.detail', $laporan->uuid) }}" class="btn btn-icon btn-light-brand">
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Laporan untuk {{ $laporan->perguruanTinggi->nama_pt }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.laporan-pts.update', $laporan->uuid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_kegiatan') is-invalid @enderror" id="jenis_kegiatan" name="jenis_kegiatan" data-select2-selector="default" required>
                                        <option value="">Pilih Jenis Kegiatan</option>
                                        <option value="Rapat/Audiensi" {{ $laporan->jenis_kegiatan == 'Rapat/Audiensi' ? 'selected' : '' }}>Rapat/Audiensi</option>
                                        <option value="Visitasi" {{ $laporan->jenis_kegiatan == 'Visitasi' ? 'selected' : '' }}>Visitasi</option>
                                        <option value="Monitoring & Evaluasi" {{ $laporan->jenis_kegiatan == 'Monitoring & Evaluasi' ? 'selected' : '' }}>Monitoring & Evaluasi</option>
                                        <option value="Aduan/Laporan" {{ $laporan->jenis_kegiatan == 'Aduan/Laporan' ? 'selected' : '' }}>Aduan/Laporan</option>
                                        <option value="Teguran/Sanksi" {{ $laporan->jenis_kegiatan == 'Teguran/Sanksi' ? 'selected' : '' }}>Teguran/Sanksi</option>
                                    </select>
                                    @error('jenis_kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker @error('tanggal_kegiatan') is-invalid @enderror" id="tanggal_kegiatan" name="tanggal_kegiatan" value="{{ $laporan->tanggal_kegiatan->format('d-m-Y') }}" required>
                                    @error('tanggal_kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tempat_kegiatan" class="form-label">Tempat Kegiatan <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('tempat_kegiatan') is-invalid @enderror"
                                id="tempat_kegiatan"
                                name="tempat_kegiatan"
                                value="{{ $laporan->tempat_kegiatan }}"
                                placeholder="Masukkan tempat kegiatan"
                                maxlength="50"
                                required>
                            <div class="d-flex justify-content-end mt-1">
                                <small class="text-muted"><span id="tempat-count">0</span>/50 karakter</small>
                            </div>
                            @error('tempat_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dokumen_undangan" class="form-label">Dokumen Undangan</label>
                                    <input type="file" class="form-control @error('dokumen_undangan') is-invalid @enderror" id="dokumen_undangan" name="dokumen_undangan" accept=".pdf">
                                    <small class="text-muted">Format: PDF, maks. 2MB</small>
                                    @if($laporan->dokumen_undangan)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/dokumen_laporan_pt/'.$laporan->dokumen_undangan) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="feather-eye me-2"></i> Lihat Dokumen Undangan
                                            </a>
                                        </div>
                                    @endif
                                    @error('dokumen_undangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dokumen_notula" class="form-label">Dokumen Notula
                                        @if(Auth::user()->name !== 'ADIA' && !$laporan->dokumen_notula)
                                        <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('dokumen_notula') is-invalid @enderror" id="dokumen_notula" name="dokumen_notula" accept=".pdf" 
                                        @if(Auth::user()->name !== 'ADIA' && !$laporan->dokumen_notula)
                                        required
                                        @endif
                                    >
                                    <small class="text-muted">Format: PDF, maks. 10MB</small>
                                    @if($laporan->dokumen_notula)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/dokumen_laporan_pt/'.$laporan->dokumen_notula) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="feather-eye me-2"></i> Lihat Dokumen Notula
                                            </a>
                                        </div>
                                    @endif
                                    @error('dokumen_notula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="created_by_name" class="form-label">Pembuat Laporan <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('created_by_name') is-invalid @enderror"
                                id="created_by_name"
                                name="created_by_name"
                                value="{{ old('created_by_name', $laporan->created_by) }}"
                                placeholder="Masukkan nama lengkap pembuat laporan"
                                maxlength="35"
                                required>
                            <div class="d-flex justify-content-end mt-1">
                                <small class="text-muted"><span id="pembuat-count">0</span>/35 karakter</small>
                            </div>
                            @error('created_by_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="resume" class="form-label">Ringkasan Kegiatan <span class="text-danger">*</span></label>
                            <div id="editor-container" style="height: 200px; border: 1px solid #ced4da; border-radius: 0.375rem;">
                                <div id="resume-editor" style="height: 150px;"></div>
                            </div>
                            <textarea name="resume" id="resume" style="display: none;" required>{{ $laporan->resume }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Gunakan bold, italic, ordered list, dan unordered list untuk format teks</small>
                                <small class="text-muted"><span id="char-count">0</span>/500 karakter</small>
                            </div>
                            @error('resume')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Lingkup Tim Kerja <span class="text-danger">*</span></label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($users as $user)
                                            @if($user->name !== 'DEVELOPER' && $user->name !== 'ADMINKLK')
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="pokja[]" id="user{{ $user->id }}" value="{{ $user->id }}" {{ (is_array($laporan->pokja) && in_array($user->id, $laporan->pokja)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="user{{ $user->id }}">
                                                        {{ $user->pokja }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('user.laporan-pts.detail', $laporan->uuid) }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">Perbarui Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/quill.min.js') }}"></script>

<script>
    // Tambahkan di bagian script yang sudah ada
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi datepicker untuk tanggal SK
        $('#tanggal_kegiatan').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
            language: 'id'
        });

        // Inisialisasi Quill Rich Text Editor
        var quill = new Quill('#resume-editor', {
            modules: {
                toolbar: [
                    ['bold', 'italic'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            },
            placeholder: 'Masukkan resume kegiatan',
            theme: 'snow'
        });

        // Set nilai awal dari database
        quill.root.innerHTML = {!! json_encode($laporan->resume) !!};

        // Update hidden textarea saat konten berubah
        quill.on('text-change', function() {
            var html = quill.root.innerHTML;
            var text = quill.getText();

            // Update hidden textarea
            document.getElementById('resume').value = html;

            // Update character count
            updateCharCount(text.length - 1); // -1 untuk menghapus karakter newline terakhir
        });

        // Validasi panjang teks
        quill.on('text-change', function() {
            var text = quill.getText();
            var length = text.length - 1; // -1 untuk menghapus karakter newline terakhir

            if (length > 500) {
                // Potong teks jika melebihi batas
                var delta = quill.getContents();
                quill.setContents(delta.ops.slice(0, -1));
            }
        });
        // Fungsi untuk update counter
        function updateCharCount(length) {
            const charCount = document.getElementById('char-count');
            charCount.textContent = length;

            // Tambahkan warna merah jika mendekati batas
            if (length > 450) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        }

        // Counter: Tempat Kegiatan (max 50)
        const tempatInput = document.querySelector('input[name="tempat_kegiatan"]');
        const tempatCount = document.getElementById('tempat-count');
        if (tempatInput && tempatCount) {
            function updateTempatCount() {
                const len = tempatInput.value.length;
                tempatCount.textContent = len;
                if (len > 50) tempatCount.classList.add('text-danger'); else tempatCount.classList.remove('text-danger');
            }
            updateTempatCount();
            tempatInput.addEventListener('input', updateTempatCount);
        }

        // Counter: Pembuat Laporan (max 50)
        const pembuatInput = document.querySelector('input[name="created_by_name"]');
        const pembuatCount = document.getElementById('pembuat-count');
        if (pembuatInput && pembuatCount) {
            function updatePembuatCount() {
                const len = pembuatInput.value.length;
                pembuatCount.textContent = len;
                if (len > 35) pembuatCount.classList.add('text-danger'); else pembuatCount.classList.remove('text-danger');
            }
            updatePembuatCount();
            pembuatInput.addEventListener('input', updatePembuatCount);
        }
        // Initial character count berdasarkan konten yang sudah ada
        var initialText = quill.getText();
        updateCharCount(initialText.length - 1);
    });
</script>
@endpush
@endsection