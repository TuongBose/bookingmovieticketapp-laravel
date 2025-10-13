class Seat {
  final int id;
  final int roomId;
  final String seatNumber;

  Seat({
    required this.id,
    required this.roomId,
    required this.seatNumber,
  });

  factory Seat.fromJson(Map<String, dynamic> json) => Seat(
    id: json['id'],
    roomId: json['roomId'],
    seatNumber: json['seatnumber'],
  );
}
