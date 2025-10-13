class BookingDTO{
  final int userId;
  final int showtimeId;
  final double totalPrice;
  final String paymentMethod;
  final String paymentStatus;

  BookingDTO({
    required this.userId,
    required this.showtimeId,
    required this.totalPrice,
    required this.paymentMethod,
    required this.paymentStatus,
  });

  Map<String, dynamic> toJson() {
    return {
      'userid': userId,
      'showtimeid': showtimeId,
      'totalprice': totalPrice,
      'paymentmethod': paymentMethod,
      'paymentstatus': paymentStatus,
    };
  }
}