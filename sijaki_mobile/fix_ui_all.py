import os

def wrap_with_refresh_indicator(filepath):
    with open(filepath, 'r') as f:
        content = f.read()
    
    # We find the body of Scaffold
    import re
    # body: SingleChildScrollView( ... ) or body: ListView( ... ) or body: CustomScrollView( ... )
    
    if "RefreshIndicator" in content:
        return
        
    old_body = "      body: SingleChildScrollView("
    new_body = """      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          setState(() {});
        },
        child: SingleChildScrollView("""
    
    if old_body in content:
        content = content.replace(old_body, new_body)
        # add closing parenthesis at the very end of Scaffold
        # Scaffold usually ends at `    );` before `  }`
        content = re.sub(r'(\s+)body: RefreshIndicator\((.*?)\n    \);\n  \}', r'\1body: RefreshIndicator(\2\n      ),\n    );\n  }', content, flags=re.DOTALL)
        
        with open(filepath, 'w') as f:
            f.write(content)
        print(f"Wrapped {filepath}")

wrap_with_refresh_indicator('lib/screens/dashboard_screen.dart')

def wrap_pt_list():
    with open('lib/screens/pt_list_screen.dart', 'r') as f:
        content = f.read()
    if 'RefreshIndicator' not in content:
        old_body = "      body: ListView.builder("
        new_body = """      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          setState(() {});
        },
        child: ListView.builder("""
        content = content.replace(old_body, new_body)
        content = content.replace("    );\n  }\n}", "      ),\n    );\n  }\n}")
        with open('lib/screens/pt_list_screen.dart', 'w') as f:
            f.write(content)
        print("Wrapped pt_list_screen.dart")
wrap_pt_list()

def wrap_laporan_list():
    with open('lib/screens/laporan_list_screen.dart', 'r') as f:
        content = f.read()
    if 'RefreshIndicator' not in content:
        old_body = "      body: SingleChildScrollView("
        new_body = """      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
          setState(() {});
        },
        child: SingleChildScrollView("""
        content = content.replace(old_body, new_body)
        content = content.replace("    );\n  }\n}", "      ),\n    );\n  }\n}")
        with open('lib/screens/laporan_list_screen.dart', 'w') as f:
            f.write(content)
        print("Wrapped laporan_list_screen.dart")
wrap_laporan_list()

def layout_form():
    with open('lib/screens/laporan_form_screen.dart', 'r') as f:
        content = f.read()
    
    # We wrap Column inside a Card
    old_col = """          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,"""
    new_col = """          child: Card(
            elevation: 2,
            shadowColor: Colors.black12,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,"""
    
    if "child: Card(" not in content:
        content = content.replace(old_col, new_col)
        # we have 2 levels of depth added. Column ends right before Form ends.
        old_end = """            ],
          ),
        ),
      ),
      ),
    );
  }"""
        new_end = """              ],
              ),
            ),
          ),
        ),
      ),
      ),
    );
  }"""
        content = content.replace(old_end, new_end)
        with open('lib/screens/laporan_form_screen.dart', 'w') as f:
            f.write(content)
        print("Re-layout laporan_form_screen.dart")

layout_form()

