@extends('layouts.app')

@section('title', 'Laporan Perguruan Tinggi Swasta')

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Laporan PTS</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Menu Utama</a></li>
            <li class="breadcrumb-item">Laporan PTS</li>
        </ul>
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
                        <table class="table table-hover" id="ptsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode PT</th>
                                    <th>Nama Perguruan Tinggi</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perguruanTinggi as $index => $pt)
                                <tr class="single-item">
                                    <td>
                                        <div class="avatar-text bg-gray-200">{{ $index + 1 }}</div>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $pt->kode_pt }}</td>
                                    <td class="fw-bold text-dark">{{ $pt->nama_pt }}</td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('user.laporan-pts.create', ['pt_uuid' => $pt->uuid]) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-file-plus me-1"></i> Buat Laporan
                                            </a>
                                            <a href="{{ route('user.laporan-pts.list', ['pt_uuid' => $pt->uuid]) }}" class="btn btn-sm btn-info">
                                                <i class="feather-list me-1"></i> Lihat Laporan
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="feather-alert-circle fs-2 mb-3"></i>
                                            <p class="mb-2">Belum ada data Perguruan Tinggi Swasta yang aktif</p>
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
        const ptsTable = $('#ptsTable').DataTable({
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