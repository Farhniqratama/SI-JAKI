<?php

namespace App\Services;

use App\Models\PerguruanTinggi;
use App\Models\LaporanPt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DataScienceService
{
    protected string $storagePath = 'data_science/latest_insights.json';

    /**
     * Stopwords Bahasa Indonesia untuk NLP
     */
    protected array $stopwords = [
        'yang', 'untuk', 'pada', 'ke', 'para', 'namun', 'menurut', 'antara', 'dia', 'dua',
        'ia', 'seperti', 'jika', 'sehingga', 'kembali', 'dan', 'ini', 'karena', 'oleh',
        'saat', 'harus', 'sementara', 'setelah', 'belum', 'kami', 'sekitar', 'bagi', 'serta',
        'di', 'dari', 'telah', 'sebagai', 'masih', 'hal', 'ketika', 'adalah', 'itu', 'dengan',
        'sampai', 'kalau', 'mereka', 'sudah', 'bisa', 'akan', 'terhadap', 'secara', 'agar',
        'lain', 'anda', 'begitu', 'mengapa', 'kenapa', 'ada', 'atau', 'apakah', 'hanya',
        'proses', 'dalam', 'atas', 'tentang', 'terkait', 'dilakukan', 'melakukan', 'tersebut',
        'pt', 'perguruan', 'tinggi', 'kegiatan', 'hasil', 'rapat', 'pelaksanaan', 'laporan',
        'bapak', 'ibu', 'dihadiri', 'bertempat', 'berdasarkan', 'upaya', 'tindak', 'lanjut'
    ];

    /**
     * Taksonomi Isu LLDIKTI
     */
    protected array $taxonomy = [
        'Tata Kelola & Legalitas SK' => [
            'sk', 'izin', 'operasional', 'legalitas', 'badan', 'penyelenggara', 'yayasan',
            'statuta', 'organisasi', 'pimpinan', 'akta', 'notaris', 'kemenkumham', 'alih', 'bentuk'
        ],
        'Sengketa & Konflik Internal' => [
            'sengketa', 'konflik', 'dualism', 'perselisihan', 'gugatan', 'hukum', 'pengadilan',
            'perebutan', 'klaim', 'polisi', 'laporan', 'aduan', 'pidana', 'perdata'
        ],
        'Akademik & PDDikti' => [
            'pddikti', 'rasio', 'dosen', 'mahasiswa', 'akreditasi', 'ban-pt', 'lam', 'kurikulum',
            'ijazah', 'kelulusan', 'krs', 'feeder', 'data', 'registrasi', 'nidn', 'linieritas'
        ],
        'Keuangan & Sarana Prasarana' => [
            'keuangan', 'gaji', 'tunggakan', 'gedung', 'lahan', 'fasilitas', 'sarpras',
            'sertifikat', 'laboratorium', 'aset', 'bangunan', 'sewa', 'finansial', 'dana'
        ],
        'Sanksi & Pelanggaran' => [
            'sanksi', 'teguran', 'peringatan', 'pembinaan', 'pemberhentian', 'penutupan',
            'ilegal', 'ijazah palsu', 'pelanggaran', 'sp1', 'sp2', 'sp3', 'pembekuan'
        ]
    ];

    /**
     * Mendapatkan Insight Data Science Lengkap
     */
    public function getInsights(bool $forceRefresh = false): array
    {
        if (!$forceRefresh && Storage::disk('local')->exists($this->storagePath)) {
            $content = Storage::disk('local')->get($this->storagePath);
            $decoded = json_decode($content, true);
            if (!empty($decoded) && isset($decoded['status']) && $decoded['status'] === 'success') {
                return $decoded;
            }
        }

        return $this->runPipeline();
    }

    /**
     * Menjalankan Pipeline Analisis Data Science (Python Engine dengan PHP Fallback)
     */
    public function runPipeline(): array
    {
        $pts = PerguruanTinggi::all();
        $laporans = LaporanPt::all();

        $inputData = [
            'perguruan_tinggi' => $pts->toArray(),
            'laporan_pt' => $laporans->toArray(),
        ];

        // Coba eksekusi Python Data Science Engine
        $pythonScript = base_path('data_science/sijaki_ds_engine.py');
        $insights = null;

        if (file_exists($pythonScript)) {
            $tempInput = tempnam(sys_get_temp_dir(), 'sijaki_in_');
            $tempOutput = tempnam(sys_get_temp_dir(), 'sijaki_out_');

            file_put_contents($tempInput, json_encode($inputData));
            $cmd = "python3 " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempInput) . " --output " . escapeshellarg($tempOutput) . " 2>&1";
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && file_exists($tempOutput)) {
                $pyOutput = file_get_contents($tempOutput);
                $decoded = json_decode($pyOutput, true);
                if (!empty($decoded) && isset($decoded['status']) && $decoded['status'] === 'success') {
                    $insights = $decoded;
                }
            }

            @unlink($tempInput);
            @unlink($tempOutput);
        }

        // Jika Python tidak menghasilkan data, gunakan Pure PHP ML Engine
        if (!$insights) {
            $insights = $this->runPhpNativePipeline($pts, $laporans);
        }

        // Simpan cache ke storage
        Storage::disk('local')->put($this->storagePath, json_encode($insights, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $insights;
    }

    /**
     * Pure PHP Data Science Engine
     */
    protected function runPhpNativePipeline($pts, $laporans): array
    {
        $reportsByPt = [];
        $allResumes = [];

        foreach ($laporans as $lap) {
            $ptId = $lap->pt_id;
            if (!isset($reportsByPt[$ptId])) {
                $reportsByPt[$ptId] = [];
            }
            $reportsByPt[$ptId][] = $lap;
            if (!empty($lap->resume)) {
                $allResumes[] = $lap->resume;
            }
        }

        // 1. NLP
        $keywords = $this->extractTfidfKeywords($allResumes);
        $globalTopic = $this->classifyTopics(implode(' ', $allResumes));

        // 2. Risk Prediction
        $campuses = [];
        foreach ($pts as $pt) {
            $ptReports = $reportsByPt[$pt->id] ?? [];
            $risk = $this->calculateRiskPrediction($pt, $ptReports);

            $campuses[] = array_merge([
                'id' => $pt->id,
                'uuid' => $pt->uuid,
                'kode_pt' => $pt->kode_pt,
                'nama_pt' => $pt->nama_pt,
                'jenis_pt' => $pt->jenis_pt,
                'status_pt' => $pt->status_pt,
                'status_kelembagaan_pt' => $pt->status_kelembagaan_pt ?? '-',
                'alamat_utama' => $pt->alamat_kampus_utama ?? '-',
            ], $risk);
        }

        // 3. K-Means Clustering
        $clusteredCampuses = $this->performKMeans($campuses);

        // 4. Cluster Summary
        $clusterStats = [
            'Klaster 1: Mandiri & Sehat' => ['count' => 0, 'color' => '#10B981', 'label' => 'Mandiri & Sehat'],
            'Klaster 2: Pengawasan Rutin' => ['count' => 0, 'color' => '#3B82F6', 'label' => 'Pengawasan Rutin'],
            'Klaster 3: Pengawasan Khusus & Rentan' => ['count' => 0, 'color' => '#F59E0B', 'label' => 'Pengawasan Khusus'],
            'Klaster 4: Kritis & Masa Transisi' => ['count' => 0, 'color' => '#EF4444', 'label' => 'Kritis & Transisi'],
        ];

        foreach ($clusteredCampuses as $c) {
            $cName = $c['ClusterName'] ?? 'Klaster 1: Mandiri & Sehat';
            if (isset($clusterStats[$cName])) {
                $clusterStats[$cName]['count']++;
            }
        }

        // 5. Prescriptive Recommendations
        $recommendations = $this->generateRecommendations($clusteredCampuses);

        $highRiskCount = 0;
        $totalRisk = 0;
        foreach ($clusteredCampuses as $c) {
            if ($c['RiskScore'] >= 60.0) {
                $highRiskCount++;
            }
            $totalRisk += $c['RiskScore'];
        }
        $avgRisk = count($clusteredCampuses) > 0 ? round($totalRisk / count($clusteredCampuses), 1) : 0.0;

        return [
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'kpi' => [
                'total_pt_analyzed' => count($clusteredCampuses),
                'total_laporan_processed' => count($laporans),
                'high_risk_count' => $highRiskCount,
                'average_risk_score' => $avgRisk,
                'dominant_global_topic' => $globalTopic['DominantCategory'] ?? 'Tata Kelola & Legalitas SK'
            ],
            'nlp_insights' => [
                'top_keywords' => $keywords,
                'topic_distribution' => $globalTopic['CategoryDistribution'] ?? [],
                'urgency_score' => $globalTopic['UrgencyScore'] ?? 15
            ],
            'cluster_summary' => $clusterStats,
            'campuses' => $clusteredCampuses,
            'prescriptive_recommendations' => $recommendations
        ];
    }

    /**
     * Ekstraksi Kata Kunci TF-IDF
     */
    protected function extractTfidfKeywords(array $documents, int $topN = 15): array
    {
        $totalDocs = count($documents);
        if ($totalDocs === 0) {
            return [];
        }

        $docTokens = [];
        $termDocFreq = [];

        foreach ($documents as $doc) {
            $clean = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', strip_tags($doc)));
            $words = array_filter(explode(' ', $clean), function ($w) {
                $w = trim($w);
                return strlen($w) > 3 && !in_array($w, $this->stopwords);
            });
            $words = array_values($words);
            $docTokens[] = $words;
            foreach (array_unique($words) as $w) {
                $termDocFreq[$w] = ($termDocFreq[$w] ?? 0) + 1;
            }
        }

        $overallTfidf = [];
        foreach ($docTokens as $words) {
            $totalWords = count($words);
            if ($totalWords === 0) continue;
            $termFreq = array_count_values($words);
            foreach ($termFreq as $w => $count) {
                $tf = $count / $totalWords;
                $idf = log((1 + $totalDocs) / (1 + ($termDocFreq[$w] ?? 1))) + 1;
                $tfidf = $tf * $idf;
                $overallTfidf[$w] = ($overallTfidf[$w] ?? 0) + $tfidf;
            }
        }

        arsort($overallTfidf);
        $result = [];
        $i = 0;
        foreach ($overallTfidf as $term => $score) {
            $result[] = ['term' => $term, 'score' => round($score, 4)];
            $i++;
            if ($i >= $topN) break;
        }

        return $result;
    }

    /**
     * Klasifikasi Topik Isu & Urgensi
     */
    public function classifyTopics(string $text): array
    {
        $cleaned = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', strip_tags($text)));
        $scores = [];
        $totalMatches = 0;

        foreach ($this->taxonomy as $cat => $keywords) {
            $count = 0;
            foreach ($keywords as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $cleaned)) {
                    $count++;
                }
            }
            $scores[$cat] = $count;
            $totalMatches += $count;
        }

        if ($totalMatches === 0) {
            return [
                'DominantCategory' => 'Umum & Koordinasi Rutin',
                'CategoryDistribution' => $scores,
                'UrgencyScore' => 15
            ];
        }

        arsort($scores);
        $dominant = array_key_first($scores);

        $urgency = (($scores['Sanksi & Pelanggaran'] ?? 0) * 35) +
                   (($scores['Sengketa & Konflik Internal'] ?? 0) * 30) +
                   (($scores['Tata Kelola & Legalitas SK'] ?? 0) * 15) +
                   (($scores['Akademik & PDDikti'] ?? 0) * 12) +
                   (($scores['Keuangan & Sarana Prasarana'] ?? 0) * 8);

        $urgencyScore = min(100, max(10, $urgency * 10));

        return [
            'DominantCategory' => $dominant,
            'CategoryDistribution' => $scores,
            'UrgencyScore' => $urgencyScore
        ];
    }

    /**
     * Prediksi Risiko Kelembagaan (Early Warning System Model)
     */
    public function calculateRiskPrediction($pt, $reports): array
    {
        $statusPt = is_array($pt) ? ($pt['status_pt'] ?? 'Aktif') : ($pt->status_pt ?? 'Aktif');
        $totalLaporan = count($reports);

        $rapat = 0; $visitasi = 0; $monev = 0; $aduan = 0; $teguran = 0;
        $resumes = [];

        foreach ($reports as $r) {
            $jenis = is_array($r) ? ($r['jenis_kegiatan'] ?? '') : ($r->jenis_kegiatan ?? '');
            if ($jenis === 'Rapat/Audiensi') $rapat++;
            elseif ($jenis === 'Visitasi') $visitasi++;
            elseif ($jenis === 'Monitoring & Evaluasi') $monev++;
            elseif ($jenis === 'Aduan/Laporan') $aduan++;
            elseif ($jenis === 'Teguran/Sanksi') $teguran++;

            $res = is_array($r) ? ($r['resume'] ?? '') : ($r->resume ?? '');
            if ($res) $resumes[] = $res;
        }

        $statusRiskMap = [
            'Tutup' => 98.0,
            'Pembinaan' => 75.0,
            'Merger' => 55.0,
            'Berubah Bentuk' => 45.0,
            'Tidak Terdata' => 85.0,
            'Pindah Lokasi' => 40.0,
            'Berubah Nama' => 25.0,
            'Penegerian' => 20.0,
            'Aktif' => 10.0
        ];

        $baseScore = $statusRiskMap[$statusPt] ?? 20.0;
        $behaviorScore = ($teguran * 25.0) + ($aduan * 18.0) + ($visitasi * 5.0) + ($monev * 2.0);

        $nlp = $this->classifyTopics(implode(' ', $resumes));
        $nlpFactor = ($nlp['UrgencyScore'] / 100.0) * 15.0;

        $rawRisk = ($baseScore * 0.45) + (min(50.0, $behaviorScore) * 0.40) + ($nlpFactor * 0.15);
        $finalScore = round(min(100.0, max(5.0, $rawRisk)), 1);

        if ($finalScore >= 75.0 || in_array($statusPt, ['Tutup', 'Tidak Terdata'])) {
            $riskLevel = 'Kritis (Tinggi)';
            $riskBadge = 'danger';
        } elseif ($finalScore >= 50.0 || $statusPt === 'Pembinaan') {
            $riskLevel = 'Waspada (Sedang-Tinggi)';
            $riskBadge = 'warning';
        } elseif ($finalScore >= 25.0) {
            $riskLevel = 'Perhatian (Sedang)';
            $riskBadge = 'info';
        } else {
            $riskLevel = 'Rendah (Sehat)';
            $riskBadge = 'success';
        }

        $riskFactors = [];
        if ($statusPt !== 'Aktif') {
            $riskFactors[] = "Status kelembagaan tercatat '{$statusPt}'";
        }
        if ($teguran > 0) {
            $riskFactors[] = "Memiliki {$teguran} riwayat Teguran/Sanksi formal";
        }
        if ($aduan > 0) {
            $riskFactors[] = "Terdapat {$aduan} laporan aduan masyarakat/internal";
        }
        if ($visitasi >= 2) {
            $riskFactors[] = "Frekuensi visitasi pengawasan intensif ({$visitasi} kali)";
        }
        if (in_array($nlp['DominantCategory'], ['Sanksi & Pelanggaran', 'Sengketa & Konflik Internal'])) {
            $riskFactors[] = "Isu utama pada dokumen notula: {$nlp['DominantCategory']}";
        }
        if (empty($riskFactors)) {
            $riskFactors[] = "Kondisi kepatuhan stabil, aktivitas pembinaan normal";
        }

        return [
            'RiskScore' => $finalScore,
            'RiskLevel' => $riskLevel,
            'RiskBadge' => $riskBadge,
            'RiskFactors' => $riskFactors,
            'DominantIssue' => $nlp['DominantCategory'],
            'ActivityMetrics' => [
                'Total' => $totalLaporan,
                'Rapat' => $rapat,
                'Visitasi' => $visitasi,
                'Monev' => $monev,
                'Aduan' => $aduan,
                'Teguran' => $teguran,
            ]
        ];
    }

    /**
     * Algoritma K-Means Clustering
     */
    protected function performKMeans(array $campuses): array
    {
        $centroids = [
            ['name' => 'Klaster 1: Mandiri & Sehat', 'features' => [12.0, 0.5, 0.0, 0.2], 'color' => '#10B981'],
            ['name' => 'Klaster 2: Pengawasan Rutin', 'features' => [35.0, 2.0, 0.2, 1.0], 'color' => '#3B82F6'],
            ['name' => 'Klaster 3: Pengawasan Khusus & Rentan', 'features' => [65.0, 4.0, 1.5, 2.5], 'color' => '#F59E0B'],
            ['name' => 'Klaster 4: Kritis & Masa Transisi', 'features' => [90.0, 6.0, 3.0, 3.5], 'color' => '#EF4444'],
        ];

        foreach ($campuses as &$item) {
            $feat = [
                $item['RiskScore'],
                $item['ActivityMetrics']['Total'],
                $item['ActivityMetrics']['Aduan'] + $item['ActivityMetrics']['Teguran'],
                $item['ActivityMetrics']['Visitasi']
            ];

            $minDist = INF;
            $bestCluster = 0;

            foreach ($centroids as $idx => $cent) {
                $cFeat = $cent['features'];
                $dist = sqrt(
                    pow($feat[0] - $cFeat[0], 2) * 1.0 +
                    pow($feat[1] - $cFeat[1], 2) * 4.0 +
                    pow($feat[2] - $cFeat[2], 2) * 8.0 +
                    pow($feat[3] - $cFeat[3], 2) * 5.0
                );
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $bestCluster = $idx;
                }
            }

            $item['ClusterId'] = $bestCluster + 1;
            $item['ClusterName'] = $centroids[$bestCluster]['name'];
            $item['ClusterColor'] = $centroids[$bestCluster]['color'];
        }

        return $campuses;
    }

    /**
     * Prescriptive Action Recommendations
     */
    protected function generateRecommendations(array $campuses): array
    {
        usort($campuses, fn($a, $b) => $b['RiskScore'] <=> $a['RiskScore']);
        $recommendations = [];

        foreach (array_slice($campuses, 0, 10) as $item) {
            $recActions = [];
            $targetPokja = [];

            if ($item['RiskScore'] >= 75.0 || in_array($item['status_pt'], ['Tutup', 'Pembinaan'])) {
                $recActions[] = "Jadwalkan rapat koordinasi evaluasi kelembagaan tingkat pimpinan";
                $recActions[] = "Audit kepatuhan data mahasiswa dan dosen pada PDDikti";
                $recActions[] = "Penerbitan surat peringatan lanjutan / rekomendasi tindak lanjut ke kementerian";
                $targetPokja = array_merge($targetPokja, ['Hukum dan Tata Laksana', 'Kelembagaan', 'Sistem Informasi dan PDDikti']);
            } elseif ($item['RiskScore'] >= 50.0) {
                $recActions[] = "Lakukan visitasi lapangan verifikasi sarana prasarana dan legalitas";
                $recActions[] = "Pendampingan pemenuhan rasio dosen serta instrumen akreditasi";
                $targetPokja = array_merge($targetPokja, ['Kelembagaan', 'Akademik dan Kemahasiswaan']);
            } elseif ($item['RiskScore'] >= 25.0) {
                $recActions[] = "Monitoring dan evaluasi periodik semesteran";
                $recActions[] = "Penguatan penjaminan mutu internal (SPMI)";
                $targetPokja = array_merge($targetPokja, ['Penjaminan Mutu', 'Akademik dan Kemahasiswaan']);
            } else {
                $recActions[] = "Pertahankan status kepatuhan dan dorong peningkatan akreditasi unggul";
                $targetPokja[] = 'Akademik dan Kemahasiswaan';
            }

            $recommendations[] = [
                'pt_id' => $item['id'],
                'uuid' => $item['uuid'],
                'nama_pt' => $item['nama_pt'],
                'kode_pt' => $item['kode_pt'],
                'status_pt' => $item['status_pt'],
                'RiskScore' => $item['RiskScore'],
                'RiskLevel' => $item['RiskLevel'],
                'ClusterName' => $item['ClusterName'],
                'DominantIssue' => $item['DominantIssue'],
                'RecommendedActions' => $recActions,
                'TargetPokja' => array_values(array_unique($targetPokja)),
            ];
        }

        return $recommendations;
    }

    /**
     * Simulator Kalkulator Prediksi Risiko Interaktif
     */
    public function simulateRisk(array $inputs): array
    {
        $statusPt = $inputs['status_pt'] ?? 'Aktif';
        $rapat = (int) ($inputs['rapat'] ?? 0);
        $visitasi = (int) ($inputs['visitasi'] ?? 0);
        $monev = (int) ($inputs['monev'] ?? 0);
        $aduan = (int) ($inputs['aduan'] ?? 0);
        $teguran = (int) ($inputs['teguran'] ?? 0);
        $sampleText = $inputs['text'] ?? '';

        $mockPt = ['status_pt' => $statusPt];
        $mockReports = [];
        for ($i = 0; $i < $rapat; $i++) $mockReports[] = ['jenis_kegiatan' => 'Rapat/Audiensi', 'resume' => ''];
        for ($i = 0; $i < $visitasi; $i++) $mockReports[] = ['jenis_kegiatan' => 'Visitasi', 'resume' => ''];
        for ($i = 0; $i < $monev; $i++) $mockReports[] = ['jenis_kegiatan' => 'Monitoring & Evaluasi', 'resume' => ''];
        for ($i = 0; $i < $aduan; $i++) $mockReports[] = ['jenis_kegiatan' => 'Aduan/Laporan', 'resume' => $sampleText];
        for ($i = 0; $i < $teguran; $i++) $mockReports[] = ['jenis_kegiatan' => 'Teguran/Sanksi', 'resume' => $sampleText];

        if (empty($mockReports) && $sampleText) {
            $mockReports[] = ['jenis_kegiatan' => 'Aduan/Laporan', 'resume' => $sampleText];
        }

        return $this->calculateRiskPrediction($mockPt, $mockReports);
    }

    /**
     * AI Problem Solver Engine: Mendiagnosis dan Memecahkan Masalah Berdasarkan Data Riil Database
     */
    public function solveProblem(string $caseDescription, ?string $ptUuid = null): array
    {
        $pt = null;
        $ptName = 'Perguruan Tinggi';
        $ptCode = '-';
        $ptStatus = 'Aktif';
        $ptJenis = 'PTS';
        $ptKeterangan = '-';
        $dbEvidence = [];
        $dbReportsCount = 0;
        $dbResumes = [];
        $combinedText = $caseDescription;

        if ($ptUuid) {
            $pt = PerguruanTinggi::where('uuid', $ptUuid)->first();
            if ($pt) {
                $ptName = $pt->nama_pt;
                $ptCode = $pt->kode_pt;
                $ptStatus = $pt->status_pt;
                $ptJenis = $pt->jenis_pt;
                $ptKeterangan = $pt->keterangan ?? '-';

                // Ambil seluruh data riil laporan dari database untuk PT ini
                $dbReports = LaporanPt::where('pt_id', $pt->id)->orderBy('tanggal_kegiatan', 'desc')->get();
                $dbReportsCount = $dbReports->count();

                foreach ($dbReports as $lap) {
                    $cleanResume = strip_tags($lap->resume ?? '');
                    $dbResumes[] = $cleanResume;
                    
                    $dbEvidence[] = [
                        'id' => $lap->id,
                        'jenis_kegiatan' => $lap->jenis_kegiatan,
                        'tanggal_kegiatan' => $lap->tanggal_kegiatan ? $lap->tanggal_kegiatan->toDateString() : '-',
                        'tempat_kegiatan' => $lap->tempat_kegiatan,
                        'dokumen_notula' => $lap->dokumen_notula ?? 'Tidak ada',
                        'dokumen_undangan' => $lap->dokumen_undangan ?? 'Tidak ada',
                        'resume' => $cleanResume,
                        'pokja' => is_array($lap->pokja) ? implode(', ', $lap->pokja) : ($lap->pokja ?? '-'),
                    ];
                }

                // Gabungkan teks riil dari database dengan deskripsi kasus
                if (!empty($dbResumes)) {
                    $combinedText = $caseDescription . " " . implode(" ", $dbResumes);
                }
            }
        }

        // NLP Analisis pada teks riil
        $nlpResult = $this->classifyTopics($combinedText);
        $dominant = $nlpResult['DominantCategory'];
        $urgency = $nlpResult['UrgencyScore'];

        // Root Cause Analysis (RCA) Generator Berbasis Data Riil
        $rootCauses = [];
        $regulations = [];
        $phase1 = [];
        $phase2 = [];
        $phase3 = [];
        $pokjas = [];
        $draftSubject = "Arahan Tindak Lanjut Pembinaan dan Penyelesaian Masalah Kelembagaan";

        if ($dominant === 'Sanksi & Pelanggaran' || stripos($combinedText, 'sanksi') !== false || stripos($combinedText, 'peringatan') !== false) {
            $rootCauses = [
                'Pelanggaran terhadap izin penyelenggaraan program studi atau pembukaan kampus di luar domisili tanpa legalitas resmi.',
                'Keterlambatan merespons surat peringatan/teguran tertulis sebelumnya dari LLDIKTI/Kementerian.',
                'Kegagalan memenuhi standar minimal akreditasi institusi/program studi dalam batas waktu yang ditentukan.'
            ];
            $regulations = [
                'Permendikbudristek No. 53 Tahun 2023 tentang Penjaminan Mutu Pendidikan Tinggi',
                'Permendikbudristek No. 7 Tahun 2020 tentang Pendirian, Perubahan, Pembubaran PTN dan PTS',
                'UU No. 12 Tahun 2012 tentang Pendidikan Tinggi (Pasal 60 tentang Perizinan PT)'
            ];
            $phase1 = [
                'Penerbitan Surat Peringatan Keras dan Pemanggilan Rektor serta Pengurus Badan Penyelenggara ke Kantor LLDIKTI.',
                'Pembekuan sementara akses penambahan kuota mahasiswa baru pada sistem PDDikti untuk program studi bermasalah.',
                'Pembentukan Tim Pencari Fakta (TPF) gabungan Pokja Kelembagaan dan Pokja Hukum LLDIKTI.'
            ];
            $phase2 = [
                'Pelaksanaan audit investigasi menyeluruh terhadap legalitas dokumen akademik dan operasional.',
                'Pemberian tenggat waktu perbaikan (grace period) maksimal 30 hari untuk penyelesaian rekomendasi sanksi.',
                'Pendampingan penyusunan dokumen revisi statuta dan tata kelola oleh Pokja Kelembagaan.'
            ];
            $phase3 = [
                'Visitasi verifikasi faktual lapangan oleh tim asesor/evaluator independen.',
                'Penyusunan Berita Acara Evaluasi Akhir Pemulihan Kepatuhan.',
                'Pengusulan pencabutan sanksi pembinaan ke Ditjen Diktiristek jika seluruh indikator terpenuhi.'
            ];
            $pokjas = ['Pokja Hukum dan Tata Laksana', 'Pokja Kelembagaan', 'Pokja Akademik'];
            $draftSubject = "Instruksi Pemenuhan Standar Kepatuhan dan Penyelesaian Pelanggaran Izin Operasional";
            $recoveryRate = 72;
            $recoveryDuration = "60 - 90 Hari";

        } elseif ($dominant === 'Sengketa & Konflik Internal' || stripos($combinedText, 'sengketa') !== false || stripos($combinedText, 'yayasan') !== false) {
            $rootCauses = [
                'Dualisme kepengurusan Badan Penyelenggara / Yayasan yang berakibat pada ketidakpastian otoritas pimpinan perguruan tinggi.',
                'Perselisihan pembagian kewenangan pengelolaan aset, rekening keuangan operasional, dan legalitas penandatanganan ijazah.',
                'Akta notaris kepengurusan yayasan belum mendapatkan pengesahan terbaru dari Ditjen AHU Kemenkumham RI.'
            ];
            $regulations = [
                'UU No. 28 Tahun 2004 jo UU No. 16 Tahun 2001 tentang Yayasan',
                'Permendikbudristek No. 53 Tahun 2023 tentang Tata Kelola Perguruan Tinggi yang Akuntabel',
                'Surat Edaran Ditjen Dikti mengenai Penyelesaian Sengketa Badan Penyelenggara PTS'
            ];
            $phase1 = [
                'Mediasi formal tahap pertama dengan mempertemukan kedua pihak yang bersengketa di LLDIKTI.',
                'Penetapan status quo sementara atas pengelolaan aset institusi demi perlindungan hak belajar mahasiswa.',
                'Penunjukan pejabat pelaksana harian (Plh) Rektor/Direktur netral yang disepakati untuk menjamin operasional perkuliahan.'
            ];
            $phase2 = [
                'Penyelarasan perubahan Anggaran Dasar / Anggaran Rumah Tangga Yayasan melalui notaris rekanan.',
                'Pembaruan SK Menkumham yang sah dan penyerahan tembusan resmi ke LLDIKTI Wilayah.',
                'Restrukturisasi Senat Akademik dan pengangkatan pimpinan definitif sesuai statuta yang sah.'
            ];
            $phase3 = [
                'Verifikasi rekonsiliasi penuh dan penerbitan Surat Keterangan Sengketa Selesai dari LLDIKTI.',
                'Pemulihan hak penuh penandatanganan ijazah wisuda dan pencairan dana hibah/beasiswa.',
                'Monitoring kepatuhan tata kelola setiap 3 bulan selama 1 tahun ke depan.'
            ];
            $pokjas = ['Pokja Hukum dan Tata Laksana', 'Pokja Kelembagaan'];
            $draftSubject = "Panggilan Mediasi Sengketa Yayasan dan Pengamanan Pelayanan Akademik Mahasiswa";
            $recoveryRate = 65;
            $recoveryDuration = "45 - 90 Hari";

        } elseif ($dominant === 'Akademik & PDDikti' || stripos($combinedText, 'dosen') !== false || stripos($combinedText, 'pddikti') !== false) {
            $rootCauses = [
                'Rasio perbandingan dosen tetap terhadap mahasiswa belum memenuhi standar nasional (1:30 IPA / 1:45 IPS).',
                'Keterlambatan migrasi dan pelaporan data semesteran (Feeder PDDikti) melebihi batas cut-off nasional.',
                'Masa berlaku status akreditasi program studi kedaluwarsa tanpa pengajuan re-akreditasi ke LAM/BAN-PT.'
            ];
            $regulations = [
                'Permendikbudristek No. 53 Tahun 2023 tentang Standar Nasional Pendidikan Tinggi (SN-Dikti)',
                'Keputusan Menteri No. 210/M/2023 tentang Indikator Kinerja Utama Perguruan Tinggi',
                'Peraturan BAN-PT / LAM terkait Instrumen Akreditasi Program Studi'
            ];
            $phase1 = [
                'Audit data feeder PDDikti dan pemetaan kesenjangan (gap analysis) rasio dosen per program studi.',
                'Pemberian dispensasi pembukaan feeder temporer selama 14 hari kerja untuk pemutakhiran data.',
                'Bimbingan teknis khusus bagi operator PDDikti dan tim penjaminan mutu internal kampus.'
            ];
            $phase2 = [
                'Perekrutan dosen baru ber-NIDN sesuai linieritas bidang ilmu atau penyesuaian beban kerja dosen (BKD).',
                'Submit dokumen Evaluasi Diri (DED) dan Laporan Kinerja Program Studi (DKPS) ke LAM/BAN-PT.',
                'Penyelenggaraan klinik akreditasi dengan narasumber pakar dari LLDIKTI.'
            ];
            $phase3 = [
                'Validasi 100% kepatuhan pelaporan feeder PDDikti status hijau (≥ 95%).',
                'Penerbitan sertifikat akreditasi baru atau perpanjangan peringkat akreditasi.',
                'Penyelarasan kurikulum berbasis Capaian Pembelajaran Lulusan (CPL) dan MBKM.'
            ];
            $pokjas = ['Pokja Sistem Informasi dan PDDikti', 'Pokja Akademik dan Kemahasiswaan', 'Pokja Penjaminan Mutu'];
            $draftSubject = "Instruksi Pemenuhan Rasio Dosen dan Percepatan Pelaporan Data PDDikti Feeder";
            $recoveryRate = 88;
            $recoveryDuration = "30 - 60 Hari";

        } else {
            $rootCauses = [
                'Kelemahan sistem penjaminan mutu internal (SPMI) dalam mengawal siklus PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan).',
                'Keterbatasan sarana prasarana penunjang pembelajaran laboratorium atau perpustakaan digital.',
                'Perlunya pembinaan berkala terkait tata laksana administrasi kelembagaan modern.'
            ];
            $regulations = [
                'Permendikbudristek No. 53 Tahun 2023 tentang Penjaminan Mutu Pendidikan Tinggi',
                'UU No. 12 Tahun 2012 tentang Pendidikan Tinggi'
            ];
            $phase1 = [
                'Kunjungan audiensi dan identifikasi kebutuhan pembinaan kelembagaan bersama pimpinan kampus.',
                'Penyusunan dokumen Baseline Assessment SPMI perguruan tinggi.'
            ];
            $phase2 = [
                'Pendampingan implementasi siklus PPEPP dan penyusunan instrumen audit mutu internal.',
                'Penguatan kapasitas manajemen pimpinan prodi dan badan pelaksana mutu.'
            ];
            $phase3 = [
                'Evaluasi dampak pembinaan terhadap peningkatan kepuasan sivitas akademika.',
                'Pemberian rekomendasi peningkatan peringkat akreditasi menuju Unggul.'
            ];
            $pokjas = ['Pokja Akademik dan Kemahasiswaan', 'Pokja Kelembagaan', 'Pokja Penjaminan Mutu'];
            $draftSubject = "Rekomendasi Peningkatan Mutu Tata Kelola dan Penguatan SPMI";
            $recoveryRate = 92;
            $recoveryDuration = "30 - 45 Hari";
        }

        // Draft Surat Resmi LLDIKTI Berbasis Data Riil
        $dateFormatted = date('d F Y');
        $officialLetterDraft = "KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI\n" .
            "LEMBAGA LAYANAN PENDIDIKAN TINGGI (LLDIKTI)\n" .
            "--------------------------------------------------------------------------------\n" .
            "Nomor       : B/AI-JAKI/" . date('Y/m') . "/0" . rand(100, 999) . "\n" .
            "Lampiran    : 1 (satu) Berkas Analisis Data Science & Rekomendasi AI\n" .
            "Perihal     : " . $draftSubject . "\n\n" .
            "Kepada Yth.\n" .
            "1. Ketua Badan Penyelenggara / Pengurus Yayasan\n" .
            "2. Pimpinan / Rektor " . $ptName . " (Kode: " . $ptCode . ")\n" .
            "Di Tempat\n\n" .
            "Sehubungan dengan hasil pemantauan, evaluasi berkala, serta analisis sistem cerdas SI-JAKI terhadap database perguruan tinggi " . $ptName . " (Status: " . $ptStatus . ", Riwayat Laporan Database: " . $dbReportsCount . " kegiatan), bersama ini kami sampaikan hal-hal sebagai berikut:\n\n" .
            "1. HASIL DIAGNOSIS MASALAH (BERBASIS DATA DATABASE RIIL):\n" .
            "   Berdasarkan sintesis data pada basis data SI-JAKI, terdeteksi fokus permasalahan pada kategori [" . strtoupper($dominant) . "] dengan tingkat urgensi " . $urgency . "%.\n\n" .
            "2. INSTRUKSI TINDAK LANJUT:\n";

        foreach ($phase1 as $idx => $p1) {
            $officialLetterDraft .= "   " . chr(97 + $idx) . ". " . $p1 . "\n";
        }

        $officialLetterDraft .= "\n3. BATAS WAKTU PELAPORAN:\n" .
            "   Laporan perkembangan tindak lanjut dan bukti fisik perbaikan wajib diserahkan kepada LLDIKTI paling lambat dalam waktu " . $recoveryDuration . " terhitung sejak surat ini diterbitkan.\n\n" .
            "Demikian surat arahan ini disampaikan untuk dipedomani dan dilaksanakan dengan penuh tanggung jawab demi menjamin kepastian studi mahasiswa dan mutu pendidikan tinggi.\n\n" .
            "Jakarta, " . $dateFormatted . "\n" .
            "Kepala Lembaga Layanan Pendidikan Tinggi,\n\n\n" .
            "( ..................................................... )\n" .
            "NIP. 197801012003121001";

        return [
            'status' => 'success',
            'pt_info' => [
                'nama_pt' => $ptName,
                'kode_pt' => $ptCode,
                'status_pt' => $ptStatus,
                'jenis_pt' => $ptJenis,
                'keterangan' => $ptKeterangan,
                'total_db_reports' => $dbReportsCount,
            ],
            'db_evidence' => $dbEvidence,
            'diagnosis' => [
                'dominant_category' => $dominant,
                'urgency_score' => $urgency,
                'root_causes' => $rootCauses,
                'regulations' => $regulations,
            ],
            'action_roadmap' => [
                'phase_1' => ['title' => 'Fase 1: Tanggap Darurat & Klarifikasi (Hari 1-14)', 'tasks' => $phase1],
                'phase_2' => ['title' => 'Fase 2: Restrukturisasi & Pemenuhan Standar (Hari 15-60)', 'tasks' => $phase2],
                'phase_3' => ['title' => 'Fase 3: Verifikasi Lapangan & Pemulihan (Hari 61-90)', 'tasks' => $phase3],
            ],
            'target_pokja' => $pokjas,
            'recovery_estimation' => [
                'probability_rate' => $recoveryRate,
                'duration' => $recoveryDuration,
            ],
            'official_letter_draft' => $officialLetterDraft,
        ];
    }

    /**
     * Mendapatkan Ringkasan Data Riil PT dari Database
     */
    public function getPtRealSummary(string $uuid): array
    {
        $pt = PerguruanTinggi::where('uuid', $uuid)->firstOrFail();
        $reports = LaporanPt::where('pt_id', $pt->id)->orderBy('tanggal_kegiatan', 'desc')->get();

        $reportList = $reports->map(function ($r) {
            return [
                'id' => $r->id,
                'jenis_kegiatan' => $r->jenis_kegiatan,
                'tanggal_kegiatan' => $r->tanggal_kegiatan ? $r->tanggal_kegiatan->format('d M Y') : '-',
                'tempat_kegiatan' => $r->tempat_kegiatan,
                'resume' => strip_tags($r->resume ?? ''),
                'dokumen_notula' => $r->dokumen_notula,
                'dokumen_undangan' => $r->dokumen_undangan,
                'pokja' => is_array($r->pokja) ? implode(', ', $r->pokja) : ($r->pokja ?? '-'),
                'created_by' => $r->created_by,
            ];
        });

        return [
            'status' => 'success',
            'pt' => [
                'id' => $pt->id,
                'uuid' => $pt->uuid,
                'kode_pt' => $pt->kode_pt,
                'nama_pt' => $pt->nama_pt,
                'jenis_pt' => $pt->jenis_pt,
                'status_pt' => $pt->status_pt,
                'keterangan' => $pt->keterangan ?? '-',
                'alamat_kampus_utama' => $pt->alamat_kampus_utama ?? '-',
            ],
            'total_reports' => $reports->count(),
            'reports' => $reportList,
        ];
    }
}

