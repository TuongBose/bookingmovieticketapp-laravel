class BookingDetailDTO {
  final int bookingId;
  final int seatId;
  final int price;

  BookingDetailDTO({
    required this.bookingId,
    required this.seatId,
    required this.price,
  });

  Map<String, dynamic> toJson() {
    return {
      'bookingid': bookingId,
      'seatid': seatId,
      'price': price,
    };
  }
}