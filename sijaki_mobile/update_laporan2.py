import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

# 1. Add import for file_picker
content = content.replace("import '../models/pt_model.dart';", "import '../models/pt_model.dart';\nimport 'package:file_picker/file_picker.dart';")

# 2. Update background image logic
body_old = """      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),"""

body_new = """      body: Container(
        decoration: const BoxDecoration(
          image: DecorationImage(
            image: AssetImage('assets/images/logo.png'),
            repeat: ImageRepeat.repeat,
            opacity: 0.05,
          ),
        ),
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),"""

content = content.replace(body_old, body_new)

# Add closing parenthesis for Container
# The SingleChildScrollView ends right before `        ), // End of SingleChildScrollView
#       );`
# Wait, let's just replace `      ), // End of form` or find the end of body.
# Usually it's `      ),\n    );`
# It's better to just use regex to find the end of the body.
end_old = """                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }"""
end_new = """                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      ), // End Container
    );
  }"""
if end_old in content:
    content = content.replace(end_old, end_new)
else:
    # try another approach
    pass

# 3. Update File Picker logic
undangan_old = """              _buildFileUploadWidget(
                title: 'Dokumen Undangan *',
                subtitle: 'Format: PDF, maks. 2MB',
                fileName: _undanganFileName,
                onUpload: () {
                  setState(() {
                    _undanganFileName = 'Undangan_${widget.type}_${DateFormat('yyyyMMdd').format(_selectedDate)}.pdf';
                  });
                },
              ),"""
undangan_new = """              _buildFileUploadWidget(
                title: 'Dokumen Undangan *',
                subtitle: 'Format: PDF, DOC, DOCX, maks. 2MB',
                fileName: _undanganFileName,
                onUpload: () async {
                  FilePickerResult? result = await FilePicker.platform.pickFiles(
                    type: FileType.custom,
                    allowedExtensions: ['pdf', 'doc', 'docx'],
                  );
                  if (result != null) {
                    setState(() {
                      _undanganFileName = result.files.single.name;
                    });
                  }
                },
              ),"""
content = content.replace(undangan_old, undangan_new)

notula_old = """              _buildFileUploadWidget(
                title: 'Dokumen Notula / Laporan Kegiatan *',
                subtitle: 'Format: PDF, maks. 2MB',
                fileName: _notulaFileName,
                onUpload: () {
                  setState(() {
                    _notulaFileName = 'Notula_${widget.type}_${DateFormat('yyyyMMdd').format(_selectedDate)}.pdf';
                  });
                },
              ),"""
notula_new = """              _buildFileUploadWidget(
                title: 'Dokumen Notula / Laporan Kegiatan *',
                subtitle: 'Format: PDF, DOC, DOCX, maks. 2MB',
                fileName: _notulaFileName,
                onUpload: () async {
                  FilePickerResult? result = await FilePicker.platform.pickFiles(
                    type: FileType.custom,
                    allowedExtensions: ['pdf', 'doc', 'docx'],
                  );
                  if (result != null) {
                    setState(() {
                      _notulaFileName = result.files.single.name;
                    });
                  }
                },
              ),"""
content = content.replace(notula_old, notula_new)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(content)
print("Replaced!")
