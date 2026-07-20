with open('lib/screens/dashboard_screen.dart', 'r') as f:
    dashboard_c = f.read()

# Replace the search box
old_search = """                  // Grab-style Search Panel "Cari kampus atau usulan..."
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(24),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.search, color: Colors.grey, size: 20),
                        const SizedBox(width: 12),
                        Text(
                          'Cari kampus atau laporan kegiatan...',
                          style: GoogleFonts.inter(
                            color: Colors.grey.shade500,
                            fontSize: 13,
                          ),
                        ),
                      ],
                    ),
                  ),"""

new_search = """                  // Grab-style Search Panel "Cari kampus atau usulan..."
                  Container(
                    height: 48,
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(24),
                    ),
                    child: TextField(
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
                    ),
                  ),"""

dashboard_c = dashboard_c.replace(old_search, new_search)

# Remove the 4 bottom icons
old_icons = """                      _buildServiceItem(
                        context,
                        title: 'Pengguna',
                        icon: Icons.people_outline_rounded,
                        color: Colors.teal,
                        onTap: () {},
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Keamanan',
                        icon: Icons.security_rounded,
                        color: Colors.red,
                        onTap: () {},
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Bantuan',
                        icon: Icons.help_outline_rounded,
                        color: Colors.indigo,
                        onTap: () {},
                      ),
                      _buildServiceItem(
                        context,
                        title: 'Tentang',
                        icon: Icons.info_outline_rounded,
                        color: Colors.blueGrey,
                        onTap: () {},
                      ),"""

dashboard_c = dashboard_c.replace(old_icons, "")

with open('lib/screens/dashboard_screen.dart', 'w') as f:
    f.write(dashboard_c)


with open('lib/screens/profile_screen.dart', 'r') as f:
    profile_c = f.read()

# Remove the simulasi peran akses card
old_card = """            // Role simulation option card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Simulasi Peran Akses (Demo)',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Sesuaikan peran pengguna untuk melihat visualisasi dan hak akses menu.',
                      style: TextStyle(color: Colors.grey.shade500, fontSize: 11),
                    ),
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _buildSimulateRoleButton(context, authProvider, 'User'),
                        _buildSimulateRoleButton(context, authProvider, 'Admin'),
                        _buildSimulateRoleButton(context, authProvider, 'Dev'),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),"""

profile_c = profile_c.replace(old_card, "")

with open('lib/screens/profile_screen.dart', 'w') as f:
    f.write(profile_c)

