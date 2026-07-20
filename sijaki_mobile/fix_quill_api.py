import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    c = f.read()

# Replace QuillSimpleToolbar with QuillToolbar.basic
c = re.sub(
    r'quill\.QuillSimpleToolbar\([^)]*configurations:[^)]*\),', 
    r'quill.QuillToolbar.basic(controller: _ringkasanQuillController),', 
    c, flags=re.DOTALL
)
# There might be nested parentheses in configurations, so let's do a more brutal replacement since I know the exact string.

old_toolbar = """                          child: quill.QuillSimpleToolbar(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillSimpleToolbarConfigurations(
                              showFontFamily: false,
                              showFontSize: false,
                              showSearchButton: false,
                              showInlineCode: false,
                              showCodeBlock: false,
                              showIndent: false,
                              showColorButton: false,
                              showBackgroundColorButton: false,
                              showClearFormat: false,
                              showAlignmentButtons: true,
                              showLeftAlignment: true,
                              showCenterAlignment: true,
                              showRightAlignment: true,
                              showJustifyAlignment: true,
                            ),
                          ),"""

new_toolbar = """                          child: quill.QuillToolbar.basic(controller: _ringkasanQuillController),"""

old_editor = """                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillEditorConfigurations(
                              padding: EdgeInsets.zero,
                            ),
                          ),"""

new_editor = """                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            readOnly: false,
                          ),"""

c = c.replace(old_toolbar, new_toolbar)
c = c.replace(old_editor, new_editor)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(c)

