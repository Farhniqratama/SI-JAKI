import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

# Remove the decoration part
old_body = """      body: Container(
        decoration: const BoxDecoration(
          image: DecorationImage(
            image: AssetImage('assets/images/logo.png'),
            repeat: ImageRepeat.repeat,
            opacity: 0.05,
          ),
        ),
        child: Form("""

new_body = """      body: Container(
        color: Colors.white,
        child: Form("""

content = content.replace(old_body, new_body)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(content)

# Update theme
with open('lib/theme/sijaki_theme.dart', 'r') as f:
    theme = f.read()

theme = theme.replace('static const Color background = Color(0xFFF3F4F6);', 'static const Color background = Colors.white;')
theme = theme.replace('scaffoldBackgroundColor: background,', 'scaffoldBackgroundColor: Colors.white,')

with open('lib/theme/sijaki_theme.dart', 'w') as f:
    f.write(theme)
    
print("Changes applied!")
