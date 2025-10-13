class BookingDetail {
  final int? id;
  final int bookingId;
  final int seatId;
  final double price;

  BookingDetail({
    this.id,
    required this.bookingId,
    required this.seatId,
    required this.price,
  });

  factory BookingDetail.fromJson(Map<String, dynamic> json) => BookingDetail(
    id: json['id'],
    bookingId: json['bookingId'],
    seatId: json['seatId'],
    price: json['price'].toDouble(),
  );
}
