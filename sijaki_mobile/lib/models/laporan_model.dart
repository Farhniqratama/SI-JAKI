class Laporan {
  final String uuid;
  final String ptUuid;
  final String ptName;
  final String type; // 'PTS' or 'PTN'
  final String activityName;
  final DateTime date;
  final String description;
  final String status; // 'Draft', 'Submitted', 'Approved', 'Rejected'
  final String undanganUrl;
  final String notulaUrl;
  
  // New Sijaki Form Fields
  final String jenisKegiatan;
  final String tempatKegiatan;
  final String pembuatLaporan;
  final String ringkasanKegiatan;
  final String lingkupTimKerja;

  Laporan({
    required this.uuid,
    required this.ptUuid,
    required this.ptName,
    required this.type,
    required this.activityName,
    required this.date,
    required this.description,
    required this.status,
    required this.undanganUrl,
    required this.notulaUrl,
    required this.jenisKegiatan,
    required this.tempatKegiatan,
    required this.pembuatLaporan,
    required this.ringkasanKegiatan,
    required this.lingkupTimKerja,
  });

  Laporan copyWith({
    String? uuid,
    String? ptUuid,
    String? ptName,
    String? type,
    String? activityName,
    DateTime? date,
    String? description,
    String? status,
    String? undanganUrl,
    String? notulaUrl,
    String? jenisKegiatan,
    String? tempatKegiatan,
    String? pembuatLaporan,
    String? ringkasanKegiatan,
    String? lingkupTimKerja,
  }) {
    return Laporan(
      uuid: uuid ?? this.uuid,
      ptUuid: ptUuid ?? this.ptUuid,
      ptName: ptName ?? this.ptName,
      type: type ?? this.type,
      activityName: activityName ?? this.activityName,
      date: date ?? this.date,
      description: description ?? this.description,
      status: status ?? this.status,
      undanganUrl: undanganUrl ?? this.undanganUrl,
      notulaUrl: notulaUrl ?? this.notulaUrl,
      jenisKegiatan: jenisKegiatan ?? this.jenisKegiatan,
      tempatKegiatan: tempatKegiatan ?? this.tempatKegiatan,
      pembuatLaporan: pembuatLaporan ?? this.pembuatLaporan,
      ringkasanKegiatan: ringkasanKegiatan ?? this.ringkasanKegiatan,
      lingkupTimKerja: lingkupTimKerja ?? this.lingkupTimKerja,
    );
  }

  factory Laporan.fromJson(Map<String, dynamic> json) {
    return Laporan(
      uuid: json['uuid'] ?? '',
      ptUuid: json['pt_uuid'] ?? '',
      ptName: json['pt_name'] ?? '',
      type: json['type'] ?? 'PTS',
      activityName: json['activity_name'] ?? '',
      date: json['date'] != null ? DateTime.parse(json['date']) : DateTime.now(),
      description: json['description'] ?? '',
      status: json['status'] ?? 'Draft',
      undanganUrl: json['undangan_url'] ?? '',
      notulaUrl: json['notula_url'] ?? '',
      jenisKegiatan: json['jenis_kegiatan'] ?? '',
      tempatKegiatan: json['tempat_kegiatan'] ?? '',
      pembuatLaporan: json['pembuat_laporan'] ?? '',
      ringkasanKegiatan: json['ringkasan_kegiatan'] ?? '',
      lingkupTimKerja: json['lingkup_tim_kerja'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'uuid': uuid,
      'pt_uuid': ptUuid,
      'pt_name': ptName,
      'type': type,
      'activity_name': activityName,
      'date': date.toIso8601String(),
      'description': description,
      'status': status,
      'undangan_url': undanganUrl,
      'notula_url': notulaUrl,
      'jenis_kegiatan': jenisKegiatan,
      'tempat_kegiatan': tempatKegiatan,
      'pembuat_laporan': pembuatLaporan,
      'ringkasan_kegiatan': ringkasanKegiatan,
      'lingkup_tim_kerja': lingkupTimKerja,
    };
  }
}
