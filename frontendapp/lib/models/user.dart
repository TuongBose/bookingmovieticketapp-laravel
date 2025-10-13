class User {
  final int? id;
  final String name;
  final String email;
  final String password;
  final String phoneNumber;
  final String? address;
  final DateTime dateOfBirth;
  final DateTime createdAt;
  final bool isActive;
  final bool roleName;
  final String? imageName;

  User({
    this.id,
    required this.name,
    required this.email,
    required this.password,
    required this.phoneNumber,
    this.address,
    required this.dateOfBirth,
    required this.createdAt,
    required this.isActive,
    required this.roleName,
    this.imageName,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
    id: json['id'],
    name: json['name'],
    email: json['email'],
    password: json['password'],
    phoneNumber: json['phonenumber'],
    address: json['address'],
    dateOfBirth: DateTime.parse(json['dateofbirth']),
    createdAt: DateTime.parse(json['createdat']),
    isActive: json['isactive'],
    roleName: json['rolename'],
    imageName: json['imagename'],
  );
}
