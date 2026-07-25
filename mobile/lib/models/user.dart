class MobileUser {
  const MobileUser({
    required this.id,
    required this.name,
    required this.email,
    this.role,
  });

  final int id;
  final String name;
  final String email;
  final String? role;

  factory MobileUser.fromJson(Map<String, dynamic> json) {
    return MobileUser(
      id: _asInt(json['id']),
      name: json['name']?.toString() ?? 'Technician',
      email: json['email']?.toString() ?? '',
      role: json['role']?.toString(),
    );
  }
}

int _asInt(dynamic value) {
  if (value is int) return value;
  return int.tryParse(value?.toString() ?? '') ?? 0;
}
