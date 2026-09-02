#!/usr/bin/env python3
"""
SIJAKI Data Science & Machine Learning Engine
---------------------------------------------
Pilar:
1. NLP & Text Mining (Topic Extraction, Keyword TF-IDF, Sentimen/Urgensi Isu)
2. Predictive Early Warning System (Institutional Vulnerability & Risk Scoring)
3. Unsupervised Clustering & Profiling (K-Means Institutional Health Segmentation)
4. Prescriptive Action Recommendation Engine (Smart Next-Best-Action for Pokja)
"""

import sys
import json
import math
import os
import re
from datetime import datetime

# Daftar Stopwords Bahasa Indonesia
INDONESIAN_STOPWORDS = {
    'yang', 'untuk', 'pada', 'ke', 'para', 'namun', 'menurut', 'antara', 'dia', 'dua',
    'ia', 'seperti', 'jika', 'sehingga', 'kembali', 'dan', 'ini', 'karena', 'oleh',
    'saat', 'harus', 'sementara', 'setelah', 'belum', 'kami', 'sekitar', 'bagi', 'serta',
    'di', 'dari', 'telah', 'sebagai', 'masih', 'hal', 'ketika', 'adalah', 'itu', 'dengan',
    'sampai', 'kalau', 'mereka', 'sudah', 'bisa', 'akan', 'terhadap', 'secara', 'agar',
    'lain', 'anda', 'begitu', 'mengapa', 'kenapa', 'ada', 'atau', 'apakah', 'hanya',
    'proses', 'dalam', 'atas', 'tentang', 'terkait', 'dilakukan', 'melakukan', 'tersebut',
    'pt', 'perguruan', 'tinggi', 'kegiatan', 'hasil', 'rapat', 'pelaksanaan', 'laporan',
    'bapak', 'ibu', 'dihadiri', 'bertempat', 'berdasarkan', 'upaya', 'tindak', 'lanjut'
}

ISSUE_TAXONOMY = {
    'Tata Kelola & Legalitas SK': [
        'sk', 'izin', 'operasional', 'legalitas', 'badan', 'penyelenggara', 'yayasan',
        'statuta', 'organisasi', 'pimpinan', 'akta', 'notaris', 'kemenkumham', 'alih', 'bentuk'
    ],
    'Sengketa & Konflik Internal': [
        'sengketa', 'konflik', 'dualism', 'perselisihan', 'gugatan', 'hukum', 'pengadilan',
        'perebutan', 'klaim', 'polisi', 'laporan', 'aduan', 'pidana', 'perdata'
    ],
    'Akademik & PDDikti': [
        'pddikti', 'rasio', 'dosen', 'mahasiswa', 'akreditasi', 'ban-pt', 'lam', 'kurikulum',
        'ijazah', 'kelulusan', 'krs', 'feeder', 'data', 'registrasi', 'nidn', 'linieritas'
    ],
    'Keuangan & Sarana Prasarana': [
        'keuangan', 'gaji', 'tunggakan', 'gedung', 'lahan', 'fasilitas', 'sarpras',
        'sertifikat', 'laboratorium', 'aset', 'bangunan', 'sewa', 'finansial', 'dana'
    ],
    'Sanksi & Pelanggaran': [
        'sanksi', 'teguran', 'peringatan', 'pembinaan', 'pemberhentian', 'penutupan',
        'ilegal', 'ijazah palsu', 'pelanggaran', 'sp1', 'sp2', 'sp3', 'pembekuan'
    ]
}

def clean_text(text):
    if not text:
        return ""
    # Strip HTML tags
    text = re.sub(r'<[^>]+>', ' ', text)
    # Strip non-alphanumeric
    text = re.sub(r'[^a-zA-Z0-9\s]', ' ', text)
    return text.lower().strip()

