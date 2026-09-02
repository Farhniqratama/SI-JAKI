@extends('layouts.app')

@section('title', 'Data Science & AI Hub')

@push('styles')
<style>
    .ai-hero-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 18px;
        padding: 28px;
        color: #ffffff;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.35);
        position: relative;
        overflow: hidden;
    }
    .ai-hero-card::before {
        content: "";
        position: absolute;
        width: 350px;
        height: 350px;
        right: -90px;
        top: -110px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%);
        pointer-events: none;
    }
    .ai-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.4);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    .kpi-ai-card {
        background: var(--sijaki-card-bg, #ffffff);
        border: 1px solid var(--sijaki-border-color, rgba(15, 23, 42, 0.08));
        border-radius: 16px;
        padding: 22px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kpi-ai-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }
    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .nav-ai-tabs .nav-link {
        color: var(--sijaki-muted-color, #64748b);
        font-weight: 600;
        padding: 12px 18px;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        margin-right: 6px;
        font-size: 13px;
    }
    .nav-ai-tabs .nav-link.active {
        color: #ffffff;
        background: var(--primary-blue, #2B79B4);
        box-shadow: 0 4px 12px rgba(43, 121, 180, 0.3);
    }
    .risk-meter-bar {
        height: 8px;
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    .keyword-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin: 4px;
        background: rgba(59, 130, 246, 0.08);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
        transition: all 0.2s;
    }
    .keyword-pill:hover {
        transform: scale(1.05);
        background: #2563eb;
        color: #ffffff;
    }
    .action-card {
        background: var(--sijaki-soft-bg, #f8fafc);
        border: 1px solid var(--sijaki-border-color, rgba(15, 23, 42, 0.08));
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 14px;
        transition: border-color 0.2s;
    }
    .action-card:hover {
        border-color: #3b82f6;
    }
    .phase-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .letter-draft-box {
        background: #1e293b;
        color: #f1f5f9;
        font-family: 'Courier New', Courier, monospace;
        font-size: 12px;
        padding: 18px;
        border-radius: 10px;
        white-space: pre-wrap;
        max-height: 380px;
        overflow-y: auto;
        line-height: 1.6;
    }
    .pagination-custom .page-link {
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid rgba(15, 23, 42, 0.1);
        color: #334155;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 14px;
    }
    .pagination-custom .page-item.active .page-link {
        background-color: var(--primary-blue, #2B79B4);
        border-color: var(--primary-blue, #2B79B4);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="row">
        <!-- AI Hero Banner -->
        <div class="col-12 mb-4">
            <div class="ai-hero-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="ai-badge mb-2">
                            <i class="feather-cpu"></i> 100% Data Science & AI Problem Solver
                        </div>
                        <h2 class="text-white fw-bold mb-2">SI-JAKI Data Science & AI Problem Solver Hub</h2>
                        <p class="text-white-50 mb-0 max-w-750">
                            Pusat pemecahan masalah cerdas (*AI Problem Solver*), analisis prediktif risiko kelembagaan, ekstraksi isu notula (NLP), dan *K-Means Profiling Segmentasi* untuk pengawasan akurat perguruan tinggi.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-warning text-dark fw-bold d-flex align-items-center gap-2 py-2 px-3 shadow-sm rounded-3" onclick="switchToProblemSolverTab()">
                            <i class="feather-zap"></i> Solusi Masalah AI
                        </button>
                        <form action="{{ route('data-science.recalculate') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-light d-flex align-items-center gap-2 py-2 px-3 shadow-sm rounded-3">
                                <i class="feather-refresh-cw"></i> Hitung Ulang Model
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="col-12 mb-3">
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- 4 Top KPI Cards -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="kpi-ai-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold fs-13">Total Kampus Dianalisis</span>
                    <div class="kpi-icon-box bg-primary-subtle text-primary">
                        <i class="feather-grid"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">{{ $kpi['total_pt_analyzed'] }} <span class="fs-14 fw-normal text-muted">PT</span></h3>
                    <div class="fs-12 text-muted">
                        <i class="feather-database text-primary me-1"></i> {{ $kpi['total_laporan_processed'] }} Dokumen Laporan Terproses
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="kpi-ai-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold fs-13">Rata-rata Skor Risiko</span>
                    <div class="kpi-icon-box bg-info-subtle text-info">
                        <i class="feather-activity"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">{{ $kpi['average_risk_score'] }} <span class="fs-14 fw-normal text-muted">/ 100</span></h3>
                    <div class="risk-meter-bar mb-2">
                        <div class="h-100 bg-info" style="width: {{ min(100, $kpi['average_risk_score']) }}%"></div>
                    </div>
                    <div class="fs-12 text-muted">Indeks Kerentanan Lembaga Keseluruhan</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="kpi-ai-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold fs-13">Kampus Perlu Atensi / Kritis</span>
                    <div class="kpi-icon-box bg-danger-subtle text-danger">
                        <i class="feather-alert-triangle"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-danger">{{ $kpi['high_risk_count'] }} <span class="fs-14 fw-normal text-muted">PT</span></h3>
                    <div class="fs-12 text-muted">
                        <span class="badge bg-danger-subtle text-danger">Early Warning Active</span> Prioritas pembinaan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="kpi-ai-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold fs-13">Isu Dominan (NLP Engine)</span>
                    <div class="kpi-icon-box bg-warning-subtle text-warning">
                        <i class="feather-message-square"></i>
                    </div>
                </div>
                <div>
                    <h5 class="fw-bold text-truncate mb-1" title="{{ $kpi['dominant_global_topic'] }}">{{ $kpi['dominant_global_topic'] }}</h5>
                    <div class="fs-12 text-muted">
                        <i class="feather-tag text-warning me-1"></i> Tingkat Urgensi NLP: {{ $nlp['urgency_score'] }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs for Data Science & AI Hub -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pb-0 pt-3 px-4">
                    <ul class="nav nav-pills nav-ai-tabs" id="aiTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ews-tab" data-bs-toggle="tab" data-bs-target="#ews-content" type="button" role="tab">
                                <i class="feather-shield me-2"></i> 1. Matriks EWS & Paginasi PT
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="solver-tab" data-bs-toggle="tab" data-bs-target="#solver-content" type="button" role="tab">
                                <i class="feather-zap me-2 text-warning"></i> 2. AI Problem Solver (Pemecah Masalah)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cluster-tab" data-bs-toggle="tab" data-bs-target="#cluster-content" type="button" role="tab">
                                <i class="feather-pie-chart me-2"></i> 3. K-Means Clustering Profiling
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nlp-tab" data-bs-toggle="tab" data-bs-target="#nlp-content" type="button" role="tab">
                                <i class="feather-file-text me-2"></i> 4. NLP Text Mining & Isu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rec-tab" data-bs-toggle="tab" data-bs-target="#rec-content" type="button" role="tab">
                                <i class="feather-compass me-2"></i> 5. Rekomendasi Aksi Cerdas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sim-tab" data-bs-toggle="tab" data-bs-target="#sim-content" type="button" role="tab">
                                <i class="feather-sliders me-2"></i> 6. Simulator Risiko Realtime
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="aiTabContent">
                        
                        <!-- TAB 1: Early Warning System (EWS) Matrix dengan PAGINATION LENGKAP -->
                        <div class="tab-pane fade show active" id="ews-content" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                                <div>
                                    <h5 class="fw-bold mb-1">Daftar Analisis Early Warning System (EWS) & Risiko PT</h5>
                                    <p class="text-muted fs-13 mb-0">Prediksi probabilistik risiko sanksi/penutupan kampus berdasarkan histori kegiatan & analisis teks NLP.</p>
                                </div>
                                <form method="GET" action="{{ route('data-science.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fs-12 text-muted">Tampilkan:</span>
                                        <select name="per_page" class="form-select form-select-sm" style="width: 75px;" onchange="this.form.submit()">
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kode / Nama PT..." value="{{ $currentSearch }}" style="width: 180px;">
                                    <select name="cluster" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                                        <option value="">Semua Klaster</option>
                                        @foreach($clusters as $cName => $cData)
                                            <option value="{{ $cName }}" {{ $currentCluster == $cName ? 'selected' : '' }}>{{ $cData['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <select name="risk_level" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                                        <option value="">Semua Risiko</option>
                                        <option value="success" {{ $currentRisk == 'success' ? 'selected' : '' }}>Sehat</option>
                                        <option value="info" {{ $currentRisk == 'info' ? 'selected' : '' }}>Perhatian</option>
                                        <option value="warning" {{ $currentRisk == 'warning' ? 'selected' : '' }}>Waspada</option>
                                        <option value="danger" {{ $currentRisk == 'danger' ? 'selected' : '' }}>Kritis</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="feather-search"></i></button>
                                    @if($currentSearch || $currentCluster || $currentRisk || $perPage != 10)
                                        <a href="{{ route('data-science.index') }}" class="btn btn-sm btn-light" title="Reset Filter"><i class="feather-x"></i></a>
                                    @endif
                                </form>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-hover align-middle border-top">
                                    <thead class="table-light fs-12 text-uppercase text-muted">
                                        <tr>
                                            <th>Perguruan Tinggi</th>
                                            <th>Status</th>
                                            <th>Skor Risiko AI</th>
                                            <th>Tingkat Risiko</th>
                                            <th>Isu Dominan (NLP)</th>
                                            <th>Klaster K-Means</th>
                                            <th class="text-center">Solusi & Profil AI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($campuses as $campus)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $campus['nama_pt'] }}</div>
                                                    <div class="text-muted fs-12">Kode: {{ $campus['kode_pt'] }} | {{ $campus['jenis_pt'] }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $campus['status_pt'] }}</span>
                                                </td>
                                                <td style="min-width: 140px;">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold fs-13">{{ $campus['RiskScore'] }}%</span>
                                                    </div>
                                                    <div class="risk-meter-bar">
                                                        <div class="h-100 bg-{{ $campus['RiskBadge'] }}" style="width: {{ min(100, $campus['RiskScore']) }}%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $campus['RiskBadge'] }}-subtle text-{{ $campus['RiskBadge'] }} px-2 py-1">
                                                        {{ $campus['RiskLevel'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fs-12 fw-medium text-muted"><i class="feather-tag me-1 text-primary"></i>{{ $campus['DominantIssue'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $campus['ClusterColor'] }}20; color: {{ $campus['ClusterColor'] }}; border: 1px solid {{ $campus['ClusterColor'] }}40;">
                                                        {{ $campus['ClusterName'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-semibold" onclick="triggerSolverForPt('{{ $campus['uuid'] }}', '{{ addslashes($campus['nama_pt']) }}')" title="Pecahkan Masalah dengan AI">
                                                            <i class="feather-zap me-1"></i> Solusi
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showAiDetail('{{ $campus['uuid'] }}')" title="Lihat Profil AI Lengkap">
                                                            <i class="feather-cpu me-1"></i> Detail
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="feather-inbox fs-24 d-block mb-2"></i>
                                                    Tidak ada data perguruan tinggi yang sesuai filter pencarian.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pager / Pagination Controls -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top">
                                <div class="fs-13 text-muted mb-2 mb-md-0">
                                    Menampilkan <strong>{{ $campuses->firstItem() ?? 0 }}</strong> sampai <strong>{{ $campuses->lastItem() ?? 0 }}</strong> dari <strong>{{ $campuses->total() }}</strong> Perguruan Tinggi (Total dianalisis: {{ $allCampusesCount }} PT)
                                </div>
                                <div class="pagination-custom">
                                    {{ $campuses->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: AI Problem Solver (Mesin Pemecah Masalah Kuat Berbasis Data Riil Database) -->
                        <div class="tab-pane fade" id="solver-content" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-5 mb-4 mb-lg-0">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="fw-bold text-dark mb-0"><i class="feather-zap text-warning me-2"></i>Konsultasi & Diagnosis Masalah</h5>
                                            <span class="badge bg-success-subtle text-success fs-10 fw-bold px-2 py-1 rounded-pill">100% Real DB</span>
                                        </div>
                                        <p class="text-muted fs-12 mb-3">Pilih perguruan tinggi untuk memuat data riil dari database SIJAKI. AI akan menganalisis histori laporan, mengekstrak akar masalah (*Root Cause*), dan menyusun rekomendasi regulasi resmi.</p>
                                        
                                        <form id="problemSolverForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fs-13 fw-semibold">Pilih Perguruan Tinggi dari Database <span class="text-danger">*</span></label>
                                                <select name="pt_uuid" id="solver_pt_uuid" class="form-select form-select-sm" onchange="onSolverPtChanged(this.value)">
                                                    <option value="">-- Pilih PT dari Database SIJAKI --</option>
                                                    @foreach($allPts as $pt)
                                                        <option value="{{ $pt->uuid }}">{{ $pt->kode_pt }} - {{ $pt->nama_pt }} ({{ $pt->status_pt }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Live Database PT Snapshot Card -->
                                            <div id="solverDbPtCard" class="p-3 mb-3 bg-white rounded-3 border d-none">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <div class="fw-bold text-dark fs-13" id="dbCardPtName">Nama PT</div>
                                                        <div class="text-muted fs-11" id="dbCardPtCode">Kode: -</div>
                                                    </div>
                                                    <span class="badge bg-primary" id="dbCardPtStatus">Status</span>
                                                </div>
                                                <div class="fs-12 text-muted mb-2" id="dbCardPtKet">Keterangan: -</div>
                                                
                                                <div class="border-top pt-2">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fs-12 fw-bold text-dark">Data Laporan di Database:</span>
                                                        <span class="badge bg-info-subtle text-info fs-11" id="dbCardReportCount">0 Laporan</span>
                                                    </div>
                                                    <div id="dbCardReportsList" class="fs-11 text-muted max-h-120 overflow-auto">
                                                        Belum ada laporan tercatat.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fs-13 fw-semibold">Deskripsi Kasus / Temuan Permasalahan</label>
                                                <textarea name="case_description" id="solver_case_description" class="form-control fs-12" rows="4" placeholder="Masukkan deskripsi masalah tambahan atau biarkan terisi otomatis dari riwayat database..."></textarea>
                                            </div>

                                            <button type="button" id="btnSolveProblem" class="btn btn-warning text-dark fw-bold w-100 py-2 shadow-sm mb-2">
                                                <i class="feather-play me-1"></i> Pecahkan Masalah dengan AI
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <div id="solverResultContainer">
                                        <!-- Placeholder when no case is solved yet -->
                                        <div class="p-5 text-center bg-light rounded-4 border" id="solverPlaceholder">
                                            <div class="kpi-icon-box bg-warning-subtle text-warning mx-auto mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                                <i class="feather-cpu"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-1">AI Problem Solver Siap Mendiagnosis</h5>
                                            <p class="text-muted fs-13 max-w-500 mx-auto mb-0">
                                                Pilih perguruan tinggi di panel sebelah kiri untuk memuat rekam jejak riil dari database, kemudian klik <strong>"Pecahkan Masalah dengan AI"</strong>.
                                            </p>
                                        </div>

                                        <!-- Solved Result Box (Hidden initially) -->
                                        <div id="solverOutput" class="d-none">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <span class="badge bg-primary-subtle text-primary fs-11 text-uppercase fw-bold mb-1" id="resCategory">KATEGORI ISU</span>
                                                    <h5 class="fw-bold text-dark mb-0" id="resPtTitle">Hasil Solusi Masalah AI</h5>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success fs-12 px-3 py-1" id="resRecoveryRate">Estimasi Pulih: 85%</span>
                                                    <div class="fs-11 text-muted mt-1" id="resDuration">Durasi: 60 Hari</div>
                                                </div>
                                            </div>

                                            <!-- Real Database Evidence Verified Banner -->
                                            <div class="p-2 px-3 bg-success-subtle text-success rounded-3 border border-success-subtle mb-3 fs-12 d-flex justify-content-between align-items-center">
                                                <span><i class="feather-check-circle me-1"></i> Sumber Data: <strong>Basis Data MySQL SIJAKI</strong> (<span id="resDbTotalReports">0</span> Laporan Terverifikasi)</span>
                                                <span class="badge bg-success text-white">Live Data Real</span>
                                            </div>

                                            <!-- Root Causes & Regulations -->
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-light rounded-3 border h-100">
                                                        <h6 class="fw-bold fs-12 text-danger mb-2"><i class="feather-alert-octagon me-1"></i>Akar Masalah (Root Cause):</h6>
                                                        <ul class="fs-12 text-muted ps-3 mb-0" id="resRootCauses"></ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-light rounded-3 border h-100">
                                                        <h6 class="fw-bold fs-12 text-primary mb-2"><i class="feather-book me-1"></i>Dasar Hukum & Regulasi:</h6>
                                                        <ul class="fs-12 text-muted ps-3 mb-0" id="resRegulations"></ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3-Phase Action Roadmap -->
                                            <div class="card border mb-3 rounded-3">
                                                <div class="card-header bg-transparent py-2">
                                                    <h6 class="fw-bold fs-13 mb-0"><i class="feather-check-square text-success me-1"></i>Roadmap Rencana Aksi Solutif 3-Fase:</h6>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="mb-2">
                                                        <span class="phase-badge bg-danger text-white" id="p1Title">Fase 1</span>
                                                        <ul class="fs-12 text-muted ps-3 mb-2" id="p1List"></ul>
                                                    </div>
                                                    <div class="mb-2">
                                                        <span class="phase-badge bg-warning text-dark" id="p2Title">Fase 2</span>
                                                        <ul class="fs-12 text-muted ps-3 mb-2" id="p2List"></ul>
                                                    </div>
                                                    <div>
                                                        <span class="phase-badge bg-success text-white" id="p3Title">Fase 3</span>
                                                        <ul class="fs-12 text-muted ps-3 mb-0" id="p3List"></ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Draft Surat Arahan Resmi LLDIKTI -->
                                            <div class="card border rounded-3">
                                                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="fw-bold fs-13 mb-0"><i class="feather-file-text text-primary me-1"></i>Draft Surat Arahan Resmi LLDIKTI (Berdasarkan Data Riil):</h6>
                                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="copyLetterDraft()">
                                                        <i class="feather-copy me-1"></i> Salin Draft Surat
                                                    </button>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="letter-draft-box" id="resLetterDraft"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: K-Means Clustering -->
                        <div class="tab-pane fade" id="cluster-content" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <h5 class="fw-bold mb-1">Distribusi Segmentasi Klaster (K-Means)</h5>
                                    <p class="text-muted fs-13 mb-3">Pengelompokan otomatis berdasarkan 4 dimensi fitur: Skor Risiko, Intensitas Laporan, Aduan/Teguran, dan Frekuensi Visitasi.</p>
                                    <div id="cluster-donut-chart" style="min-height: 320px;"></div>
                                </div>
                                <div class="col-lg-6">
                                    <h5 class="fw-bold mb-1">Karakteristik 4 Klaster Lembaga</h5>
                                    <p class="text-muted fs-13 mb-3">Penjelasan profil untuk pengambilan kebijakan Pokja.</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="p-3 rounded-3 border" style="border-left: 4px solid #10B981 !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-success mb-0">Klaster 1: Mandiri & Sehat</h6>
                                                    <span class="badge bg-success">{{ $clusters['Klaster 1: Mandiri & Sehat']['count'] ?? 0 }} PT</span>
                                                </div>
                                                <div class="fs-12 text-muted">Tingkat kepatuhan PDDikti sangat baik, minim aduan dan teguran, intervensi pembinaan bersifat apresiasi dan dorongan akreditasi unggul.</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 rounded-3 border" style="border-left: 4px solid #3B82F6 !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-primary mb-0">Klaster 2: Pengawasan Rutin</h6>
                                                    <span class="badge bg-primary">{{ $clusters['Klaster 2: Pengawasan Rutin']['count'] ?? 0 }} PT</span>
                                                </div>
                                                <div class="fs-12 text-muted">Aktivitas pembinaan dan monev berjalan dalam ritme normal semesteran, rasio dosen dan perizinan dalam pemantauan wajar.</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 rounded-3 border" style="border-left: 4px solid #F59E0B !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-warning mb-0">Klaster 3: Pengawasan Khusus & Rentan</h6>
                                                    <span class="badge bg-warning">{{ $clusters['Klaster 3: Pengawasan Khusus & Rentan']['count'] ?? 0 }} PT</span>
                                                </div>
                                                <div class="fs-12 text-muted">Memiliki catatan aduan sengketa atau teguran, membutuhkan pendampingan teknis dan visitasi lapangan terpadu.</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-3 rounded-3 border" style="border-left: 4px solid #EF4444 !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-danger mb-0">Klaster 4: Kritis & Masa Transisi</h6>
                                                    <span class="badge bg-danger">{{ $clusters['Klaster 4: Kritis & Masa Transisi']['count'] ?? 0 }} PT</span>
                                                </div>
                                                <div class="fs-12 text-muted">Status pembinaan aktif/tutup/merger, sanksi berat, pelanggaran izin fatal, memerlukan rekomendasi tingkat kementerian.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: NLP Text Mining & Isu -->
                        <div class="tab-pane fade" id="nlp-content" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <h5 class="fw-bold mb-1">Kata Kunci Utama Resume Dokumen (TF-IDF)</h5>
                                    <p class="text-muted fs-13 mb-3">Ekstraksi istilah paling signifikan dari seluruh notula & laporan kegiatan pembinaan.</p>
                                    <div class="d-flex flex-wrap p-3 rounded-3 bg-light border">
                                        @forelse($nlp['top_keywords'] as $kw)
                                            <span class="keyword-pill" title="Bobot TF-IDF: {{ $kw['score'] }}">
                                                #{{ $kw['term'] }}
                                                <span class="badge bg-primary text-white fs-10 rounded-pill">{{ $kw['score'] }}</span>
                                            </span>
                                        @empty
                                            <div class="text-muted fs-13">Belum ada korpus teks yang cukup untuk analisis TF-IDF.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-4">
                                    <h5 class="fw-bold mb-1">Distribusi Taksonomi Isu LLDIKTI</h5>
                                    <p class="text-muted fs-13 mb-3">Frekuensi kemunculan topik permasalahan utama.</p>
                                    <div id="topic-bar-chart" style="min-height: 280px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: Prescriptive Recommendations -->
                        <div class="tab-pane fade" id="rec-content" role="tabpanel">
                            <div class="mb-4">
                                <h5 class="fw-bold mb-1">Daftar Rekomendasi Tindak Lanjut Pokja LLDIKTI</h5>
                                <p class="text-muted fs-13 mb-0">Rekomendasi tindakan terotomasi (*Next-Best-Action*) berdasarkan profil risiko dan temuan NLP setiap kampus.</p>
                            </div>

                            <div class="row">
                                @forelse($recommendations as $rec)
                                    <div class="col-lg-6 mb-3">
                                        <div class="action-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">{{ $rec['nama_pt'] }}</h6>
                                                    <div class="text-muted fs-11">Kode: {{ $rec['kode_pt'] }} | {{ $rec['status_pt'] }}</div>
                                                </div>
                                                <span class="badge bg-{{ $rec['RiskScore'] >= 50 ? 'danger' : ($rec['RiskScore'] >= 25 ? 'warning' : 'success') }}">
                                                    Skor Risiko: {{ $rec['RiskScore'] }}%
                                                </span>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <span class="fs-12 text-muted"><i class="feather-tag me-1 text-primary"></i>Isu Utama: <strong>{{ $rec['DominantIssue'] }}</strong></span>
                                            </div>

                                            <div class="mb-3">
                                                <span class="fs-12 fw-semibold text-dark d-block mb-1">Rekomendasi Aksi:</span>
                                                <ul class="mb-0 ps-3 fs-12 text-muted">
                                                    @foreach($rec['RecommendedActions'] as $action)
                                                        <li>{{ $action }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="fs-11 fw-semibold text-muted align-self-center me-1">Target Pokja:</span>
                                                @foreach($rec['TargetPokja'] as $pokja)
                                                    <span class="badge bg-primary-subtle text-primary fs-11">{{ $pokja }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 text-muted">
                                        Belum ada rekomendasi aktif saat ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- TAB 6: Live Interactive Risk Simulator -->
                        <div class="tab-pane fade" id="sim-content" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <h5 class="fw-bold mb-1">Simulasi Kalkulator Risiko Lembaga (Live ML Model)</h5>
                                    <p class="text-muted fs-13 mb-3">Uji coba parameter variabel aktivitas dan teks untuk melihat bagaimana Machine Learning memprediksi skor risiko.</p>
                                    
                                    <form id="riskSimulatorForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fs-13 fw-semibold">Status Kelembagaan PT</label>
                                            <select name="status_pt" id="sim_status_pt" class="form-select">
                                                <option value="Aktif">Aktif</option>
                                                <option value="Pembinaan">Pembinaan</option>
                                                <option value="Merger">Merger</option>
                                                <option value="Berubah Bentuk">Berubah Bentuk</option>
                                                <option value="Tutup">Tutup</option>
                                                <option value="Tidak Terdata">Tidak Terdata</option>
                                            </select>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label fs-12 fw-semibold">Jumlah Visitasi</label>
                                                <input type="number" name="visitasi" id="sim_visitasi" class="form-control" value="1" min="0">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-12 fw-semibold">Jumlah Monev</label>
                                                <input type="number" name="monev" id="sim_monev" class="form-control" value="2" min="0">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-12 fw-semibold">Jumlah Aduan</label>
                                                <input type="number" name="aduan" id="sim_aduan" class="form-control" value="0" min="0">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-12 fw-semibold">Jumlah Teguran/Sanksi</label>
                                                <input type="number" name="teguran" id="sim_teguran" class="form-control" value="0" min="0">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fs-13 fw-semibold">Simulasi Teks Resume / Catatan Masalah</label>
                                            <textarea name="text" id="sim_text" class="form-control" rows="3" placeholder="Contoh: Terjadi sengketa kepengurusan yayasan dan masalah rasio dosen PDDikti..."></textarea>
                                        </div>

                                        <button type="button" id="btnRunSimulation" class="btn btn-primary w-100 py-2">
                                            <i class="feather-play me-1"></i> Jalankan Simulasi Prediksi AI
                                        </button>
                                    </form>
                                </div>

                                <div class="col-lg-6">
                                    <h5 class="fw-bold mb-1">Hasil Prediksi Model AI Real-time</h5>
                                    <p class="text-muted fs-13 mb-3">Estimasi skor kerentanan dan faktor pemicu risiko.</p>
                                    
                                    <div class="p-4 rounded-4 bg-light border text-center" id="simResultBox">
                                        <div class="mb-3">
                                            <span class="fs-12 text-muted text-uppercase fw-bold">Prediksi Skor Risiko</span>
                                            <h1 class="display-4 fw-bold text-primary mb-0" id="simRiskScore">10.0%</h1>
                                            <span class="badge bg-success-subtle text-success fs-13 px-3 py-1 mt-2" id="simRiskBadge">Rendah (Sehat)</span>
                                        </div>

                                        <div class="risk-meter-bar mb-4">
                                            <div class="h-100 bg-primary" id="simRiskBar" style="width: 10%"></div>
                                        </div>

                                        <div class="text-start">
                                            <h6 class="fw-bold fs-13 mb-2">Faktor Pemicu Risiko Terdeteksi (Explainable AI):</h6>
                                            <ul class="fs-12 text-muted mb-0 ps-3" id="simFactorsList">
                                                <li>Kondisi kepatuhan stabil, aktivitas pembinaan normal</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal AI Profile Detail -->
<div class="modal fade" id="aiProfileModal" tabindex="-1" aria-labelledby="aiProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="aiProfileModalLabel">Detail Profil Data Science & AI</h5>
                    <div class="text-muted fs-12" id="modalPtSub">Memuat data...</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalPtBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted fs-13 mt-2">Menganalisis data perguruan tinggi...</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Render Cluster Donut Chart
        const clusterStats = @json($clusters);
        const clusterLabels = Object.keys(clusterStats).map(k => clusterStats[k].label);
        const clusterSeries = Object.keys(clusterStats).map(k => clusterStats[k].count);
        const clusterColors = Object.keys(clusterStats).map(k => clusterStats[k].color);

        const donutElement = document.querySelector("#cluster-donut-chart");
        if (donutElement && typeof ApexCharts !== 'undefined') {
            const donutOptions = {
                chart: {
                    type: 'donut',
                    height: 320
                },
                series: clusterSeries.length ? clusterSeries : [1],
                labels: clusterLabels.length ? clusterLabels : ['Tidak Ada Data'],
                colors: clusterColors.length ? clusterColors : ['#10B981'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                }
            };
            const chart = new ApexCharts(donutElement, donutOptions);
            chart.render();
        }

        // Render NLP Topic Bar Chart
        const topicDist = @json($nlp['topic_distribution'] ?? []);
        const topicLabels = Object.keys(topicDist);
        const topicValues = Object.values(topicDist);

        const topicElement = document.querySelector("#topic-bar-chart");
        if (topicElement && typeof ApexCharts !== 'undefined') {
            const topicOptions = {
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Frekuensi Isu',
                    data: topicValues.length ? topicValues : [0]
                }],
                xaxis: {
                    categories: topicLabels.length ? topicLabels : ['Tidak ada isu'],
                    labels: { style: { fontSize: '11px' } }
                },
                colors: ['#2B79B4'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: true
                    }
                }
            };
            const topicChart = new ApexCharts(topicElement, topicOptions);
            topicChart.render();
        }

        // Live Risk Simulation AJAX
        const btnSim = document.getElementById('btnRunSimulation');
        if (btnSim) {
            btnSim.addEventListener('click', function () {
                const formData = new FormData(document.getElementById('riskSimulatorForm'));
                
                fetch("{{ route('data-science.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const sim = data.simulation;
                        document.getElementById('simRiskScore').innerText = sim.RiskScore + '%';
                        
                        const badge = document.getElementById('simRiskBadge');
                        badge.innerText = sim.RiskLevel;
                        badge.className = `badge bg-${sim.RiskBadge}-subtle text-${sim.RiskBadge} fs-13 px-3 py-1 mt-2`;
                        
                        const bar = document.getElementById('simRiskBar');
                        bar.style.width = sim.RiskScore + '%';
                        bar.className = `h-100 bg-${sim.RiskBadge}`;

                        const factorsList = document.getElementById('simFactorsList');
                        factorsList.innerHTML = '';
                        sim.RiskFactors.forEach(f => {
                            const li = document.createElement('li');
                            li.innerText = f;
                            factorsList.appendChild(li);
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal menjalankan simulasi risiko.');
                });
            });
        }

        // AI Problem Solver Engine AJAX
        const btnSolve = document.getElementById('btnSolveProblem');
        if (btnSolve) {
            btnSolve.addEventListener('click', function () {
                const ptUuid = document.getElementById('solver_pt_uuid').value;
                let caseDesc = document.getElementById('solver_case_description').value.trim();

                if (!ptUuid && !caseDesc) {
                    alert('Silakan pilih Perguruan Tinggi dari database atau masukkan deskripsi masalah.');
                    return;
                }

                if (!caseDesc && ptUuid) {
                    caseDesc = 'Analisis kepatuhan dan histori laporan database kelembagaan.';
                }

                btnSolve.disabled = true;
                btnSolve.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mendiagnosis Data Riil Database...';

                fetch("{{ route('data-science.solve') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        case_description: caseDesc,
                        pt_uuid: ptUuid
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btnSolve.disabled = false;
                    btnSolve.innerHTML = '<i class="feather-play me-1"></i> Pecahkan Masalah dengan AI';

                    if (data.status === 'success') {
                        document.getElementById('solverPlaceholder').classList.add('d-none');
                        document.getElementById('solverOutput').classList.remove('d-none');

                        document.getElementById('resCategory').innerText = data.diagnosis.dominant_category;
                        document.getElementById('resPtTitle').innerText = data.pt_info.nama_pt + ' (' + data.pt_info.kode_pt + ')';
                        document.getElementById('resDbTotalReports').innerText = data.pt_info.total_db_reports;
                        document.getElementById('resRecoveryRate').innerText = 'Peluang Pemulihan: ' + data.recovery_estimation.probability_rate + '%';
                        document.getElementById('resDuration').innerText = 'Estimasi Waktu: ' + data.recovery_estimation.duration;

                        // Root Causes
                        const rcList = document.getElementById('resRootCauses');
                        rcList.innerHTML = '';
                        data.diagnosis.root_causes.forEach(rc => {
                            rcList.innerHTML += `<li>${rc}</li>`;
                        });

                        // Regulations
                        const regList = document.getElementById('resRegulations');
                        regList.innerHTML = '';
                        data.diagnosis.regulations.forEach(reg => {
                            regList.innerHTML += `<li>${reg}</li>`;
                        });

                        // 3-Phase Roadmap
                        document.getElementById('p1Title').innerText = data.action_roadmap.phase_1.title;
                        const p1List = document.getElementById('p1List');
                        p1List.innerHTML = '';
                        data.action_roadmap.phase_1.tasks.forEach(t => p1List.innerHTML += `<li>${t}</li>`);

                        document.getElementById('p2Title').innerText = data.action_roadmap.phase_2.title;
                        const p2List = document.getElementById('p2List');
                        p2List.innerHTML = '';
                        data.action_roadmap.phase_2.tasks.forEach(t => p2List.innerHTML += `<li>${t}</li>`);

                        document.getElementById('p3Title').innerText = data.action_roadmap.phase_3.title;
                        const p3List = document.getElementById('p3List');
                        p3List.innerHTML = '';
                        data.action_roadmap.phase_3.tasks.forEach(t => p3List.innerHTML += `<li>${t}</li>`);

                        // Official Letter Draft
                        document.getElementById('resLetterDraft').innerText = data.official_letter_draft;
                    }
                })
                .catch(err => {
                    btnSolve.disabled = false;
                    btnSolve.innerHTML = '<i class="feather-play me-1"></i> Pecahkan Masalah dengan AI';
                    console.error(err);
                    alert('Gagal memproses solusi masalah AI.');
                });
            });
        }
    });

    // Handle dropdown PT changed in Problem Solver
    function onSolverPtChanged(uuid) {
        const card = document.getElementById('solverDbPtCard');
        const descField = document.getElementById('solver_case_description');
        
        if (!uuid) {
            card.classList.add('d-none');
            return;
        }

        fetch(`/data-science/pt-summary/${uuid}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    card.classList.remove('d-none');
                    document.getElementById('dbCardPtName').innerText = data.pt.nama_pt;
                    document.getElementById('dbCardPtCode').innerText = `Kode: ${data.pt.kode_pt} | Jenis: ${data.pt.jenis_pt}`;
                    document.getElementById('dbCardPtStatus').innerText = data.pt.status_pt;
                    document.getElementById('dbCardPtKet').innerText = `Keterangan: ${data.pt.keterangan}`;
                    document.getElementById('dbCardReportCount').innerText = `${data.total_reports} Laporan Riil Database`;

                    const list = document.getElementById('dbCardReportsList');
                    list.innerHTML = '';
                    if (data.reports.length > 0) {
                        let textContext = `Rekam Database: Ditemukan ${data.total_reports} aktivitas pembinaan. `;
                        data.reports.forEach((r, idx) => {
                            list.innerHTML += `
                                <div class="p-1 mb-1 border-bottom">
                                    <strong>${r.tanggal_kegiatan}</strong>: <span class="badge bg-secondary">${r.jenis_kegiatan}</span>
                                    <div class="text-dark">${r.resume || '(Tidak ada ringkasan teks)'}</div>
                                </div>
                            `;
                            if (r.resume) {
                                textContext += `[${r.jenis_kegiatan} - ${r.tanggal_kegiatan}: ${r.resume}] `;
                            }
                        });
                        descField.value = textContext.trim();
                    } else {
                        list.innerHTML = '<div class="fst-italic text-muted">Belum ada riwayat laporan di database. Kampus tercatat berstatus: ' + data.pt.status_pt + '.</div>';
                        descField.value = `Evaluasi kelembagaan untuk ${data.pt.nama_pt} (Status database: ${data.pt.status_pt}). Belum ada catatan aduan/teguran di database.`;
                    }
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    // Helper functions
    function switchToProblemSolverTab() {
        const solverTab = new bootstrap.Tab(document.getElementById('solver-tab'));
        solverTab.show();
    }

    function triggerSolverForPt(uuid, ptName) {
        switchToProblemSolverTab();
        const select = document.getElementById('solver_pt_uuid');
        select.value = uuid;
        onSolverPtChanged(uuid);
        
        setTimeout(() => {
            document.getElementById('btnSolveProblem').click();
        }, 300);
    }

    function copyLetterDraft() {
        const text = document.getElementById('resLetterDraft').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Draft Surat Arahan Resmi LLDIKTI berhasil disalin ke clipboard!');
        }).catch(err => {
            console.error(err);
        });
    }

    // Function to load AI Profile Detail Modal
    function showAiDetail(uuid) {
        const modal = new bootstrap.Modal(document.getElementById('aiProfileModal'));
        const modalBody = document.getElementById('modalPtBody');
        const modalSub = document.getElementById('modalPtSub');
        
        modalSub.innerText = 'Memuat analisis...';
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="text-muted fs-13 mt-2">Menganalisis data perguruan tinggi dari database...</div>
            </div>
        `;
        modal.show();

        fetch(`/data-science/pt/${uuid}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const pt = data.pt;
                    const risk = data.risk_analysis;
                    const nlp = data.nlp_analysis;

                    modalSub.innerText = `${pt.kode_pt} | ${pt.jenis_pt} | Status Database: ${pt.status_pt}`;
                    
                    let factorsHtml = '';
                    risk.RiskFactors.forEach(f => {
                        factorsHtml += `<li>${f}</li>`;
                    });

                    modalBody.innerHTML = `
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-12 text-muted fw-bold">SKOR RISIKO AI</span>
                                        <span class="badge bg-${risk.RiskBadge}">${risk.RiskLevel}</span>
                                    </div>
                                    <h2 class="fw-bold text-${risk.RiskBadge} mb-1">${risk.RiskScore}%</h2>
                                    <div class="risk-meter-bar mb-3">
                                        <div class="h-100 bg-${risk.RiskBadge}" style="width: ${risk.RiskScore}%"></div>
                                    </div>
                                    <div class="fs-12 text-muted">Isu Dokumen Riil: <strong>${risk.DominantIssue}</strong></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <span class="fs-12 text-muted fw-bold d-block mb-2">METRIK RIIL DATABASE</span>
                                    <div class="d-flex justify-content-between fs-12 mb-1">
                                        <span>Total Laporan Database:</span>
                                        <strong class="text-dark">${risk.ActivityMetrics.Total}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between fs-12 mb-1">
                                        <span>Visitasi Lapangan:</span>
                                        <strong class="text-dark">${risk.ActivityMetrics.Visitasi}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between fs-12 mb-1">
                                        <span>Monitoring & Evaluasi:</span>
                                        <strong class="text-dark">${risk.ActivityMetrics.Monev}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between fs-12">
                                        <span>Aduan & Teguran:</span>
                                        <strong class="text-danger">${risk.ActivityMetrics.Aduan + risk.ActivityMetrics.Teguran}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold fs-13 mb-2">Faktor Risiko Terdeteksi dari Data Riil:</h6>
                            <ul class="fs-12 text-muted ps-3 mb-0">
                                ${factorsHtml}
                            </ul>
                        </div>

                        <div class="pt-2 border-top text-end">
                            <button type="button" class="btn btn-warning text-dark fw-bold btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('aiProfileModal')).hide(); triggerSolverForPt('${pt.uuid}', '${pt.nama_pt}');">
                                <i class="feather-zap me-1"></i> Pecahkan Masalah Kampus Ini dengan AI
                            </button>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = `<div class="alert alert-danger">Gagal memuat detail profil AI.</div>`;
            });
    }
</script>
@endpush
