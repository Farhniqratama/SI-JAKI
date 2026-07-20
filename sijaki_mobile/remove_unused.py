import re

with open('lib/screens/profile_screen.dart', 'r') as f:
    c = f.read()

# _buildSimulateRoleButton function should be removed
# Widget _buildSimulateRoleButton(BuildContext context, AuthProvider authProvider, String role) { ... }
c = re.sub(r'  Widget _buildSimulateRoleButton\(.*?\}', '', c, flags=re.DOTALL)

with open('lib/screens/profile_screen.dart', 'w') as f:
    f.write(c)