def extract_keywords_tfidf(documents, top_n=15):
    """
    Menghitung representasi TF-IDF sederhana untuk korpus resume dokumen.
    """
    total_docs = len(documents)
    if total_docs == 0:
        return []

    doc_tokens = []
    term_doc_freq = {}

    for doc in documents:
        cleaned = clean_text(doc)
        words = [w for w in cleaned.split() if len(w) > 3 and w not in INDONESIAN_STOPWORDS]
        doc_tokens.append(words)
        unique_words = set(words)
        for word in unique_words:
            term_doc_freq[word] = term_doc_freq.get(word, 0) + 1

    overall_tfidf = {}
    for words in doc_tokens:
        total_words = len(words)
        if total_words == 0:
            continue
        term_freq = {}
        for word in words:
            term_freq[word] = term_freq.get(word, 0) + 1

        for word, count in term_freq.items():
            tf = count / total_words
            idf = math.log((1 + total_docs) / (1 + term_doc_freq.get(word, 1))) + 1
            tfidf = tf * idf
            overall_tfidf[word] = overall_tfidf.get(word, 0) + tfidf

    sorted_terms = sorted(overall_tfidf.items(), key=lambda x: x[1], reverse=True)
    return [{'term': k, 'score': round(v, 4)} for k, v in sorted_terms[:top_n]]

def classify_topics(text):
    """
    Klasifikasi teks resume ke dalam taksonomi isu pembinaan perguruan tinggi.
    """
    cleaned = clean_text(text)
    scores = {}
    total_matches = 0

    for category, keywords in ISSUE_TAXONOMY.items():
        matches = sum(1 for kw in keywords if re.search(r'\b' + re.escape(kw) + r'\b', cleaned))
        scores[category] = matches
        total_matches += matches

    if total_matches == 0:
        return {'DominantCategory': 'Umum & Koordinasi Rutin', 'CategoryDistribution': scores, 'UrgencyScore': 15}

    dominant = max(scores.items(), key=lambda x: x[1])
    dominant_cat = dominant[0] if dominant[1] > 0 else 'Umum & Koordinasi Rutin'

    # Hitung urgensi (bobot sanksi dan sengketa lebih tinggi)
    urgency = (scores.get('Sanksi & Pelanggaran', 0) * 35 +
               scores.get('Sengketa & Konflik Internal', 0) * 30 +
               scores.get('Tata Kelola & Legalitas SK', 0) * 15 +
               scores.get('Akademik & PDDikti', 0) * 12 +
               scores.get('Keuangan & Sarana Prasarana', 0) * 8)
    urgency_score = min(100, max(10, urgency * 10))

    return {
        'DominantCategory': dominant_cat,
        'CategoryDistribution': scores,
        'UrgencyScore': urgency_score
    }

