import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/data_provider.dart';
import '../models/laporan_model.dart';
import '../theme/sijaki_theme.dart';
import 'laporan_detail_screen.dart';
import 'laporan_form_screen.dart';

class LaporanListScreen extends StatefulWidget {
  const LaporanListScreen({super.key});

  @override
  State<LaporanListScreen> createState() => _LaporanListScreenState();
}

class _LaporanListScreenState extends State<LaporanListScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';
  String _statusFilter = 'Semua';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final dataProvider = Provider.of<DataProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Laporan Kegiatan',
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.white,
        bottom: TabBar(
          controller: _tabController,
          labelColor: SijakiTheme.primary,
          unselectedLabelColor: Colors.grey,
          indicatorColor: SijakiTheme.primary,
          tabs: const [
            Tab(text: 'Laporan PTS'),
            Tab(text: 'Laporan PTN'),
          ],
        ),
      ),
      body: Column(
        children: [
          // Search & Filter Panel
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Row(
              children: [
                Expanded(
                  child: SearchBar(
                    controller: _searchController,
                    hintText: 'Cari aktivitas atau nama PT...',
                    leading: const Icon(Icons.search, color: Colors.grey),
                    elevation: WidgetStateProperty.all(0),
                    backgroundColor: WidgetStateProperty.all(Colors.grey.shade100),
                    onChanged: (value) {
                      setState(() {
                        _searchQuery = value.toLowerCase();
                      });
                    },
                  ),
                ),
                const SizedBox(width: 8),
                PopupMenuButton<String>(
                  icon: const Icon(Icons.filter_list_rounded, color: SijakiTheme.primary),
                  onSelected: (value) {
                    setState(() {
                      _statusFilter = value;
                    });
                  },
                  itemBuilder: (context) => [
                    const PopupMenuItem(value: 'Semua', child: Text('Semua Status')),
                    const PopupMenuItem(value: 'Draft', child: Text('Draft')),
                    const PopupMenuItem(value: 'Submitted', child: Text('Submitted')),
                    const PopupMenuItem(value: 'Approved', child: Text('Approved')),
                    const PopupMenuItem(value: 'Rejected', child: Text('Rejected')),
                  ],
                ),
              ],
            ),
          ),
          if (_statusFilter != 'Semua')
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Chip(
                  label: Text('Status: $_statusFilter'),
                  onDeleted: () {
                    setState(() {
                      _statusFilter = 'Semua';
                    });
                  },
                ),
              ),
            ),
          // Tab views
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildLaporanList(dataProvider.getLaporansByType('PTS'), dataProvider),
                _buildLaporanList(dataProvider.getLaporansByType('PTN'), dataProvider),
              ],
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: SijakiTheme.primary,
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => LaporanFormScreen(
                type: _tabController.index == 0 ? 'PTS' : 'PTN',
              ),
            ),
          );
        },
        icon: const Icon(Icons.add, color: Colors.white),
        label: const Text('Buat Laporan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }

  Widget _buildLaporanList(List<Laporan> rawList, DataProvider provider) {
    final filteredList = rawList.where((lap) {
      final matchesSearch = lap.activityName.toLowerCase().contains(_searchQuery) ||
          lap.ptName.toLowerCase().contains(_searchQuery);
      final matchesStatus = _statusFilter == 'Semua' || lap.status == _statusFilter;
      return matchesSearch && matchesStatus;
    }).toList();

    if (filteredList.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.assignment_outlined, size: 64, color: Colors.grey.shade300),
            const SizedBox(height: 12),
            Text(
              'Laporan tidak ditemukan',
              style: TextStyle(color: Colors.grey.shade500, fontSize: 16),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.fromLTRB(16, 0, 16, 80),
      itemCount: filteredList.length,
      itemBuilder: (context, index) {
        final laporan = filteredList[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          child: Dismissible(
            key: Key(laporan.uuid),
            direction: DismissDirection.endToStart,
            background: Container(
              alignment: Alignment.centerRight,
              padding: const EdgeInsets.symmetric(horizontal: 20),
              decoration: BoxDecoration(
                color: SijakiTheme.error,
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(Icons.delete_forever, color: Colors.white, size: 28),
            ),
            confirmDismiss: (direction) async {
              return await showDialog(
                context: context,
                builder: (context) => AlertDialog(
                  title: const Text('Hapus Laporan?'),
                  content: const Text('Apakah Anda yakin ingin menghapus laporan kegiatan ini secara permanen?'),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.of(context).pop(false),
                      child: const Text('Batal'),
                    ),
                    TextButton(
                      style: TextButton.styleFrom(foregroundColor: SijakiTheme.error),
                      onPressed: () => Navigator.of(context).pop(true),
                      child: const Text('Hapus'),
                    ),
                  ],
                ),
              );
            },
            onDismissed: (direction) {
              provider.deleteLaporan(laporan.uuid);
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Laporan berhasil dihapus.')),
              );
            },
            child: InkWell(
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => LaporanDetailScreen(laporan: laporan),
                  ),
                );
              },
              borderRadius: BorderRadius.circular(16),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          '${laporan.date.day}/${laporan.date.month}/${laporan.date.year}',
                          style: TextStyle(color: Colors.grey.shade400, fontSize: 11),
                        ),
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
                    const SizedBox(height: 8),
                    Text(
                      laporan.activityName,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 15,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        const Icon(Icons.school_outlined, size: 14, color: Colors.grey),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            laporan.ptName,
                            style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
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
