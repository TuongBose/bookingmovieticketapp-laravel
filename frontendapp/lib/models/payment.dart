class Payment {
  final int? id;
  final int bookingId;
  final double totalPrice;
  final String paymentMethod;
  final String paymentStatus;
  final String paymentTime;

  Payment({
    this.id,
    required this.bookingId,
    required this.totalPrice,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.paymentTime,
  });

  factory Payment.fromMap(Map<String, dynamic> map) => Payment(
    id: map['ID'],
    bookingId: map['BOOKINGID'],
    totalPrice: map['TOTALPRICE'].toDouble(),
    paymentMethod: map['PAYMENTMETHOD'],
    paymentStatus: map['PAYMENTSTATUS'],
    paymentTime: map['PAYMENTTIME'],
  );

  Map<String, dynamic> toMap() => {
    'ID': id,
    'BOOKINGID': bookingId,
    'TOTALPRICE': totalPrice,
    'PAYMENTMETHOD': paymentMethod,
    'PAYMENTSTATUS': paymentStatus,
    'PAYMENTTIME': paymentTime,
  };
}
