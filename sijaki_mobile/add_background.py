with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

body_old = """      body: Form("""
body_new = """      body: Container(
        decoration: const BoxDecoration(
          image: DecorationImage(
            image: AssetImage('assets/images/logo.png'),
            repeat: ImageRepeat.repeat,
            opacity: 0.05,
          ),
        ),
        child: Form("""

content = content.replace(body_old, body_new)

# Since I added a Container wrapper, I need an extra closing bracket at the end.
# Let's find the end of the Scaffold.
#     ); // end Scaffold
end_old = """    );
  }
}"""
end_new = """      ),
    );
  }
}"""

content = content.replace(end_old, end_new)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(content)
print("Added background successfully!")
