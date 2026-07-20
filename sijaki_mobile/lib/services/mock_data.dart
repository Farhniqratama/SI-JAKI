import '../models/pt_model.dart';
import '../models/laporan_model.dart';

class MockDataService {
  static List<PerguruanTinggi> getMockPTs() {
    return [
      PerguruanTinggi(
        uuid: 'pt-1',
        name: 'PT Polimedia Negeri Media Kreatif',
        npsn: '001010',
        type: 'PTN',
        address: 'Srengseng Sawah, Jagakarsa, Jakarta Selatan',
        status: 'Aktif',
        accreditation: 'Baik Sekali',
        lecturersCount: 340,
        studentsCount: 4500,
        logoUrl: 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=150',
        website: 'https://polimedia.ac.id',
      ),
      PerguruanTinggi(
        uuid: 'pt-2',
        name: 'PT Universitas Ibnu Chaldun',
        npsn: '031020',
        type: 'PTS',
        address: 'Jl. Pemuda I No. 9, Rawamangun, Jakarta Timur',
        status: 'Aktif',
        accreditation: 'Baik',
        lecturersCount: 150,
        studentsCount: 2200,
        logoUrl: 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=150',
        website: 'https://uic.ac.id',
      ),
      PerguruanTinggi(
        uuid: 'pt-3',
        name: 'PT Universitas Pertamina',
        npsn: '031030',
        type: 'PTS',
        address: 'Jl. Teuku Nyak Arief, Kebayoran Lama, Jakarta Selatan',
        status: 'Aktif',
        accreditation: 'Baik Sekali',
        lecturersCount: 280,
        studentsCount: 3900,
        logoUrl: 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=150',
        website: 'https://universitaspertamina.ac.id',
      ),
      PerguruanTinggi(
        uuid: 'pt-4',
        name: 'Universitas Indonesia',
        npsn: '001001',
        type: 'PTN',
        address: 'Jl. Margonda Raya, Depok, Jawa Barat',
        status: 'Aktif',
        accreditation: 'Unggul',
        lecturersCount: 1540,
        studentsCount: 28400,
        logoUrl: 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=150',
        website: 'https://ui.ac.id',
      ),
    ];
  }

  static List<Laporan> getMockLaporans() {
    return [
      Laporan(
        uuid: 'lap-1',
        ptUuid: 'pt-3',
        ptName: 'PT Universitas Pertamina',
        type: 'PTS',
        activityName: 'Bimbingan Teknis Kurikulum Vokasi Energi',
        date: DateTime.now().subtract(const Duration(days: 1)),
        description: 'Penyusunan kurikulum baru.',
        status: 'Approved',
        undanganUrl: 'undangan_pertamina.pdf',
        notulaUrl: 'notula_pertamina.pdf',
        jenisKegiatan: 'Penyusunan Kurikulum',
        tempatKegiatan: 'Gedung Rektorat Pertamina Lt. 3',
        pembuatLaporan: 'Prof. Dr. Ir. Wawan Gunawan',
        ringkasanKegiatan: 'Merumuskan kurikulum berbasis teknologi transisi energi.',
        lingkupTimKerja: 'Tata Usaha dan Barang Milik Negara, Riset dan Pengabdian Masyarakat',
      ),
      Laporan(
        uuid: 'lap-2',
        ptUuid: 'pt-2',
        ptName: 'PT Universitas Ibnu Chaldun',
        type: 'PTS',
        activityName: 'Bimbingan Teknis Jabatan Akademik Asisten Ahli',
        date: DateTime.now().subtract(const Duration(days: 2)),
        description: 'Melaksanakan sosialisasi pengisian berkas JAD.',
        status: 'Approved',
        undanganUrl: 'undangan_uic.pdf',
        notulaUrl: 'notula_uic.pdf',
        jenisKegiatan: 'Sosialisasi dan Asistensi',
        tempatKegiatan: 'Aula Rektorat Lt. 2 Kampus UIC',
        pembuatLaporan: 'Dr. Ahmad Fauzi, M.Pd.',
        ringkasanKegiatan: 'Telah diselenggarakan bimbingan teknis untuk pengisian data LKD dan BKD bagi dosen muda.',
        lingkupTimKerja: 'Kelembagaan dan Kemitraan',
      ),
    ];
  }
}
