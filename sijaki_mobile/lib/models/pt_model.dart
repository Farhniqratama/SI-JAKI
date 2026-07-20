class PerguruanTinggi {
  final String uuid;
  final String name;
  final String npsn;
  final String type; // 'PTS' or 'PTN'
  final String address;
  final String status; // 'Aktif', 'Pembinaan', 'Tutup'
  final String accreditation; // 'A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik'
  final int lecturersCount;
  final int studentsCount;
  final String logoUrl;
  final String website;

  PerguruanTinggi({
    required this.uuid,
    required this.name,
    required this.npsn,
    required this.type,
    required this.address,
    required this.status,
    required this.accreditation,
    required this.lecturersCount,
    required this.studentsCount,
    required this.logoUrl,
    required this.website,
  });

  factory PerguruanTinggi.fromJson(Map<String, dynamic> json) {
    return PerguruanTinggi(
      uuid: json['uuid'] ?? '',
      name: json['name'] ?? '',
      npsn: json['npsn'] ?? '',
      type: json['type'] ?? 'PTS',
      address: json['address'] ?? '',
      status: json['status'] ?? 'Aktif',
      accreditation: json['accreditation'] ?? 'Baik',
      lecturersCount: json['lecturers_count'] ?? 0,
      studentsCount: json['students_count'] ?? 0,
      logoUrl: json['logo_url'] ?? '',
      website: json['website'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'uuid': uuid,
      'name': name,
      'npsn': npsn,
      'type': type,
      'address': address,
      'status': status,
      'accreditation': accreditation,
      'lecturers_count': lecturersCount,
      'students_count': studentsCount,
      'logo_url': logoUrl,
      'website': website,
    };
  }
}
