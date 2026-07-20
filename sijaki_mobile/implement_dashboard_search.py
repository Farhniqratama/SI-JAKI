import re

with open('lib/screens/dashboard_screen.dart', 'r') as f:
    c = f.read()

# 1. Convert StatelessWidget to StatefulWidget
old_class_def = """class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {"""

new_class_def = """class DashboardScreen extends StatefulWidget {
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
  Widget build(BuildContext context) {"""

c = c.replace(old_class_def, new_class_def)

# 2. Update the TextField to use the controller and onChanged
old_search_box = """                    child: TextField(
                      decoration: InputDecoration(
                        hintText: 'Cari kampus atau laporan kegiatan...',
                        hintStyle: GoogleFonts.inter(
                          color: Colors.grey.shade500,
                          fontSize: 13,
                        ),
                        prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 20),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      ),
                    ),"""

new_search_box = """                    child: TextField(
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
                    ),"""

c = c.replace(old_search_box, new_search_box)

# 3. Filter the recentLaporans
old_list_builder = """                  ListView.separated(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: dataProvider.recentLaporans.length,
                    separatorBuilder: (context, index) => const Divider(height: 1),
                    itemBuilder: (context, index) {
                      final laporan = dataProvider.recentLaporans[index];"""

new_list_builder = """                  Builder(
                    builder: (context) {
                      final filteredLaporans = dataProvider.recentLaporans.where((laporan) {
                        return laporan.activityName.toLowerCase().contains(_searchQuery) ||
                               laporan.ptName.toLowerCase().contains(_searchQuery);
                      }).toList();

                      if (filteredLaporans.isEmpty) {
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
                        itemCount: filteredLaporans.length,
                        separatorBuilder: (context, index) => const Divider(height: 1),
                        itemBuilder: (context, index) {
                          final laporan = filteredLaporans[index];"""

c = c.replace(old_list_builder, new_list_builder)

# We also need to close the Builder block that we opened!
# It replaces the end of ListView.separated.
# Let's find the end of ListView.separated.
old_list_end = """                      );
                    },
                  ),"""

new_list_end = """                      );
                        },
                      );
                    },
                  ),"""

# Since there might be multiple occurrences, let's just replace the first one that matches the end of the list builder.
# Wait, actually, the ListView inside DashboardScreen ends exactly like that under recentLaporans.
c = c.replace(old_list_end, new_list_end, 1)

with open('lib/screens/dashboard_screen.dart', 'w') as f:
    f.write(c)

