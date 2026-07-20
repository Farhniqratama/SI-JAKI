class User {
  final String uuid;
  final String name;
  final String email;
  final String role; // 'Dev', 'Admin', 'User'
  final String avatarUrl;

  User({
    required this.uuid,
    required this.name,
    required this.email,
    required this.role,
    required this.avatarUrl,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      uuid: json['uuid'] ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'User',
      avatarUrl: json['avatar_url'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'uuid': uuid,
      'name': name,
      'email': email,
      'role': role,
      'avatar_url': avatarUrl,
    };
  }
}
