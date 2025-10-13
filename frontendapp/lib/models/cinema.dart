class Cinema {
  final int id;
  final String name;
  final String city;
  final String coordinates;
  final String address;
  final String phoneNumber;
  final int maxRoom;
  String? imageName;
  final bool isActive;

  Cinema({
    required this.id,
    required this.name,
    required this.city,
    required this.coordinates,
    required this.address,
    required this.phoneNumber,
    required this.maxRoom,
    this.imageName,
    required this.isActive,
  });

  factory Cinema.fromJson(Map<String, dynamic> json) => Cinema(
    id: json['id'],
    name: json['name'],
    city: json['city'],
    coordinates: json['coordinates'],
    address: json['address'],
    phoneNumber: json['phonenumber'],
    maxRoom: json['maxroom'],
    imageName: json['imagename'],
    isActive: json['isactive'],
  );
}
