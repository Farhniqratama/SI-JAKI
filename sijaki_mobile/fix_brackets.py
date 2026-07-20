with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

# find where SingleChildScrollView ends. It's the body of the Scaffold.
# Currently it looks like:
#         child: SingleChildScrollView(
#           padding: const EdgeInsets.all(24.0),
# ... 
#       body: Container(
#        decoration: const BoxDecoration( ... ),
#        child: SingleChildScrollView( ...
#
# But wait, did I even replace body correctly?
print("Container found:", "body: Container(" in content)

import re
# We need to find the `return Scaffold(` and its closing
