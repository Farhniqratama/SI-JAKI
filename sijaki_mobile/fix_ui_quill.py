with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    c = f.read()

# The UI section is messed up right after pembuatLaporan.
# Let's find:
#               validator: (value) => value == null || value.trim().isEmpty ? 'Pembuat Laporan wajib diisi' : null,
#               ),
#               const SizedBox(height: 16),
#               
#               ),
#               Padding(

old = """              validator: (value) => value == null || value.trim().isEmpty ? 'Pembuat Laporan wajib diisi' : null,
              ),
              const SizedBox(height: 16),

              ),
              Padding("""

new = """              validator: (value) => value == null || value.trim().isEmpty ? 'Pembuat Laporan wajib diisi' : null,
              ),
              const SizedBox(height: 16),

              // Ringkasan Kegiatan
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Ringkasan Kegiatan *', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
                  const SizedBox(height: 8),
                  Container(
                    decoration: BoxDecoration(
                      border: Border.all(color: Colors.grey.shade300, width: 1.5),
                      borderRadius: BorderRadius.circular(14),
                      color: Colors.grey.shade50,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            border: Border(bottom: BorderSide(color: Colors.grey.shade300)),
                          ),
                          child: quill.QuillSimpleToolbar(
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
                          ),
                        ),
                        Container(
                          height: 200,
                          padding: const EdgeInsets.all(16),
                          child: quill.QuillEditor.basic(
                            controller: _ringkasanQuillController,
                            configurations: const quill.QuillEditorConfigurations(
                              padding: EdgeInsets.zero,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              Padding("""

c = c.replace(old, new)
with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(c)

