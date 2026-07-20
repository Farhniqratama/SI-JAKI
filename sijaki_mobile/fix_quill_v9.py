with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    c = f.read()

old_toolbar = "                          child: quill.QuillToolbar.basic(controller: _ringkasanQuillController),"
new_toolbar = """                          child: quill.QuillSimpleToolbar(
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

old_editor = """                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            readOnly: false,
                          ),"""
new_editor = """                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillEditorConfigurations(
                              padding: EdgeInsets.zero,
                            ),
                          ),"""

c = c.replace(old_toolbar, new_toolbar)
c = c.replace(old_editor, new_editor)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(c)

