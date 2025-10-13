class Showtime {
  final int id;
  final int movieId;
  final int roomId;
  final DateTime showDate;
  final DateTime startTime;
  final int price;
  final bool isactive;

  Showtime({
    required this.id,
    required this.movieId,
    required this.roomId,
    required this.showDate,
    required this.startTime,
    required this.price,
    required this.isactive,
  });

  factory Showtime.fromJson(Map<String, dynamic> json) => Showtime(
    id: json['id'],
    movieId: json['movieId'],
    roomId: json['roomId'],
    showDate: DateTime.parse(json['showdate']),
    startTime: DateTime.parse(json['starttime']),
    price: json['price'],
    isactive: json['isactive'],
  );
} 
