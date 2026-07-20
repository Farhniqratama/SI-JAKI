import 'package:flutter/material.dart';
import 'package:flutter_quill/flutter_quill.dart' as quill;
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../models/laporan_model.dart';
import '../theme/sijaki_theme.dart';
import '../providers/data_provider.dart';
import 'package:dropdown_search/dropdown_search.dart';
import '../models/pt_model.dart';
import 'package:file_picker/file_picker.dart';

class LaporanFormScreen extends StatefulWidget {
  final String type; // 'PTN' or 'PTS'
  final Laporan? laporan; // Null if new, non-null if edit

  const LaporanFormScreen({
    super.key,
    required this.type,
    this.laporan,
  });

  @override
  State<LaporanFormScreen> createState() => _LaporanFormScreenState();
}

class _LaporanFormScreenState extends State<LaporanFormScreen> {
  final _formKey = GlobalKey<FormState>();
  
  late TextEditingController _tempatController;
  late TextEditingController _pembuatController;
  late TextEditingController _ringkasanController;
  late quill.QuillController _ringkasanQuillController;
  
  String _selectedPtUuid = '';
  late DateTime _selectedDate;
  late String _selectedStatus;
  String _selectedJenis = 'Rapat/Audiensi';
  
  List<String> _selectedLingkupList = [];
  String _undanganFileName = '';
  String _notulaFileName = '';

  final List<String> _jenisOptions = [
    'Rapat/Audiensi',
    'Visitasi',
    'Monitoring & Evaluasi',
    'Aduan/Laporan',
    'Teguran/Sanksi'
  ];

  final List<String> _lingkupOptions = [
    'Kelembagaan dan Kemitraan',
    'Kepala Bagian Umum',
    'Kepala LLDIKTI',
    'Sistem Informasi dan PDDikti',
    'Hubungan Masyarakat dan Kerja Sama',
    'Hukum, Kepegawaian, dan Tata Laksana',
    'Riset dan Pengabdian Masyarakat',
    'Pembelajaran, Kemahasiswaan, dan Prestasi',
    'Penjaminan Mutu',
    'Sumber Daya',
    'Anti Dosa Pendidikan dan Integritas Akademik',
    'Perencanaan dan Keuangan',
    'Tata Usaha dan Barang Milik Negara'
  ];

  @override
  void initState() {
    super.initState();
    final isEdit = widget.laporan != null;
    
    // Prefill form values
    _tempatController = TextEditingController(text: isEdit ? widget.laporan!.tempatKegiatan : '');
    _pembuatController = TextEditingController(text: isEdit ? widget.laporan!.pembuatLaporan : '');
    _ringkasanController = TextEditingController(text: isEdit ? widget.laporan!.ringkasanKegiatan : '');
    
    _ringkasanQuillController = quill.QuillController.basic();
    if (isEdit && widget.laporan!.ringkasanKegiatan.isNotEmpty) {
      _ringkasanQuillController.document.insert(0, widget.laporan!.ringkasanKegiatan);
    }
    
    _selectedDate = isEdit ? widget.laporan!.date : DateTime.now();
    _selectedStatus = isEdit ? widget.laporan!.status : 'Draft';
    _selectedJenis = isEdit ? widget.laporan!.jenisKegiatan : 'Rapat/Audiensi';
    
    // Parse lingkup tim kerja
    if (isEdit && widget.laporan!.lingkupTimKerja.isNotEmpty) {
      _selectedLingkupList = widget.laporan!.lingkupTimKerja
          .split(',')
          .map((e) => e.trim())
          .where((e) => e.isNotEmpty)
          .toList();
    } else {
      _selectedLingkupList = ['Kelembagaan dan Kemitraan'];
    }

    _undanganFileName = isEdit ? widget.laporan!.undanganUrl : '';
    _notulaFileName = isEdit ? widget.laporan!.notulaUrl : '';
    
    // Pre-select college
    final pts = Provider.of<DataProvider>(context, listen: false).pts.where((pt) => pt.type == widget.type).toList();
    if (isEdit) {
      _selectedPtUuid = widget.laporan!.ptUuid;
    } else {
      _selectedPtUuid = pts.isNotEmpty ? pts.first.uuid : '';
    }
  }

  @override
  void dispose() {
    _tempatController.dispose();
    _pembuatController.dispose();
    _ringkasanController.dispose();
    _ringkasanQuillController.dispose();
    super.dispose();
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2025),
      lastDate: DateTime(2030),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: SijakiTheme.primary,
              onPrimary: Colors.white,
              onSurface: Colors.black,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  void _showMultiSelectLingkup() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return DraggableScrollableSheet(
              initialChildSize: 0.7,
              maxChildSize: 0.9,
              minChildSize: 0.5,
              expand: false,
              builder: (context, scrollController) {
                return Column(
                  children: [
                    Container(
                      margin: const EdgeInsets.symmetric(vertical: 12),
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            'Pilih Lingkup Tim Kerja',
                            style: GoogleFonts.outfit(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xFF0F172A),
                            ),
                          ),
                          TextButton(
                            onPressed: () {
                              setModalState(() {
                                _selectedLingkupList.clear();
                              });
                              setState(() {});
                            },
                            child: const Text('Reset', style: TextStyle(color: SijakiTheme.primary)),
                          ),
                        ],
                      ),
                    ),
                    const Divider(),
                    Expanded(
                      child: ListView.builder(
                        controller: scrollController,
                        itemCount: _lingkupOptions.length,
                        itemBuilder: (context, index) {
                          final option = _lingkupOptions[index];
                          final isSelected = _selectedLingkupList.contains(option);
                          return CheckboxListTile(
                            activeColor: SijakiTheme.primary,
                            title: Text(
                              option,
                              style: GoogleFonts.inter(
                                fontSize: 13,
                                fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                                color: isSelected ? SijakiTheme.primary : const Color(0xFF334155),
                              ),
                            ),
                            value: isSelected,
                            onChanged: (checked) {
                              setModalState(() {
                                if (checked == true) {
                                  _selectedLingkupList.add(option);
                                } else {
                                  _selectedLingkupList.remove(option);
                                }
                              });
                              setState(() {});
                            },
                          );
                        },
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: SijakiTheme.primary,
                          minimumSize: const Size.fromHeight(50),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(24),
                          ),
                        ),
                        onPressed: () => Navigator.pop(context),
                        child: Text(
                          'Selesai (${_selectedLingkupList.length} Terpilih)',
                          style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ),
                  ],
                );
              },
            );
          },
        );
      },
    );
  }

  void _saveForm() {
    if (_formKey.currentState!.validate()) {
      if (_selectedLingkupList.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pilih minimal satu Lingkup Tim Kerja.'), backgroundColor: SijakiTheme.error),
        );
        return;
      }
      if (_undanganFileName.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Dokumen Undangan wajib diunggah.'), backgroundColor: SijakiTheme.error),
        );
        return;
      }
      if (_notulaFileName.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Dokumen Notula wajib diunggah.'), backgroundColor: SijakiTheme.error),
        );
        return;
      }

      final dataProvider = Provider.of<DataProvider>(context, listen: false);
      final pts = dataProvider.pts;
      final selectedPt = pts.firstWhere((pt) => pt.uuid == _selectedPtUuid);

      final isEdit = widget.laporan != null;
      final lingkupString = _selectedLingkupList.join(', ');

      if (isEdit) {
        final updated = widget.laporan!.copyWith(
          ptUuid: selectedPt.uuid,
          ptName: selectedPt.name,
          activityName: _selectedJenis,
          date: _selectedDate,
          description: _ringkasanQuillController.document.toPlainText().trim(),
          status: _selectedStatus,
          jenisKegiatan: _selectedJenis,
          tempatKegiatan: _tempatController.text.trim(),
          pembuatLaporan: _pembuatController.text.trim(),
          ringkasanKegiatan: _ringkasanQuillController.document.toPlainText().trim(),
          lingkupTimKerja: lingkupString,
          undanganUrl: _undanganFileName,
          notulaUrl: _notulaFileName,
        );
        dataProvider.updateLaporan(updated);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Laporan berhasil diperbarui!')),
        );
      } else {
        final newLaporan = Laporan(
          uuid: 'lap-${DateTime.now().millisecondsSinceEpoch}',
          ptUuid: selectedPt.uuid,
          ptName: selectedPt.name,
          type: widget.type,
          activityName: _selectedJenis,
          date: _selectedDate,
          description: _ringkasanQuillController.document.toPlainText().trim(),
          status: _selectedStatus,
          jenisKegiatan: _selectedJenis,
          tempatKegiatan: _tempatController.text.trim(),
          pembuatLaporan: _pembuatController.text.trim(),
          ringkasanKegiatan: _ringkasanQuillController.document.toPlainText().trim(),
          lingkupTimKerja: lingkupString,
          undanganUrl: _undanganFileName,
          notulaUrl: _notulaFileName,
        );
        dataProvider.addLaporan(newLaporan);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Laporan berhasil ditambahkan!')),
        );
      }

      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final dataProvider = Provider.of<DataProvider>(context);
    final isEdit = widget.laporan != null;
    final filteredPts = dataProvider.pts.where((pt) => pt.type == widget.type).toList();

    return Scaffold(
      appBar: AppBar(
        iconTheme: const IconThemeData(color: Colors.white),
        title: Text(
          isEdit ? 'Ubah Laporan PT' : 'Buat Laporan PT',
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: SijakiTheme.primaryGradient,
          ),
        ),
      ),
      body: Container(
        color: Colors.white,
        child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20.0),
          child: Card(
            elevation: 2,
            shadowColor: Colors.black12,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'Menu Utama › ${widget.type} › ${isEdit ? 'Edit Laporan PT' : 'Buat Laporan PT'}',
                style: GoogleFonts.inter(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: SijakiTheme.primary,
                ),
              ),
              const SizedBox(height: 16),

              Text(
                isEdit ? 'Form Ubah Laporan PT' : 'Form Buat Laporan untuk Perguruan Tinggi',
                style: Theme.of(context).textTheme.titleLarge,
              ),
              const SizedBox(height: 20),

              // Dropdown Perguruan Tinggi
              DropdownSearch<PerguruanTinggi>(
                popupProps: const PopupProps.menu(
                  showSearchBox: true,
                  searchFieldProps: TextFieldProps(
                    decoration: InputDecoration(
                      labelText: 'Cari Perguruan Tinggi...',
                      prefixIcon: Icon(Icons.search),
                    ),
                  ),
                ),
                items: filteredPts,
                itemAsString: (PerguruanTinggi u) => u.name,
                dropdownDecoratorProps: const DropDownDecoratorProps(
                  dropdownSearchDecoration: InputDecoration(
                    labelText: "Pilih Perguruan Tinggi *",
                    prefixIcon: Icon(Icons.school_outlined),
                  ),
                ),
                onChanged: (PerguruanTinggi? data) {
                  if (data != null) {
                    setState(() {
                      _selectedPtUuid = data.uuid;
                    });
                  }
                },
                selectedItem: _selectedPtUuid.isEmpty 
                    ? null 
                    : filteredPts.firstWhere((pt) => pt.uuid == _selectedPtUuid, orElse: () => filteredPts.first),
                validator: (PerguruanTinggi? value) {
                  if (value == null && _selectedPtUuid.isEmpty) {
                    return 'Perguruan Tinggi wajib dipilih';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Jenis Kegiatan
              DropdownSearch<String>(
                popupProps: const PopupProps.menu(
                  showSearchBox: true,
                  searchFieldProps: TextFieldProps(
                    decoration: InputDecoration(
                      labelText: 'Cari Jenis Kegiatan...',
                      prefixIcon: Icon(Icons.search),
                    ),
                  ),
                ),
                items: _jenisOptions,
                dropdownDecoratorProps: const DropDownDecoratorProps(
                  dropdownSearchDecoration: InputDecoration(
                    labelText: "Jenis Kegiatan *",
                    prefixIcon: Icon(Icons.category_outlined),
                  ),
                ),
                onChanged: (String? data) {
                  if (data != null) {
                    setState(() {
                      _selectedJenis = data;
                    });
                  }
                },
                selectedItem: _selectedJenis,
                validator: (String? value) => value == null ? 'Jenis Kegiatan wajib dipilih' : null,
              ),
              const SizedBox(height: 16),

              // Tanggal Kegiatan Picker
              InkWell(
                onTap: () => _selectDate(context),
                child: InputDecorator(
                  decoration: const InputDecoration(
                    labelText: 'Tanggal Kegiatan *',
                    prefixIcon: Icon(Icons.calendar_today_outlined),
                  ),
                  child: Text(
                    DateFormat('dd MMMM yyyy', 'id').format(_selectedDate),
                    style: const TextStyle(fontSize: 14),
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Tempat Kegiatan (Maks 50 karakter)
              TextFormField(
                controller: _tempatController,
                maxLength: 50,
                decoration: const InputDecoration(
                  labelText: 'Tempat Kegiatan *',
                  prefixIcon: Icon(Icons.place_outlined),
                  counterText: "", // Hide default counter to make customized design
                ),
                buildCounter: (context, {required currentLength, required isFocused, maxLength}) {
                  return Text(
                    '$currentLength/$maxLength karakter',
                    style: const TextStyle(fontSize: 11, color: Colors.grey),
                  );
                },
                validator: (value) => value == null || value.trim().isEmpty ? 'Tempat Kegiatan wajib diisi' : null,
              ),
              const SizedBox(height: 16),

              // Dokumen Undangan (max 2MB)
              _buildFileUploadWidget(
                title: 'Dokumen Undangan *',
                subtitle: 'Format: PDF, DOC, DOCX, maks. 2MB',
                fileName: _undanganFileName,
                onUpload: () async {
                  FilePickerResult? result = await FilePicker.pickFiles(
                    type: FileType.custom,
                    allowedExtensions: ['pdf', 'doc', 'docx'],
                  );
                  if (result != null) {
                    setState(() {
                      _undanganFileName = result.files.single.name;
                    });
                  }
                },
              ),
              const SizedBox(height: 16),

              // Dokumen Notula (max 10MB)
              _buildFileUploadWidget(
                title: 'Dokumen Notula *',
                subtitle: 'Format: PDF, DOC, DOCX, maks. 10MB',
                fileName: _notulaFileName,
                onUpload: () async {
                  FilePickerResult? result = await FilePicker.pickFiles(
                    type: FileType.custom,
                    allowedExtensions: ['pdf', 'doc', 'docx'],
                  );
                  if (result != null) {
                    setState(() {
                      _notulaFileName = result.files.single.name;
                    });
                  }
                },
              ),
              const SizedBox(height: 16),

              // Pembuat Laporan (Maks 35 Karakter)
              TextFormField(
                controller: _pembuatController,
                maxLength: 35,
                decoration: const InputDecoration(
                  labelText: 'Pembuat Laporan *',
                  prefixIcon: Icon(Icons.person_outline),
                  counterText: "",
                ),
                buildCounter: (context, {required currentLength, required isFocused, maxLength}) {
                  return Text(
                    '$currentLength/$maxLength karakter',
                    style: const TextStyle(fontSize: 11, color: Colors.grey),
                  );
                },
                validator: (value) => value == null || value.trim().isEmpty ? 'Pembuat Laporan wajib diisi' : null,
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
                            configurations: quill.QuillSimpleToolbarConfigurations(
                              controller: _ringkasanQuillController,
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
                            configurations: quill.QuillEditorConfigurations(
                              controller: _ringkasanQuillController,
                              padding: EdgeInsets.zero,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              Padding(
                padding: const EdgeInsets.only(top: 4.0, bottom: 16.0),
                child: Text(
                  'Gunakan bold, italic, ordered list, dan unordered list untuk format teks',
                  style: GoogleFonts.inter(
                    fontSize: 10,
                    fontStyle: FontStyle.italic,
                    color: Colors.grey.shade500,
                  ),
                ),
              ),

              // Lingkup Tim Kerja Selector (Multi-select)
              InkWell(
                onTap: _showMultiSelectLingkup,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade50,
                    border: Border.all(color: Colors.grey.shade200, width: 1.5),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Lingkup Tim Kerja *',
                        style: GoogleFonts.inter(fontSize: 11, color: Colors.grey.shade600),
                      ),
                      const SizedBox(height: 8),
                      _selectedLingkupList.isEmpty
                          ? Row(
                              children: [
                                const Icon(Icons.group_outlined, color: Colors.grey, size: 20),
                                const SizedBox(width: 8),
                                Text(
                                  'Pilih Lingkup Tim Kerja...',
                                  style: GoogleFonts.inter(color: Colors.grey.shade500, fontSize: 13),
                                ),
                              ],
                            )
                          : Wrap(
                              spacing: 8,
                              runSpacing: 4,
                              children: _selectedLingkupList.map((tag) {
                                return Chip(
                                  label: Text(tag, style: const TextStyle(fontSize: 10, color: SijakiTheme.primary)),
                                  backgroundColor: SijakiTheme.primary.withOpacity(0.08),
                                  side: BorderSide.none,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                  padding: EdgeInsets.zero,
                                );
                              }).toList(),
                            ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 32),

              // Submit Button in Brand Blue
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  backgroundColor: SijakiTheme.primary,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(28),
                  ),
                ),
                onPressed: _saveForm,
                child: Text(
                  isEdit ? 'Simpan Perubahan Laporan' : 'Kirim Laporan Baru',
                  style: GoogleFonts.outfit(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
              ),
              const SizedBox(height: 24),
              ],
              ),
            ),
          ),
        ),
      ),
      ),
    );
  }

  Widget _buildFileUploadWidget({
    required String title,
    required String subtitle,
    required String fileName,
    required VoidCallback onUpload,
  }) {
    final bool hasFile = fileName.isNotEmpty;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        border: Border.all(color: Colors.grey.shade200),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          Icon(
            Icons.picture_as_pdf_outlined,
            color: hasFile ? SijakiTheme.primary : Colors.grey,
            size: 28,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.outfit(
                    fontSize: 13,
                    fontWeight: FontWeight.bold,
                    color: const Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  hasFile ? fileName : subtitle,
                  style: GoogleFonts.inter(
                    fontSize: 11,
                    color: hasFile ? SijakiTheme.primary : Colors.grey.shade500,
                  ),
                ),
              ],
            ),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: hasFile ? Colors.grey.shade200 : SijakiTheme.primary.withOpacity(0.1),
              foregroundColor: hasFile ? Colors.black54 : SijakiTheme.primary,
              elevation: 0,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            onPressed: onUpload,
            child: Text(
              hasFile ? 'Ganti' : 'Unggah',
              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }
}