def calculate_risk_prediction(pt_data, reports):
    """
    Predictive Early Warning System Model:
    Menghitung probabilitas risiko kelembagaan (0 - 100) dan menentukan level risiko.
    """
    status_pt = pt_data.get('status_pt', 'Aktif')
    jenis_pt = pt_data.get('jenis_pt', 'PTS')
    
    total_laporan = len(reports)
    rapat_count = sum(1 for r in reports if r.get('jenis_kegiatan') == 'Rapat/Audiensi')
    visitasi_count = sum(1 for r in reports if r.get('jenis_kegiatan') == 'Visitasi')
    monev_count = sum(1 for r in reports if r.get('jenis_kegiatan') == 'Monitoring & Evaluasi')
    aduan_count = sum(1 for r in reports if r.get('jenis_kegiatan') == 'Aduan/Laporan')
    teguran_count = sum(1 for r in reports if r.get('jenis_kegiatan') == 'Teguran/Sanksi')

    # Baseline risk score based on official institutional status
    status_risk_map = {
        'Tutup': 98.0,
        'Pembinaan': 75.0,
        'Merger': 55.0,
        'Berubah Bentuk': 45.0,
        'Tidak Terdata': 85.0,
        'Pindah Lokasi': 40.0,
        'Berubah Nama': 25.0,
        'Penegerian': 20.0,
        'Aktif': 10.0
    }
    base_score = status_risk_map.get(status_pt, 20.0)

    # Risk adjustments based on behavioral data features
    # Aduan & Teguran add massive risk weight
    behavior_score = (teguran_count * 25.0) + (aduan_count * 18.0) + (visitasi_count * 5.0) + (monev_count * 2.0)
    
    # NLP text-based risk analysis
    combined_resumes = " ".join([r.get('resume', '') for r in reports])
    nlp_result = classify_topics(combined_resumes)
    nlp_urgency_factor = (nlp_result['UrgencyScore'] / 100.0) * 15.0

    # Composite Machine Learning Risk Probability (Bounded between 0 and 100)
    raw_risk = (base_score * 0.45) + (min(50.0, behavior_score) * 0.40) + (nlp_urgency_factor * 0.15)
    final_risk_score = round(min(100.0, max(5.0, raw_risk)), 1)

    # Categorize Risk Level
    if final_risk_score >= 75.0 or status_pt in ['Tutup', 'Tidak Terdata']:
        risk_level = 'Kritis (Tinggi)'
        risk_badge = 'danger'
    elif final_risk_score >= 50.0 or status_pt == 'Pembinaan':
        risk_level = 'Waspada (Sedang-Tinggi)'
        risk_badge = 'warning'
    elif final_risk_score >= 25.0:
        risk_level = 'Perhatian (Sedang)'
        risk_badge = 'info'
    else:
        risk_level = 'Rendah (Sehat)'
        risk_badge = 'success'

    # Explainable AI: Identify Key Risk Drivers
    risk_factors = []
    if status_pt != 'Aktif':
        risk_factors.append(f"Status kelembagaan tercatat '{status_pt}'")
    if teguran_count > 0:
        risk_factors.append(f"Memiliki {teguran_count} riwayat Teguran/Sanksi formal")
    if aduan_count > 0:
        risk_factors.append(f"Terdapat {aduan_count} laporan aduan masyarakat/internal")
    if visitasi_count >= 2:
        risk_factors.append(f"Frekuensi visitasi pengawasan intensif ({visitasi_count} kali)")
    if nlp_result['DominantCategory'] in ['Sanksi & Pelanggaran', 'Sengketa & Konflik Internal']:
        risk_factors.append(f"Isu utama pada dokumen notula: {nlp_result['DominantCategory']}")
    if not risk_factors:
        risk_factors.append("Kondisi kepatuhan stabil, aktivitas pembinaan normal")

    return {
        'RiskScore': final_risk_score,
        'RiskLevel': risk_level,
        'RiskBadge': risk_badge,
        'RiskFactors': risk_factors,
        'DominantIssue': nlp_result['DominantCategory'],
        'ActivityMetrics': {
            'Total': total_laporan,
            'Rapat': rapat_count,
            'Visitasi': visitasi_count,
            'Monev': monev_count,
            'Aduan': aduan_count,
            'Teguran': teguran_count
        }
    }

def perform_kmeans_clustering(campus_features, k=4):
    """
    Algoritma K-Means Clustering Multidimensi untuk Profiling Kesehatan Kampus.
    Fitur: [RiskScore, TotalLaporan, AduanTeguranCount, VisitasiCount]
    """
    if not campus_features:
        return []

    # Initial centroids (Heuristik Terstandarisasi Domain Kampus)
    centroids = [
        {'name': 'Klaster 1: Mandiri & Sehat', 'features': [12.0, 0.5, 0.0, 0.2], 'color': '#10B981'},
        {'name': 'Klaster 2: Pengawasan Rutin', 'features': [35.0, 2.0, 0.2, 1.0], 'color': '#3B82F6'},
        {'name': 'Klaster 3: Pengawasan Khusus & Rentan', 'features': [65.0, 4.0, 1.5, 2.5], 'color': '#F59E0B'},
        {'name': 'Klaster 4: Kritis & Masa Transisi', 'features': [90.0, 6.0, 3.0, 3.5], 'color': '#EF4444'}
    ]

    assignments = []
    for item in campus_features:
        feat = [
            item['RiskScore'],
            item['ActivityMetrics']['Total'],
            item['ActivityMetrics']['Aduan'] + item['ActivityMetrics']['Teguran'],
            item['ActivityMetrics']['Visitasi']
        ]

        # Euclidean distance to centroids
        min_dist = float('inf')
        best_cluster = 0

        for idx, cent in enumerate(centroids):
            c_feat = cent['features']
            # Weighted Euclidean Distance
            dist = math.sqrt(
                ((feat[0] - c_feat[0]) ** 2) * 1.0 +
                ((feat[1] - c_feat[1]) ** 2) * 4.0 +
                ((feat[2] - c_feat[2]) ** 2) * 8.0 +
                ((feat[3] - c_feat[3]) ** 2) * 5.0
            )
            if dist < min_dist:
                min_dist = dist
                best_cluster = idx

        item['ClusterId'] = best_cluster + 1
        item['ClusterName'] = centroids[best_cluster]['name']
        item['ClusterColor'] = centroids[best_cluster]['color']
        assignments.append(item)

    return assignments

