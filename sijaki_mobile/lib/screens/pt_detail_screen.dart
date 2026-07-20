import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../models/pt_model.dart';
import '../providers/data_provider.dart';
import '../theme/sijaki_theme.dart';
import 'laporan_detail_screen.dart';

class PtDetailScreen extends StatelessWidget {
  final PerguruanTinggi pt;

  const PtDetailScreen({super.key, required this.pt});

  @override
  Widget build(BuildContext context) {
    final dataProvider = Provider.of<DataProvider>(context);
    
    // Filter laporans for this PT
    final ptLaporans = dataProvider.laporans.where((lap) => lap.ptUuid == pt.uuid).toList();

    return Scaffold(
      appBar: AppBar(
        iconTheme: const IconThemeData(color: Colors.white),
        title: const Text('Detail Kampus & Histori', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: SijakiTheme.primaryGradient,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Header Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  children: [
                    Container(
                      width: 80,
                      height: 80,
                      decoration: BoxDecoration(
                        color: SijakiTheme.primary.withOpacity(0.05),
                        shape: BoxShape.circle,
                      ),
                      child: Center(
                        child: Text(
                          pt.name.split(' ').map((e) => e[0]).take(2).join(''),
                          style: const TextStyle(
                            color: SijakiTheme.primary,
                            fontWeight: FontWeight.bold,
                            fontSize: 26,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      pt.name,
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontSize: 20,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: (pt.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            pt.type,
                            style: TextStyle(
                              color: pt.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: pt.status == 'Aktif' ? Colors.green.withOpacity(0.1) : Colors.amber.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            pt.status,
                            style: TextStyle(
                              color: pt.status == 'Aktif' ? Colors.green : Colors.amber.shade700,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Statistics Grid Row
            Row(
              children: [
                Expanded(
                  child: _buildMetricCard(
                    context,
                    title: 'Akreditasi',
                    value: pt.accreditation,
                    icon: Icons.verified_rounded,
                    color: Colors.purple,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildMetricCard(
                    context,
                    title: 'Dosen Tetap',
                    value: pt.lecturersCount.toString(),
                    icon: Icons.people_alt_rounded,
                    color: SijakiTheme.secondary,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            _buildMetricCard(
              context,
              title: 'Total Mahasiswa Aktif',
              value: pt.studentsCount.toString(),
              icon: Icons.school_rounded,
              color: SijakiTheme.accent,
            ),
            const SizedBox(height: 16),

            // Detailed profile list
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Informasi Umum',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const Divider(height: 24),
                    _buildDetailRow(Icons.pin_rounded, 'NPSN', pt.npsn),
                    _buildDetailRow(Icons.location_on_outlined, 'Alamat', pt.address),
                    _buildDetailRow(Icons.language_rounded, 'Website', pt.website, isLink: true),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Histori Laporan Pembinaan PT (like desktop)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Histori Laporan Pembinaan',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const Divider(height: 24),
                    ptLaporans.isEmpty
                        ? Padding(
                            padding: const EdgeInsets.symmetric(vertical: 24.0),
                            child: Center(
                              child: Column(
                                children: [
                                  Icon(Icons.history_toggle_off_rounded, color: Colors.grey.shade400, size: 48),
                                  const SizedBox(height: 12),
                                  Text(
                                    'Belum ada riwayat laporan pembinaan.',
                                    style: TextStyle(color: Colors.grey.shade500, fontSize: 13),
                                    textAlign: TextAlign.center,
                                  ),
                                ],
                              ),
                            ),
                          )
                        : ListView.separated(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: ptLaporans.length,
                            separatorBuilder: (context, index) => const Divider(height: 20),
                            itemBuilder: (context, index) {
                              final laporan = ptLaporans[index];
                              return ListTile(
                                contentPadding: EdgeInsets.zero,
                                leading: Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: SijakiTheme.primary.withOpacity(0.08),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(Icons.description_outlined, color: SijakiTheme.primary, size: 20),
                                ),
                                title: Text(
                                  laporan.jenisKegiatan,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 13,
                                    color: Color(0xFF1E293B),
                                  ),
                                ),
                                subtitle: Padding(
                                  padding: const EdgeInsets.only(top: 4.0),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.calendar_month, size: 12, color: Colors.grey),
                                      const SizedBox(width: 4),
                                      Text(
                                        DateFormat('dd MMM yyyy', 'id').format(laporan.date),
                                        style: const TextStyle(fontSize: 11, color: Colors.grey),
                                      ),
                                      const SizedBox(width: 12),
                                      const Icon(Icons.person, size: 12, color: Colors.grey),
                                      const SizedBox(width: 4),
                                      Expanded(
                                        child: Text(
                                          laporan.pembuatLaporan,
                                          style: const TextStyle(fontSize: 11, color: Colors.grey),
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                trailing: const Icon(Icons.chevron_right_rounded, color: Colors.grey),
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => LaporanDetailScreen(laporan: laporan),
                                    ),
                                  );
                                },
                              );
                            },
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

  Widget _buildMetricCard(
    BuildContext context, {
    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      color: Colors.grey.shade500,
                      fontSize: 11,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    value,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String value, {bool isLink = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16.0),
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
                  style: TextStyle(
                    fontWeight: FontWeight.w500,
                    fontSize: 14,
                    color: isLink ? SijakiTheme.secondary : const Color(0xFF1E293B),
                    decoration: isLink ? TextDecoration.underline : TextDecoration.none,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
