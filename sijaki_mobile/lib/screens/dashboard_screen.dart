import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';
import '../providers/data_provider.dart';
import '../theme/sijaki_theme.dart';
import 'laporan_form_screen.dart';
import 'pt_list_screen.dart';
import 'laporan_detail_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final dataProvider = Provider.of<DataProvider>(context);
    final user = authProvider.currentUser;

    return Scaffold(
      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          // If you have a fetch data method, call it here
          // e.g. await Provider.of<DataProvider>(context, listen: false).fetchData();
        },
        child: CustomScrollView(
        slivers: [
          // Grab-style Top Header with Search and Profile
          SliverToBoxAdapter(
            child: Container(
              color: Colors.white,
              padding: const EdgeInsets.fromLTRB(20, 56, 20, 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Greeting & Avatar
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Selamat pagi,',
                            style: GoogleFonts.inter(
                              color: Colors.grey.shade500,
                              fontSize: 12,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            user?.name ?? 'User Sijaki',
                            style: GoogleFonts.outfit(
                              color: const Color(0xFF0F172A),
                              fontSize: 20,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ],
                      ),
                      CircleAvatar(
                        radius: 20,
                        backgroundColor: Colors.transparent,
                        backgroundImage: const AssetImage('assets/images/logo.png'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Grab-style Search Panel "Cari kampus atau usulan..."
                  Container(
                    height: 48,
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(24),
                    ),
                    child: TextField(
                      controller: _searchController,
                      onChanged: (value) {
                        setState(() {
                          _searchQuery = value.toLowerCase();
                        });
                      },
                      decoration: InputDecoration(
                        hintText: 'Cari kampus atau laporan kegiatan...',
                        hintStyle: GoogleFonts.inter(
                          color: Colors.grey.shade500,
                          fontSize: 13,
                        ),
                        prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 20),
                        suffixIcon: _searchQuery.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear, color: Colors.grey, size: 20),
                                onPressed: () {
                                  _searchController.clear();
                                  setState(() {
                                    _searchQuery = '';
                                  });
                                },
                              )
                            : null,
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          if (_searchQuery.isNotEmpty)
            SliverToBoxAdapter(
              child: Container(
                color: Colors.white,
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Hasil Pencarian Laporan', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 12),
                    Builder(
                      builder: (context) {
                        final results = dataProvider.laporans.where((l) => 
                          l.activityName.toLowerCase().contains(_searchQuery) || 
                          l.ptName.toLowerCase().contains(_searchQuery)
                        ).toList();
                        
                        if (results.isEmpty) {
                          return const Padding(
                            padding: EdgeInsets.all(32),
                            child: Center(child: Text('Tidak ditemukan')),
                          );
                        }
                        
                        return ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: results.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 8),
                          itemBuilder: (context, index) {
                            final laporan = results[index];
                            return Card(
                              child: InkWell(
                                borderRadius: BorderRadius.circular(12),
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => LaporanDetailScreen(laporan: laporan),
                                    ),
                                  );
                                },
                                child: Padding(
                                  padding: const EdgeInsets.all(12),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(color: Colors.grey.shade50, shape: BoxShape.circle),
                                        child: const Icon(Icons.assignment_turned_in_outlined, color: SijakiTheme.primary, size: 22),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(laporan.activityName, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                                            const SizedBox(height: 4),
                                            Text(laporan.ptName, style: TextStyle(color: Colors.grey.shade500, fontSize: 11)),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                        decoration: BoxDecoration(color: _getStatusColor(laporan.status).withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                                        child: Text(laporan.status, style: TextStyle(color: _getStatusColor(laporan.status), fontSize: 10, fontWeight: FontWeight.bold)),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            );
                          },
                        );
                      }
                    ),
                    const SizedBox(height: 24),
                    Text('Hasil Pencarian PT', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 12),
                    Builder(
                      builder: (context) {
                        final ptResults = dataProvider.pts.where((p) => 
                          p.name.toLowerCase().contains(_searchQuery) || 
                          p.npsn.contains(_searchQuery)
                        ).toList();
                        
                        if (ptResults.isEmpty) {
                          return const Padding(
                            padding: EdgeInsets.all(32),
                            child: Center(child: Text('Tidak ditemukan')),
                          );
                        }
                        
                        return ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: ptResults.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 8),
                          itemBuilder: (context, index) {
                            final pt = ptResults[index];
                            return Card(
                              child: ListTile(
                                leading: const CircleAvatar(backgroundColor: Colors.white, backgroundImage: AssetImage('assets/images/logo.png')),
                                title: Text(pt.name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                subtitle: Text('NPSN: ${pt.npsn}', style: const TextStyle(fontSize: 12)),
                                trailing: const Icon(Icons.chevron_right),
                                onTap: () {}, // Can navigate to PT Detail
                              ),
                            );
                          },
                        );
                      }
                    ),
                  ],
                ),
              ),
            ),
          if (_searchQuery.isEmpty)
          // Grab-style Grid Services Menu
          SliverToBoxAdapter(
            child: Container(
              color: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  GridView.count(
                    crossAxisCount: 4,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    mainAxisSpacing: 16,
                    crossAxisSpacing: 8,
                    childAspectRatio: 0.9,
                    children: [
                      _buildServiceItem(
                        context,
                        title: 'Laporan PTS',
                        icon: Icons.assignment_outlined,
                        color: Colors.green,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const LaporanFormScreen(type: 'PTS'),
                            ),
                          );
                        },
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Laporan PTN',
                        icon: Icons.assignment_rounded,
                        color: Colors.blue,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const LaporanFormScreen(type: 'PTN'),
                            ),
                          );
                        },
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Histori PT',
                        icon: Icons.history_rounded,
                        color: Colors.orange,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const PtListScreen(),
                            ),
                          );
                        },
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Manajemen PT',
                        icon: Icons.account_balance_rounded,
                        color: Colors.purple,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const PtListScreen(),
                            ),
                          );
                        },
                      ),

                    ],
                  ),
                ],
              ),
            ),
          ),

          // Horizontal Info/Announcement Carousel (Grab promos layout)
          SliverToBoxAdapter(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 24, 20, 12),
                  child: Text(
                    'Info & Metrik Penting',
                    style: GoogleFonts.outfit(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF0F172A),
                    ),
                  ),
                ),
                SizedBox(
                  height: 130,
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    children: [
                      _buildPromoCard(
                        context,
                        title: 'Perguruan Tinggi Swasta',
                        value: '${dataProvider.totalPTS} Kampus',
                        desc: 'Terdaftar aktif di sistem LLDIKTI III',
                        gradient: SijakiTheme.primaryGradient,
                        icon: Icons.school_outlined,
                      ),
                      const SizedBox(width: 12),
                      _buildPromoCard(
                        context,
                        title: 'Perguruan Tinggi Negeri',
                        value: '${dataProvider.totalPTN} Kampus',
                        desc: 'Pengawasan dan fasilitasi berkala',
                        gradient: SijakiTheme.secondaryGradient,
                        icon: Icons.account_balance_rounded,
                      ),
                      const SizedBox(width: 12),
                      _buildPromoCard(
                        context,
                        title: 'Aktivitas Laporan',
                        value: '${dataProvider.totalLaporan} Pengajuan',
                        desc: 'Laporan PTS & PTN terdaftar',
                        gradient: SijakiTheme.accentGradient,
                        icon: Icons.task_outlined,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Laporan Status Pie Chart (Grab styled info card)
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Card(
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Ringkasan Persetujuan Laporan',
                        style: GoogleFonts.outfit(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1E293B),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          // Chart
                          SizedBox(
                            height: 90,
                            width: 90,
                            child: PieChart(
                              PieChartData(
                                sectionsSpace: 2,
                                centerSpaceRadius: 24,
                                sections: [
                                  PieChartSectionData(
                                    color: SijakiTheme.primary,
                                    value: dataProvider.approvedLaporan.toDouble(),
                                    title: '${dataProvider.approvedLaporan}',
                                    radius: 12,
                                    titleStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.white),
                                  ),
                                  PieChartSectionData(
                                    color: Colors.blue,
                                    value: dataProvider.submittedLaporan.toDouble(),
                                    title: '${dataProvider.submittedLaporan}',
                                    radius: 12,
                                    titleStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.white),
                                  ),
                                  PieChartSectionData(
                                    color: Colors.orange,
                                    value: dataProvider.draftLaporan.toDouble(),
                                    title: '${dataProvider.draftLaporan}',
                                    radius: 12,
                                    titleStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.white),
                                  ),
                                  PieChartSectionData(
                                    color: Colors.red,
                                    value: dataProvider.rejectedLaporan.toDouble(),
                                    title: '${dataProvider.rejectedLaporan}',
                                    radius: 12,
                                    titleStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.white),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(width: 24),
                          // Stats
                          Expanded(
                            child: Column(
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    _buildLegendItem('Disetujui', SijakiTheme.primary),
                                    Text('${dataProvider.approvedLaporan}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    _buildLegendItem('Diajukan', Colors.blue),
                                    Text('${dataProvider.submittedLaporan}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    _buildLegendItem('Draft', Colors.orange),
                                    Text('${dataProvider.draftLaporan}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),

          // Recent Activity styled as Grab transactions
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Padding(
                    padding: const EdgeInsets.only(left: 4, bottom: 12),
                    child: Text(
                      'Laporan Terbaru',
                      style: GoogleFonts.outfit(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF0F172A),
                      ),
                    ),
                  ),
                  Builder(
                    builder: (context) {
                      final filteredLaporans = dataProvider.laporans.where((laporan) {
                        return laporan.activityName.toLowerCase().contains(_searchQuery) ||
                               laporan.ptName.toLowerCase().contains(_searchQuery);
                      }).toList();
                      
                      final itemCount = filteredLaporans.length > 5 ? 5 : filteredLaporans.length;

                      if (itemCount == 0 && _searchQuery.isNotEmpty) {
                        return const Padding(
                          padding: EdgeInsets.all(32.0),
                          child: Center(
                            child: Text(
                              'Tidak ada laporan yang ditemukan',
                              style: TextStyle(color: Colors.grey),
                            ),
                          ),
                        );
                      }

                      return ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: itemCount,
                        separatorBuilder: (context, index) => const SizedBox(height: 8),
                        itemBuilder: (context, index) {
                          final laporan = filteredLaporans[index];
                          return Card(
                            child: InkWell(
                              borderRadius: BorderRadius.circular(12),
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => LaporanDetailScreen(laporan: laporan),
                                  ),
                                );
                              },
                              child: Padding(
                                padding: const EdgeInsets.all(12.0),
                                child: Row(
                                  children: [
                                    // Green Icon Wrap like Grab Ride completed
                                    Container(
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: Colors.grey.shade50,
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(Icons.assignment_turned_in_outlined, color: SijakiTheme.primary, size: 22),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            laporan.activityName,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            laporan.ptName,
                                            style: TextStyle(color: Colors.grey.shade500, fontSize: 11),
                                          ),
                                        ],
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: _getStatusColor(laporan.status).withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Text(
                                        laporan.status,
                                        style: TextStyle(
                                          color: _getStatusColor(laporan.status),
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
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
          const SliverToBoxAdapter(
            child: SizedBox(height: 32),
          ),
        ],
      ),
      ),
    );
  }

  Widget _buildServiceItem(
    BuildContext context, {
    required String title,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 46,
            height: 46,
            decoration: BoxDecoration(
              color: color.withOpacity(0.08),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 6),
          Text(
            title,
            textAlign: TextAlign.center,
            style: GoogleFonts.inter(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: const Color(0xFF1E293B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPromoCard(
    BuildContext context, {
    required String title,
    required String value,
    required String desc,
    required Gradient gradient,
    required IconData icon,
  }) {
    return Container(
      width: 240,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                title,
                style: GoogleFonts.outfit(
                  color: Colors.white.withOpacity(0.9),
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                ),
              ),
              Icon(icon, color: Colors.white.withOpacity(0.8), size: 18),
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: GoogleFonts.outfit(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                desc,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.inter(
                  color: Colors.white.withOpacity(0.7),
                  fontSize: 10,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildLegendItem(String title, Color color) {
    return Row(
      children: [
        Container(
          width: 8,
          height: 8,
          decoration: BoxDecoration(
            color: color,
            shape: BoxShape.circle,
          ),
        ),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 11,
            color: Color(0xFF475569),
          ),
        ),
      ],
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'Draft':
        return Colors.orange;
      case 'Submitted':
        return Colors.blue;
      case 'Approved':
        return SijakiTheme.primary;
      case 'Rejected':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }
}
