class Room {
  final int id;
  final int cinemaId;
  final String name;
  final int seatColumnMax;
  final int seatRowMax;

  Room({
    required this.id,
    required this.cinemaId,
    required this.name,
    required this.seatColumnMax,
    required this.seatRowMax,
  });

  factory Room.fromJson(Map<String, dynamic> json)=> Room(
    id: json['id'],
    cinemaId: json['cinemaId'],
    name: json['name'],
    seatColumnMax: json['seatcolumnmax'],
    seatRowMax: json['seatrowmax'],
  );
}
