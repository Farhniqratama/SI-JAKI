import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    c = f.read()

old_toolbar = """                          child: quill.QuillSimpleToolbar(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillSimpleToolbarConfigurations("""
new_toolbar = """                          child: quill.QuillSimpleToolbar(
                            configurations: quill.QuillSimpleToolbarConfigurations(
                              controller: _ringkasanQuillController,"""

old_editor = """                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillEditorConfigurations("""
new_editor = """                          child: quill.QuillEditor.basic(
                            configurations: quill.QuillEditorConfigurations(
                              controller: _ringkasanQuillController,"""

c = c.replace(old_toolbar, new_toolbar)
c = c.replace(old_editor, new_editor)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(c)