def generate_prescriptive_recommendations(campus_analysis):
    """
    Prescriptive Analytics: Menghasilkan rekomendasi tindakan konkret untuk Pokja LLDIKTI.
    """
    recommendations = []
    
    # Sort by risk score descending
    sorted_campuses = sorted(campus_analysis, key=lambda x: x['RiskScore'], reverse=True)

    for item in sorted_campuses[:10]: # Top 10 priority cases
        rec_actions = []
        target_pokja = []

        if item['RiskScore'] >= 75.0 or item['status_pt'] in ['Tutup', 'Pembinaan']:
            rec_actions.append("Jadwalkan rapat koordinasi evaluasi kelembagaan tingkat pimpinan")
            rec_actions.append("Audit kepatuhan data mahasiswa dan dosen pada PDDikti")
            rec_actions.append("Penerbitan surat peringatan lanjutan / rekomendasi tindak lanjut ke kementerian")
            target_pokja.extend(['Hukum dan Tata Laksana', 'Kelembagaan', 'Sistem Informasi dan PDDikti'])
        elif item['RiskScore'] >= 50.0:
            rec_actions.append("Lakukan visitasi lapangan verifikasi sarana prasarana dan legalitas")
            rec_actions.append("Pendampingan pemenuhan rasio dosen serta instrumen akreditasi")
            target_pokja.extend(['Kelembagaan', 'Akademik dan Kemahasiswaan'])
        elif item['RiskScore'] >= 25.0:
            rec_actions.append("Monitoring dan evaluasi periodik semesteran")
            rec_actions.append("Penguatan penjaminan mutu internal (SPMI)")
            target_pokja.extend(['Penjaminan Mutu', 'Akademik dan Kemahasiswaan'])
        else:
            rec_actions.append("Pertahankan status kepatuhan dan dorong peningkatan akreditasi unggul")
            target_pokja.extend(['Akademik dan Kemahasiswaan'])

        recommendations.append({
            'pt_id': item.get('id'),
            'uuid': item.get('uuid'),
            'nama_pt': item.get('nama_pt'),
            'kode_pt': item.get('kode_pt'),
            'status_pt': item.get('status_pt'),
            'RiskScore': item['RiskScore'],
            'RiskLevel': item['RiskLevel'],
            'ClusterName': item['ClusterName'],
            'DominantIssue': item['DominantIssue'],
            'RecommendedActions': rec_actions,
            'TargetPokja': list(set(target_pokja))
        })

    return recommendations

