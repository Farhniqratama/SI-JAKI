import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

# 1. Add import
if "import 'package:flutter_quill/flutter_quill.dart'" not in content:
    content = content.replace("import 'package:flutter/material.dart';", "import 'package:flutter/material.dart';\nimport 'package:flutter_quill/flutter_quill.dart' as quill;")

# 2. Add controller
if "_ringkasanQuillController" not in content:
    # replace TextEditingController _ringkasanController
    content = content.replace("final _ringkasanController = TextEditingController();", 
        "final _ringkasanController = TextEditingController();\n  final quill.QuillController _ringkasanQuillController = quill.QuillController.basic();")

# 3. Add to initState
init_old = """    _ringkasanController.text = isEdit ? widget.laporan!.ringkasanKegiatan : '';"""
init_new = """    _ringkasanController.text = isEdit ? widget.laporan!.ringkasanKegiatan : '';
    if (isEdit && widget.laporan!.ringkasanKegiatan.isNotEmpty) {
      _ringkasanQuillController.document.insert(0, widget.laporan!.ringkasanKegiatan);
    }"""
content = content.replace(init_old, init_new)

# 4. In build method, replace TextFormField for Ringkasan
text_form_field_old = """              TextFormField(
                controller: _ringkasanController,
                maxLines: 5,
                decoration: const InputDecoration(
                  labelText: 'Ringkasan Kegiatan *',
                  alignLabelWithHint: true,
                ),
                validator: (value) => value == null || value.trim().isEmpty ? 'Wajib diisi' : null,
              ),"""

text_form_field_new = """              Column(
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
              ),"""

content = content.replace(text_form_field_old, text_form_field_new)

# 5. In save method, update ringkasan_kegiatan to get from quill
save_old = """ringkasanKegiatan: _ringkasanController.text.trim(),"""
save_new = """ringkasanKegiatan: _ringkasanQuillController.document.toPlainText().trim(),"""
content = content.replace(save_old, save_new)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(content)

print("Updated laporan_form_screen.dart")

