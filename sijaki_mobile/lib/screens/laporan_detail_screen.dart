import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/laporan_model.dart';
import '../theme/sijaki_theme.dart';
import '../providers/data_provider.dart';
import 'laporan_form_screen.dart';

class LaporanDetailScreen extends StatelessWidget {
  final Laporan laporan;

  const LaporanDetailScreen({super.key, required this.laporan});

  @override
  Widget build(BuildContext context) {
    final dataProvider = Provider.of<DataProvider>(context);
    // Fetch latest instance of the laporan in case it has been edited
    final currentLaporan = dataProvider.laporans.firstWhere(
      (element) => element.uuid == laporan.uuid,
      orElse: () => laporan,
    );

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Laporan Kegiatan'),
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_note_rounded, color: SijakiTheme.primary, size: 28),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => LaporanFormScreen(
                    type: currentLaporan.type,
                    laporan: currentLaporan,
                  ),
                ),
              );
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Status Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: (currentLaporan.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            'Laporan ${currentLaporan.type}',
                            style: TextStyle(
                              color: currentLaporan.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent,
                              fontWeight: FontWeight.bold,
                              fontSize: 11,
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: _getStatusColor(currentLaporan.status).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Text(
                            currentLaporan.status,
                            style: TextStyle(
                              color: _getStatusColor(currentLaporan.status),
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      currentLaporan.activityName,
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontSize: 18,
                        height: 1.4,
                      ),
                    ),
                    const Divider(height: 32),
                    _buildDetailItem(context, Icons.school_rounded, 'Perguruan Tinggi', currentLaporan.ptName),
                    _buildDetailItem(context, Icons.label_important_outline_rounded, 'Jenis Kegiatan', currentLaporan.jenisKegiatan),
                    _buildDetailItem(context, Icons.calendar_today_rounded, 'Tanggal Kegiatan', 
                        '${currentLaporan.date.day}/${currentLaporan.date.month}/${currentLaporan.date.year}'),
                    _buildDetailItem(context, Icons.location_on_outlined, 'Tempat Kegiatan', currentLaporan.tempatKegiatan),
                    _buildDetailItem(context, Icons.person_outline_rounded, 'Pembuat Laporan', currentLaporan.pembuatLaporan),
                    _buildDetailItem(context, Icons.group_outlined, 'Lingkup Tim Kerja', currentLaporan.lingkupTimKerja),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Description Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Ringkasan Kegiatan',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const SizedBox(height: 12),
                    Text(
                      currentLaporan.ringkasanKegiatan.isNotEmpty 
                          ? currentLaporan.ringkasanKegiatan.replaceAll(RegExp(r'<[^>]*>|&[^;]+;'), '')
                          : 'Tidak ada ringkasan kegiatan.',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        height: 1.6,
                        color: Colors.grey.shade700,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Documentations / Downloads
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Lampiran Berkas',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const Divider(height: 24),
                    _buildDocumentLink(
                      context, 
                      title: 'Surat Undangan Kegiatan', 
                      fileName: currentLaporan.undanganUrl,
                    ),
                    const SizedBox(height: 12),
                    _buildDocumentLink(
                      context, 
                      title: 'Notula & Daftar Hadir', 
                      fileName: currentLaporan.notulaUrl,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailItem(BuildContext context, IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 20, color: Colors.grey.shade400),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    color: Colors.grey.shade400,
                    fontSize: 11,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: const TextStyle(
                    fontWeight: FontWeight.w500,
                    fontSize: 14,
                    color: Color(0xFF1E293B),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDocumentLink(BuildContext context, {required String title, required String fileName}) {
    final bool hasFile = fileName.isNotEmpty;

    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: (hasFile ? SijakiTheme.secondary : Colors.grey).withOpacity(0.1),
            shape: BoxShape.circle,
          ),
          child: Icon(
            Icons.picture_as_pdf_rounded, 
            color: hasFile ? SijakiTheme.secondary : Colors.grey,
            size: 20,
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                  color: Color(0xFF1E293B),
                ),
              ),
              const SizedBox(height: 2),
              Text(
                hasFile ? fileName : 'Berkas belum diunggah',
                style: TextStyle(
                  fontSize: 11,
                  color: Colors.grey.shade500,
                ),
              ),
            ],
          ),
        ),
        if (hasFile)
          IconButton(
            icon: const Icon(Icons.download_rounded, color: SijakiTheme.secondary),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text('Mengunduh berkas $fileName...'),
                  backgroundColor: SijakiTheme.accent,
                ),
              );
            },
          ),
      ],
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'Draft':
        return SijakiTheme.primary;
      case 'Submitted':
        return SijakiTheme.secondary;
      case 'Approved':
        return SijakiTheme.accent;
      case 'Rejected':
        return SijakiTheme.error;
      default:
        return Colors.grey;
    }
  }
}
