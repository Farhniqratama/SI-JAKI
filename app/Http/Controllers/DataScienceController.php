<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataScienceService;
use App\Models\PerguruanTinggi;
use App\Models\LaporanPt;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataScienceController extends Controller
{
    protected DataScienceService $dsService;

    public function __construct(DataScienceService $dsService)
    {
        $this->dsService = $dsService;
    }

    /**
     * Tampilkan Halaman Utama Dashboard Data Science & AI Hub dengan Paginasi
     */
    public function index(Request $request)
    {
        $forceRefresh = $request->has('refresh');
        $insights = $this->dsService->getInsights($forceRefresh);

        // Filter / Search jika ada
        $search = $request->input('search');
        $filterCluster = $request->input('cluster');
        $filterRisk = $request->input('risk_level');
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 10;
        }

        $campuses = $insights['campuses'];

        if ($search) {
            $campuses = array_filter($campuses, function ($c) use ($search) {
                return stripos($c['nama_pt'], $search) !== false ||
                       stripos($c['kode_pt'], $search) !== false;
            });
        }

        if ($filterCluster) {
            $campuses = array_filter($campuses, function ($c) use ($filterCluster) {
                return ($c['ClusterName'] ?? '') === $filterCluster;
            });
        }

        if ($filterRisk) {
            $campuses = array_filter($campuses, function ($c) use ($filterRisk) {
                return ($c['RiskBadge'] ?? '') === $filterRisk;
            });
        }

        $campusesCollection = collect(array_values($campuses));
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $campusesCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedCampuses = new LengthAwarePaginator(
            $currentPageItems,
            $campusesCollection->count(),
            $perPage,
            $currentPage,
            ['path' => route('data-science.index'), 'query' => $request->query()]
        );

        $allPts = PerguruanTinggi::select('uuid', 'kode_pt', 'nama_pt', 'status_pt')->orderBy('nama_pt')->get();

        return view('data_science.index', [
            'kpi' => $insights['kpi'],
            'nlp' => $insights['nlp_insights'],
            'clusters' => $insights['cluster_summary'],
            'campuses' => $paginatedCampuses,
            'filteredCount' => $campusesCollection->count(),
            'allCampusesCount' => count($insights['campuses']),
            'recommendations' => $insights['prescriptive_recommendations'],
            'lastUpdated' => $insights['timestamp'] ?? now()->toIso8601String(),
            'currentSearch' => $search,
            'currentCluster' => $filterCluster,
            'currentRisk' => $filterRisk,
            'perPage' => $perPage,
            'allPts' => $allPts,
        ]);
    }

    /**
     * Detail Profil AI & Data Science untuk Satu PT Spesifik
     */
    public function detail(string $uuid)
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        $reports = LaporanPt::where('pt_id', $pt->id)->orderBy('tanggal_kegiatan', 'desc')->get();

        $riskAnalysis = $this->dsService->calculateRiskPrediction($pt, $reports);
        
        $combinedText = $reports->pluck('resume')->implode(' ');
        $nlpAnalysis = $this->dsService->classifyTopics($combinedText);

        return response()->json([
            'status' => 'success',
            'pt' => $pt,
            'risk_analysis' => $riskAnalysis,
            'nlp_analysis' => $nlpAnalysis,
            'total_reports' => $reports->count(),
            'recent_reports' => $reports->take(5)
        ]);
    }

    /**
     * Ringkasan Data Riil PT dari Database untuk Panel Problem Solver
     */
    public function ptSummary(string $uuid)
    {
        $summary = $this->dsService->getPtRealSummary($uuid);
        return response()->json($summary);
    }

    /**
     * AI Problem Solver Endpoint: Memecahkan Masalah Kasus Kampus
     */
    public function solve(Request $request)
    {
        $validated = $request->validate([
            'case_description' => 'required|string|min:5',
            'pt_uuid' => 'nullable|string',
        ]);

        $solution = $this->dsService->solveProblem(
            $validated['case_description'],
            $validated['pt_uuid'] ?? null
        );

        return response()->json($solution);
    }

    /**
     * Paksa Kalkulasi Ulang Pipeline Data Science
     */
    public function recalculate()
    {
        $this->dsService->getInsights(true);
        return redirect()->route('data-science.index')->with('success', 'Model Data Science & NLP berhasil dikalkulasi ulang dengan data terbaru!');
    }

    /**
     * Endpoint Simulasi Prediksi Risiko (Interactive Risk Simulator)
     */
    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'status_pt' => 'nullable|string',
            'rapat' => 'nullable|integer|min:0',
            'visitasi' => 'nullable|integer|min:0',
            'monev' => 'nullable|integer|min:0',
            'aduan' => 'nullable|integer|min:0',
            'teguran' => 'nullable|integer|min:0',
            'text' => 'nullable|string',
        ]);

        $result = $this->dsService->simulateRisk($validated);
        return response()->json([
            'status' => 'success',
            'simulation' => $result
        ]);
    }
}
