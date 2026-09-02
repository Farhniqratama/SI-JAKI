#!/usr/bin/env python3
"""
Script Generator PDF Laporan & Portofolio Komprehensif:
Keterkaitan Aplikasi SI-JAKI dengan Bidang Data Science & Panduan Portofolio Profesional
"""

import os
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_JUSTIFY, TA_RIGHT
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        canvas.Canvas.__init__(self, *args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        
        # Header (pages > 1)
        if self._pageNumber > 1:
            self.drawString(36, 810, "SI-JAKI: Laporan Analisis Data Science & Portofolio Profesional")
            self.drawRightString(559, 810, "LLDIKTI / Sistem Cerdas")
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.5)
            self.line(36, 804, 559, 804)

        # Footer
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.5)
        self.line(36, 42, 559, 42)
        
        self.drawString(36, 30, "Dokumen Resmi Evaluasi Arsitektur & Portofolio Data Science | SI-JAKI 2026")
        self.drawRightString(559, 30, f"Halaman {self._pageNumber} dari {page_count}")
        self.restoreState()

def build_pdf(output_path):
    doc = SimpleDocTemplate(
        output_path,
        pagesize=A4,
        rightMargin=36,
        leftMargin=36,
        topMargin=48,
        bottomMargin=48
    )

    styles = getSampleStyleSheet()

    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=17,
        leading=21,
        textColor=colors.HexColor('#0F172A'),
        alignment=TA_CENTER,
        spaceAfter=4
    )
    
    subtitle_style = ParagraphStyle(
        'DocSubTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13,
        textColor=colors.HexColor('#2563EB'),
        alignment=TA_CENTER,
        spaceAfter=12
    )

    h1_style = ParagraphStyle(
        'H1',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=15,
        textColor=colors.HexColor('#0F172A'),
        spaceBefore=10,
        spaceAfter=5
    )

    h2_style = ParagraphStyle(
        'H2',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13,
        textColor=colors.HexColor('#1E3A8A'),
        spaceBefore=7,
        spaceAfter=3
    )

    body_style = ParagraphStyle(
        'Body',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.8,
        leading=12.5,
        textColor=colors.HexColor('#334155'),
        alignment=TA_JUSTIFY,
        spaceAfter=5
    )

    body_bold = ParagraphStyle(
        'BodyBold',
        parent=body_style,
        fontName='Helvetica-Bold'
    )

    quote_box = ParagraphStyle(
        'QuoteBox',
        parent=styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=8.5,
        leading=12,
        textColor=colors.HexColor('#1E293B'),
        spaceBefore=4,
        spaceAfter=4
    )

    table_header_style = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.white,
        alignment=TA_CENTER
    )

    table_cell_style = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=10.5,
        textColor=colors.HexColor('#1E293B')
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=table_cell_style,
        fontName='Helvetica-Bold'
    )

    table_cell_center = ParagraphStyle(
        'TableCellCenter',
        parent=table_cell_style,
        alignment=TA_CENTER
    )

    elements = []

    # Title Header Box
    elements.append(Paragraph("LAPORAN ANALISIS TEKNIS & PANDUAN PORTOFOLIO DATA SCIENCE", subtitle_style))
    elements.append(Paragraph("Keterkaitan Aplikasi SI-JAKI dengan Bidang Data Science", title_style))
    elements.append(Paragraph("Evaluasi Arsitektur, Proporsi Persentase, dan Panduan Portofolio Profesional", subtitle_style))
    elements.append(HRFlowable(width="100%", thickness=1.5, color=colors.HexColor('#2563EB'), spaceAfter=10))

    # SECTION 1: RINGKASAN & PERSENTASE
    elements.append(Paragraph("1. Ringkasan Eksekutif & Pembagian Persentase Sistem", h1_style))
    elements.append(Paragraph(
        "Aplikasi <b>SI-JAKI (Sistem Informasi Jejak Pembinaan Perguruan Tinggi)</b> adalah platform terpadu yang memadukan keahlian <b>Software Engineering (OLTP)</b>, <b>Descriptive Data Analytics (Business Intelligence)</b>, dan <b>Advanced Data Science / Machine Learning (NLP, Clustering, Predictive EWS, & Prescriptive Decision Support)</b>. Sistem ini menganalisis data riil perguruan tinggi langsung dari basis data MySQL untuk memberikan pengawasan cerdas bagi Lembaga Layanan Pendidikan Tinggi (LLDIKTI).",
        body_style
    ))

    data_proporsi = [
        [
            Paragraph("Domain / Komponen Sistem", table_header_style),
            Paragraph("Proporsi Bobot", table_header_style),
            Paragraph("Deskripsi Teknis & Implementasi pada SI-JAKI", table_header_style)
        ],
        [
            Paragraph("<b>Software Engineering & Web/Mobile (OLTP)</b>", table_cell_style),
            Paragraph("<b>70% - 75%</b>", table_cell_center),
            Paragraph("Fondasi transaksional: Framework Laravel (PHP), MySQL, Flutter (Dart), Blade, Autentikasi, Role-Based Access Control (Admin/Dev/User), manajemen berkas notula/SK, dan REST API.", table_cell_style)
        ],
        [
            Paragraph("<b>Descriptive Data Analytics & BI</b>", table_cell_style),
            Paragraph("<b>15% - 20%</b>", table_cell_center),
            Paragraph("Pelaporan & pemantauan tren historis: Agregasi SQL (COUNT, GROUP BY), visualisasi performa pokja (Bar Chart), status distribusi kampus (Donut Chart), filter paginasi dinamis, dan ekspor PDF/Excel.", table_cell_style)
        ],
        [
            Paragraph("<b>Advanced Data Science & Machine Learning</b>", table_cell_style),
            Paragraph("<b>10% - 15%</b>", table_cell_center),
            Paragraph("Mesin kecerdasan prediktif & preskriptif: NLP TF-IDF & Topic Modeling, Early Warning System (EWS) Risk Scoring, K-Means Clustering 4-D, dan AI Problem Solver Root Cause Analysis.", table_cell_style)
        ],
        [
            Paragraph("<b>Data Readiness & Asset Value (Potensi Aset)</b>", table_cell_bold),
            Paragraph("<b>100% (Sangat Tinggi)</b>", table_cell_center),
            Paragraph("Data primer kelembagaan, kronologi visitasi, teguran, sanksi, dan resume notula rapat merupakan data longitudinal berkualitas tinggi untuk riset analitik dan machine learning lanjutan.", table_cell_style)
        ]
    ]

    t_proporsi = Table(data_proporsi, colWidths=[140, 85, 298])
    t_proporsi.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#1E3A8A')),
        ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#CBD5E1')),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.HexColor('#F8FAFC'), colors.white]),
        ('TOPPADDING', (0, 0), (-1, -1), 4.5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4.5),
    ]))
    elements.append(t_proporsi)
    elements.append(Spacer(1, 8))

    # SECTION 2: 4 PILAR DATA SCIENCE
    elements.append(Paragraph("2. Analisis Rinci 4 Pilar Data Science Terapan", h1_style))

    elements.append(Paragraph("A. Natural Language Processing (NLP) & Text Mining pada Resume Dokumen", h2_style))
    elements.append(Paragraph(
        "Dokumen resume rapat, audiensi, dan catatan visitasi diolah menggunakan pembobotan <b>TF-IDF (Term Frequency-Inverse Document Frequency)</b> dengan pembersihan stopwords bahasa Indonesia. Teks diklasifikasikan secara otomatis ke dalam 5 taksonomi isu LLDIKTI: <i>(1) Tata Kelola & Legalitas SK, (2) Sengketa & Konflik Internal, (3) Akademik & PDDikti, (4) Keuangan & Sarpras, dan (5) Sanksi & Pelanggaran</i>, sekaligus menghitung Indeks Skor Urgensi Masalah (0-100%).",
        body_style
    ))

    elements.append(Paragraph("B. Early Warning System (EWS) & Predictive Risk Scoring Model", h2_style))
    elements.append(Paragraph(
        "Model prediksi risiko berbasis machine learning probabilistik mengkombinasikan status kelembagaan riil (Aktif, Pembinaan, Tutup, Merger), frekuensi kegiatan intervensi (aduan, teguran, visitasi, monev), serta bobot urgensi NLP. Menghasilkan <b>Skor Risiko Lembaga (0 - 100%)</b> dan kategorisasi tingkat risiko (<i>Sehat, Perhatian, Waspada, Kritis</i>) dengan fitur <b>Explainable AI (XAI)</b> transparan.",
        body_style
    ))

    elements.append(Paragraph("C. Unsupervised Learning: K-Means Multi-Dimensional Clustering Profiling", h2_style))
    elements.append(Paragraph(
        "Mengelompokkan seluruh perguruan tinggi ke dalam 4 kuadran profil kesehatan kelembagaan menggunakan 4 dimensi fitur (Skor Risiko, Intensitas Laporan, Rasio Aduan/Teguran, dan Frekuensi Visitasi): <b>Klaster 1: Mandiri & Sehat</b>, <b>Klaster 2: Pengawasan Rutin</b>, <b>Klaster 3: Pengawasan Khusus & Rentan</b>, dan <b>Klaster 4: Kritis & Masa Transisi</b>.",
        body_style
    ))

    elements.append(Paragraph("D. AI Problem Solver & Prescriptive Decision Support System", h2_style))
    elements.append(Paragraph(
        "Modul pemecah masalah cerdas yang mengevaluasi rekam jejak riil database dan menghasilkan: <b>(1) Root Cause Analysis (RCA)</b>, <b>(2) Rujukan Regulasi Hukum Nasional</b> (Permendikbudristek No. 53/2023, UU No. 12/2012, UU No. 28/2004), <b>(3) Roadmap Rencana Aksi Solutif 3-Fase (30-60-90 Hari)</b>, <b>(4) Generator Draf Surat Arahan Resmi LLDIKTI</b> otomatis, dan <b>(5) Estimasi Probabilitas Pemulihan Status Kampus</b>.",
        body_style
    ))

    elements.append(Spacer(1, 6))

    # SECTION 3: VALIDASI DATA RIIL
    elements.append(Paragraph("3. Karakteristik & Validasi Data Riil Database (Zero Dummy Data)", h1_style))
    elements.append(Paragraph(
        "Seluruh komputasi analitik dan model AI beroperasi secara langsung pada data aktif di basis data MySQL SI-JAKI: <b>25 data master perguruan tinggi nyata</b> (Universitas Indonesia, UNJ, Univ Pancasila, Univ Pertamina, Binus, Gunadarma, Poltek APP, Univ Tarumanagara, PKN STAN, dll) serta <b>seluruh dokumen laporan kegiatan pembinaan riil</b>. Sistem dilengkapi fitur live database inspection dan paginasi tabel dinamis.",
        body_style
    ))

    elements.append(Spacer(1, 8))

    # SECTION 4: PANDUAN PORTOFOLIO DATA SCIENCE
    elements.append(Paragraph("4. Panduan Portofolio Profesional Data Science", h1_style))
    elements.append(Paragraph(
        "Proyek SI-JAKI memiliki <b>Nilai Jual Sangat Tinggi (*High-Value Portfolio*)</b> karena merupakan proyek <b>End-to-End Full-Stack Data Science</b> yang memecahkan masalah nyata institusi pemerintah, bukan sekadar notebook analisis teoritis.",
        body_style
    ))

    # Portofolio CV Format Box
    cv_box_data = [
        [
            Paragraph("<b>Judul Proyek Portofolio:</b><br/>"
                      "<b>SI-JAKI: End-to-End AI-Driven Institutional Health Monitoring & Early Warning System</b><br/>"
                      "<i>(Sistem Kecerdasan Buatan Pemantauan Risiko & Pemecah Masalah Kelembagaan Perguruan Tinggi)</i>", table_cell_style)
        ],
        [
            Paragraph("<b>Poin Pencapaian di Resume / CV (Google XYZ Formula):</b><br/>"
                      "• <b>Natural Language Processing (NLP)</b>: Mengembangkan mesin ekstraksi kata kunci <i>TF-IDF</i> dan klasifikasi taksonomi isu bahasa Indonesia untuk menganalisis teks notula/resume rapat secara otomatis ke dalam 5 domain masalah hukum & akademik.<br/>"
                      "• <b>Predictive Risk Modeling (EWS)</b>: Merancang model <i>Early Warning System (EWS)</i> berbasis probabilitas risiko (0 - 100%) dengan menggabungkan data transaksional histori laporan dan fitur teks NLP dengan fitur <i>Explainable AI (XAI)</i>.<br/>"
                      "• <b>Unsupervised Machine Learning</b>: Menerapkan algoritma <i>K-Means Clustering 4-Dimensi</i> untuk mengelompokkan 25+ perguruan tinggi ke dalam 4 kuadran profil kesehatan kelembagaan secara objektif.<br/>"
                      "• <b>Prescriptive Analytics & Decision Support</b>: Membangun modul <i>AI Problem Solver</i> yang mampu melakukan <i>Root Cause Analysis (RCA)</i>, memetakan rujukan regulasi nasional (Permendikbudristek No. 53/2023 & UU No. 12/2012), serta men-generate draf surat dinas otomatis.<br/>"
                      "• <b>Full-Stack Deployment & Integration</b>: Mengintegrasikan seluruh pipeline analitik data ke dalam web dashboard interaktif (ApexCharts, live risk simulator) dan REST API endpoint untuk aplikasi mobile Flutter.", table_cell_style)
        ]
    ]

    t_cv = Table(cv_box_data, colWidths=[523])
    t_cv.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#F1F5F9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#3B82F6')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    elements.append(t_cv)
    elements.append(Spacer(1, 8))

    # Tech Stack Matrix Table
    data_skills = [
        [Paragraph("Kategori Portofolio", table_header_style), Paragraph("Keahlian & Teknologi (Tech Stack Keywords)", table_header_style)],
        [Paragraph("<b>Data Science & ML</b>", table_cell_style), Paragraph("Natural Language Processing (NLP), TF-IDF, Topic Modeling, K-Means Clustering, Feature Engineering, Predictive Modeling, Prescriptive Analytics, Explainable AI (XAI), Root Cause Analysis.", table_cell_style)],
        [Paragraph("<b>Programming & Data</b>", table_cell_style), Paragraph("Python, PHP, SQL (MySQL Relational Database), JSON Data Pipelines.", table_cell_style)],
        [Paragraph("<b>Deployment & UI</b>", table_cell_style), Paragraph("RESTful API Integration, Interactive Web Analytics, ApexCharts Visualization, Automated PDF Reporting (ReportLab).", table_cell_style)]
    ]
    t_skills = Table(data_skills, colWidths=[140, 383])
    t_skills.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#1E3A8A')),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#CBD5E1')),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor('#F8FAFC')]),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
    ]))
    elements.append(t_skills)
    elements.append(Spacer(1, 8))

    # SECTION 5: PANDUAN INTERVIEW (STAR METHOD)
    elements.append(Paragraph("5. Panduan Menjawab saat Wawancara Kerja (Metode STAR)", h1_style))
    
    interview_text = (
        "<b>Pertanyaan Interview:</b> <i>\"Ceritakan proyek Data Science paling menantang yang pernah Anda bangun!\"</i><br/><br/>"
        "<b>Contoh Jawaban Anda:</b><br/>"
        "• <b>Situation (Situasi):</b> LLDIKTI memerlukan sistem pemantauan komprehensif untuk mendeteksi risiko penutupan, sengketa, dan penurunan mutu perguruan tinggi swasta/negeri.<br/>"
        "• <b>Task (Tantangan):</b> Mengolah data transaksional dan ratusan dokumen teks notula rapat yang tidak terstruktur menjadi insight prediktif dan preskriptif yang dapat ditindaklanjuti pimpinan.<br/>"
        "• <b>Action (Aksi):</b> Saya mengembangkan modul Data Science terintegrasi: (1) NLP TF-IDF & Topic Taxonomy Classifier untuk ekstraksi akar masalah, (2) Model EWS berbasis probabilitas risiko dengan Explainable AI, (3) K-Means Clustering 4-D untuk segmentasi kesehatan kampus, dan (4) AI Problem Solver yang memetakan solusi ke regulasi resmi (Permendikbudristek 53/2023) serta men-generate draf surat dinas otomatis.<br/>"
        "• <b>Result (Hasil):</b> Pipeline berhasil memproses 25+ data kampus dan dokumen laporan riil dalam waktu ~130 ms, menyajikan live risk matrix interaktif, serta menyediakan REST API endpoint untuk aplikasi mobile."
    )

    t_interview = Table([[Paragraph(interview_text, table_cell_style)]], colWidths=[523])
    t_interview.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#ECFDF5')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#10B981')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    elements.append(t_interview)

    # Build Document with NumberedCanvas
    doc.build(elements, canvasmaker=NumberedCanvas)
    print(f"Comprehensive PDF successfully generated at: {output_path}")

if __name__ == '__main__':
    target = '/Applications/MAMP/htdocs/SIJAKI/Laporan_Analisis_Keterkaitan_Data_Science_SIJAKI.pdf'
    build_pdf(target)