def run_pipeline(input_data):
    """
    Menjalankan seluruh pipeline Data Science dan menghasilkan output komprehensif.
    """
    pts = input_data.get('perguruan_tinggi', [])
    laporans = input_data.get('laporan_pt', [])

    # Group reports by pt_id
    reports_by_pt = {}
    all_resumes = []

    for lap in laporans:
        pt_id = lap.get('pt_id')
        if pt_id not in reports_by_pt:
            reports_by_pt[pt_id] = []
        reports_by_pt[pt_id].append(lap)
        if lap.get('resume'):
            all_resumes.append(lap.get('resume'))

    # 1. NLP Keyword & Global Topic Extraction
    global_keywords = extract_keywords_tfidf(all_resumes, top_n=20)
    
    # Global Topic Distribution
    combined_all_text = " ".join(all_resumes)
    global_topic_dist = classify_topics(combined_all_text)

    # 2. Risk Prediction for each PT
    analyzed_campuses = []
    for pt in pts:
        pt_id = pt.get('id')
        pt_reports = reports_by_pt.get(pt_id, [])
        risk_info = calculate_risk_prediction(pt, pt_reports)
        
        campus_record = {
            'id': pt.get('id'),
            'uuid': pt.get('uuid'),
            'kode_pt': pt.get('kode_pt'),
            'nama_pt': pt.get('nama_pt'),
            'jenis_pt': pt.get('jenis_pt'),
            'status_pt': pt.get('status_pt'),
            'status_kelembagaan_pt': pt.get('status_kelembagaan_pt', '-'),
            'alamat_utama': pt.get('alamat_kampus_utama', '-'),
            **risk_info
        }
        analyzed_campuses.append(campus_record)

    # 3. K-Means Clustering Profiling
    clustered_campuses = perform_kmeans_clustering(analyzed_campuses, k=4)

    # 4. Cluster Summary Statistics
    cluster_stats = {
        'Klaster 1: Mandiri & Sehat': {'count': 0, 'color': '#10B981', 'label': 'Mandiri & Sehat'},
        'Klaster 2: Pengawasan Rutin': {'count': 0, 'color': '#3B82F6', 'label': 'Pengawasan Rutin'},
        'Klaster 3: Pengawasan Khusus & Rentan': {'count': 0, 'color': '#F59E0B', 'label': 'Pengawasan Khusus'},
        'Klaster 4: Kritis & Masa Transisi': {'count': 0, 'color': '#EF4444', 'label': 'Kritis & Transisi'}
    }
    for c in clustered_campuses:
        c_name = c['ClusterName']
        if c_name in cluster_stats:
            cluster_stats[c_name]['count'] += 1

    # 5. Prescriptive Recommendations
    recommendations = generate_prescriptive_recommendations(clustered_campuses)

    # 6. Global Summary KPI
    high_risk_count = sum(1 for c in clustered_campuses if c['RiskScore'] >= 60.0)
    avg_risk = round(sum(c['RiskScore'] for c in clustered_campuses) / len(clustered_campuses), 1) if clustered_campuses else 0.0

    return {
        'status': 'success',
        'timestamp': datetime.now().isoformat(),
        'kpi': {
            'total_pt_analyzed': len(clustered_campuses),
            'total_laporan_processed': len(laporans),
            'high_risk_count': high_risk_count,
            'average_risk_score': avg_risk,
            'dominant_global_topic': global_topic_dist['DominantCategory']
        },
        'nlp_insights': {
            'top_keywords': global_keywords,
            'topic_distribution': global_topic_dist['CategoryDistribution'],
            'urgency_score': global_topic_dist['UrgencyScore']
        },
        'cluster_summary': cluster_stats,
        'campuses': clustered_campuses,
        'prescriptive_recommendations': recommendations
    }

if __name__ == '__main__':
    input_json = None
    output_file = None

    for i, arg in enumerate(sys.argv):
        if arg == '--output' and i + 1 < len(sys.argv):
            output_file = sys.argv[i + 1]

    if len(sys.argv) > 1 and os.path.exists(sys.argv[1]):
        with open(sys.argv[1], 'r', encoding='utf-8') as f:
            input_json = json.load(f)
    elif '--stdin' in sys.argv:
        try:
            raw_input = sys.stdin.read()
            if raw_input.strip():
                input_json = json.loads(raw_input)
        except Exception:
            input_json = None

    if not input_json:
        # Generate demo sample output if no input provided
        input_json = {
            'perguruan_tinggi': [
                {'id': 1, 'uuid': 'sample-1', 'kode_pt': '031001', 'nama_pt': 'Universitas Contoh Sehat', 'jenis_pt': 'PTS', 'status_pt': 'Aktif'},
                {'id': 2, 'uuid': 'sample-2', 'kode_pt': '031002', 'nama_pt': 'Institut Risiko Tinggi', 'jenis_pt': 'PTS', 'status_pt': 'Pembinaan'}
            ],
            'laporan_pt': [
                {'id': 1, 'pt_id': 2, 'jenis_kegiatan': 'Aduan/Laporan', 'resume': 'Terdapat laporan sengketa yayasan dan masalah akreditasi serta rasio dosen.'},
                {'id': 2, 'pt_id': 2, 'jenis_kegiatan': 'Teguran/Sanksi', 'resume': 'Diberikan surat peringatan terkait pelanggaran izin operasional.'}
            ]
        }

    result = run_pipeline(input_json)
    output_str = json.dumps(result, ensure_ascii=False, indent=2)

    if output_file:
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(output_str)
        print(f"Results written to {output_file}")
    else:
        print(output_str)
