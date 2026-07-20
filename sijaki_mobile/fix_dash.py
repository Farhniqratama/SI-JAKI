import re

with open('lib/screens/dashboard_screen.dart', 'r') as f:
    content = f.read()

old_body = "      body: CustomScrollView("
new_body = """      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          // If you have a fetch data method, call it here
          // e.g. await Provider.of<DataProvider>(context, listen: false).fetchData();
        },
        child: CustomScrollView("""

if old_body in content:
    content = content.replace(old_body, new_body)
    # The end of dashboard_screen is typically just `  }\n}`
    # Wait, the end of Scaffold in dashboard_screen is:
    #     );
    #   }
    content = re.sub(r'(\s+)body: RefreshIndicator\((.*?)\n    \);\n  \}', r'\1body: RefreshIndicator(\2\n      ),\n    );\n  }', content, flags=re.DOTALL)
    
    with open('lib/screens/dashboard_screen.dart', 'w') as f:
        f.write(content)
    print("Wrapped dashboard")
else:
    print("Not found!")
