@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('breadcrumb-title', 'Hasil Pencarian')
@section('breadcrumb-header', 'Beranda')
@section('breadcrumb-home-url', route('dashboard'))
@section('breadcrumb', 'Pencarian')
@section('breadcrumb-active', 'Hasil')

@section('content')
<div class="col-lg-12">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <h5 class="card-title">Hasil Pencarian: "{{ $query }}"</h5>
            <div class="card-header-action">
                <span class="badge bg-primary">{{ $totalResults }} hasil ditemukan</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Form Pencarian -->
            <div class="mb-4">
                <form action="{{ route('search.results') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="feather-search"></i>
                        </span>
                        <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ $query }}" required>
                        <button type="submit" class="btn btn-primary">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Hasil Pencarian -->
            @if($totalResults > 0)
                @foreach($resultsByType as $type => $results)
                    <div class="search-results-section mb-4">
                        <h5 class="text-uppercase border-bottom pb-2 mb-3">
                            @if($type == 'user')
                                <i class="feather-user me-2 text-primary"></i>Pengguna
                            @elseif($type == 'laporan')
                                <i class="feather-file-text me-2 text-primary"></i>Laporan PT
                            @elseif($type == 'perguruan_tinggi')
                                <i class="feather-home me-2 text-primary"></i>Perguruan Tinggi
                            @elseif($type == 'dokumen')
                                <i class="feather-file me-2 text-primary"></i>Dokumen
                            @else
                                <i class="feather-search me-2 text-primary"></i>{{ ucfirst($type) }}
                            @endif
                            <span class="text-muted fs-6">({{ count($results) }})</span>
                        </h5>

                        <div class="row">
                            @foreach($results as $result)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-80 border shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="{{ $result['url'] }}" class="text-decoration-none text-dark">
                                                    {!! preg_replace("/(" . preg_quote($query, '/') . ")/i", '<span class="bg-warning text-dark">$1</span>', $result['title']) !!}
                                                </a>
                                            </h6>
                                            <p class="card-text text-muted small mb-2">{{ $result['description'] }}</p>
                                            <a href="{{ $result['url'] }}" class="btn btn-lg btn-light-primary">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="feather-search fs-1 text-muted mb-3"></i>
                    <h5>Tidak ada hasil yang ditemukan</h5>
                    <p class="text-muted">Coba gunakan kata kunci lain atau periksa ejaan kata kunci Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection