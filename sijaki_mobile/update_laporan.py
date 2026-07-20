import re

with open('lib/screens/laporan_form_screen.dart', 'r') as f:
    content = f.read()

# Add import
content = content.replace("import '../providers/data_provider.dart';", "import '../providers/data_provider.dart';\nimport 'package:dropdown_search/dropdown_search.dart';\nimport '../models/pt_model.dart';")

# Replace PT dropdown
pt_old = """              DropdownButtonFormField<String>(
                value: _selectedPtUuid.isEmpty ? null : _selectedPtUuid,
                decoration: const InputDecoration(
                  labelText: 'Pilih Perguruan Tinggi *',
                  prefixIcon: Icon(Icons.school_outlined),
                ),
                items: filteredPts.map((pt) {
                  return DropdownMenuItem<String>(
                    value: pt.uuid,
                    child: Text(
                      pt.name,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12),
                    ),
                  );
                }).toList(),
                onChanged: (value) {
                  setState(() {
                    _selectedPtUuid = value!;
                  });
                },
                validator: (value) => value == null ? 'Perguruan Tinggi wajib dipilih' : null,
              ),"""

pt_new = """              DropdownSearch<PerguruanTinggi>(
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
              ),"""

content = content.replace(pt_old, pt_new)

jenis_old = """              DropdownButtonFormField<String>(
                value: _selectedJenis,
                decoration: const InputDecoration(
                  labelText: 'Jenis Kegiatan *',
                  prefixIcon: Icon(Icons.category_outlined),
                ),
                items: _jenisOptions.map((option) {
                  return DropdownMenuItem<String>(
                    value: option,
                    child: Text(
                      option,
                      style: const TextStyle(fontSize: 12),
                    ),
                  );
                }).toList(),
                onChanged: (value) {
                  setState(() {
                    _selectedJenis = value!;
                  });
                },
                validator: (value) => value == null ? 'Jenis Kegiatan wajib dipilih' : null,
              ),"""

jenis_new = """              DropdownSearch<String>(
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
              ),"""

content = content.replace(jenis_old, jenis_new)

with open('lib/screens/laporan_form_screen.dart', 'w') as f:
    f.write(content)

print("Replaced!")
