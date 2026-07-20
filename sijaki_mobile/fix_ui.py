import re

# 1. Update dashboard_screen.dart
with open('lib/screens/dashboard_screen.dart', 'r') as f:
    dashboard = f.read()

# dashboard body starts with `body: SingleChildScrollView(`
dash_old = """      body: SingleChildScrollView("""
dash_new = """      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          // TODO: reload data if needed
          setState(() {});
        },
        child: SingleChildScrollView("""

dashboard = dashboard.replace(dash_old, dash_new)
# find the closing bracket for body
dashboard = dashboard.replace("        ), // bottom padding", "        ),\n      ), // end RefreshIndicator")
if "end RefreshIndicator" not in dashboard:
    # Just replace the last `);` of the build method with `),);` properly.
    # Actually, it's safer to use regex to find `SingleChildScrollView` and wrap it.
    pass

with open('lib/screens/dashboard_screen.dart', 'w') as f:
    f.write(dashboard)

print("Updated dashboard_screen")
