import re

with open('lib/screens/dashboard_screen.dart', 'r') as f:
    c = f.read()

# 1. Fix Avatar
old_avatar = """                      CircleAvatar(
                        radius: 20,
                        backgroundColor: SijakiTheme.primary.withOpacity(0.1),
                        backgroundImage: user?.avatarUrl.isNotEmpty == true
                            ? NetworkImage(user!.avatarUrl)
                            : null,
                        child: user?.avatarUrl.isEmpty == true
                            ? const Icon(Icons.person, color: SijakiTheme.primary)
                            : null,
                      ),"""

new_avatar = """                      CircleAvatar(
                        radius: 20,
                        backgroundColor: Colors.transparent,
                        backgroundImage: const AssetImage('assets/images/logo.png'),
                      ),"""

c = c.replace(old_avatar, new_avatar)

# 2. Fix Search Functionality
# Currently, CustomScrollView has slivers: [ SliverToBoxAdapter(header), SliverToBoxAdapter(grid), SliverToBoxAdapter(recentLaporans) ]
# If _searchQuery is not empty, we hide the grid and recentLaporans, and show search results!

old_build = """    return Scaffold(
      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          // If you have a fetch data method, call it here
          // e.g. await Provider.of<DataProvider>(context, listen: false).fetchData();
        },
        child: CustomScrollView(
        slivers: ["""

new_build = """    return Scaffold(
      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          // If you have a fetch data method, call it here
          // e.g. await Provider.of<DataProvider>(context, listen: false).fetchData();
        },
        child: CustomScrollView(
        slivers: ["""

c = c.replace(old_build, new_build)

# Wait, the easiest way to swap out the content is using if (_searchQuery.isNotEmpty) in the slivers array.

old_slivers = """          // Grab-style Grid Services Menu
          SliverToBoxAdapter(
            child: Container(
              color: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,"""

new_slivers = """          if (_searchQuery.isNotEmpty)
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
                crossAxisAlignment: CrossAxisAlignment.start,"""

c = c.replace(old_slivers, new_slivers)

# Finally, we also need to hide the Recent Laporans block if _searchQuery is not empty.
old_recent = """          SliverToBoxAdapter(
            child: Container(
              color: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Laporan Terbaru',"""

new_recent = """          if (_searchQuery.isEmpty)
          SliverToBoxAdapter(
            child: Container(
              color: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Laporan Terbaru',"""

c = c.replace(old_recent, new_recent)

with open('lib/screens/dashboard_screen.dart', 'w') as f:
    f.write(c)

