import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/data_provider.dart';
import '../models/pt_model.dart';
import '../theme/sijaki_theme.dart';
import 'pt_detail_screen.dart';

class PtListScreen extends StatefulWidget {
  const PtListScreen({super.key});

  @override
  State<PtListScreen> createState() => _PtListScreenState();
}

class _PtListScreenState extends State<PtListScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';
  String _accreditationFilter = 'Semua';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
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
    final pts = dataProvider.pts;

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Daftar Perguruan Tinggi',
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
            Tab(text: 'Semua'),
            Tab(text: 'PTS'),
            Tab(text: 'PTN'),
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
                    hintText: 'Cari nama atau NPSN...',
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
                // Accreditation filter button
                PopupMenuButton<String>(
                  icon: const Icon(Icons.filter_list_rounded, color: SijakiTheme.primary),
                  onSelected: (value) {
                    setState(() {
                      _accreditationFilter = value;
                    });
                  },
                  itemBuilder: (context) => [
                    const PopupMenuItem(value: 'Semua', child: Text('Semua Akreditasi')),
                    const PopupMenuItem(value: 'Unggul', child: Text('Unggul')),
                    const PopupMenuItem(value: 'A', child: Text('Akreditasi A')),
                    const PopupMenuItem(value: 'Baik Sekali', child: Text('Baik Sekali')),
                    const PopupMenuItem(value: 'Baik', child: Text('Baik')),
                  ],
                ),
              ],
            ),
          ),
          // Active filter chip reminder
          if (_accreditationFilter != 'Semua')
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Chip(
                  label: Text('Akreditasi: $_accreditationFilter'),
                  onDeleted: () {
                    setState(() {
                      _accreditationFilter = 'Semua';
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
                _buildPtList(pts), // Semua
                _buildPtList(pts.where((pt) => pt.type == 'PTS').toList()), // PTS
                _buildPtList(pts.where((pt) => pt.type == 'PTN').toList()), // PTN
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPtList(List<PerguruanTinggi> rawList) {
    // Filter list based on search and accreditation filters
    final filteredList = rawList.where((pt) {
      final matchesSearch = pt.name.toLowerCase().contains(_searchQuery) ||
          pt.npsn.contains(_searchQuery);
      final matchesAccreditation = _accreditationFilter == 'Semua' ||
          pt.acaccreditationMatches(_accreditationFilter);
      return matchesSearch && matchesAccreditation;
    }).toList();

    if (filteredList.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.school_outlined, size: 64, color: Colors.grey.shade300),
            const SizedBox(height: 12),
            Text(
              'Perguruan Tinggi tidak ditemukan',
              style: TextStyle(color: Colors.grey.shade500, fontSize: 16),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      itemCount: filteredList.length,
      itemBuilder: (context, index) {
        final pt = filteredList[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          child: InkWell(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => PtDetailScreen(pt: pt),
                ),
              );
            },
            borderRadius: BorderRadius.circular(16),
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                children: [
                  // Logo Placeholder
                  Container(
                    width: 60,
                    height: 60,
                    decoration: BoxDecoration(
                      color: SijakiTheme.primary.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Text(
                        pt.name.split(' ').map((e) => e[0]).take(2).join(''),
                        style: const TextStyle(
                          color: SijakiTheme.primary,
                          fontWeight: FontWeight.bold,
                          fontSize: 18,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  // Details
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                              decoration: BoxDecoration(
                                color: (pt.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent).withOpacity(0.1),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                pt.type,
                                style: TextStyle(
                                  color: pt.type == 'PTN' ? SijakiTheme.secondary : SijakiTheme.accent,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            const SizedBox(width: 8),
                            Text(
                              'NPSN: ${pt.npsn}',
                              style: TextStyle(color: Colors.grey.shade500, fontSize: 11),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          pt.name,
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 15,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            const Icon(Icons.location_on_outlined, size: 12, color: Colors.grey),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                pt.address,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Icon(Icons.chevron_right, color: Colors.grey),
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}

extension on PerguruanTinggi {
  bool acaccreditationMatches(String filter) {
    return accreditation.toLowerCase() == filter.toLowerCase();
  }
}
