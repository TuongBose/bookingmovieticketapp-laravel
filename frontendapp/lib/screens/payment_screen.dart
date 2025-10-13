import 'dart:convert';

import 'package:crypto/crypto.dart';
import 'package:flutter/material.dart';
import 'package:frontendapp/screens/payment_webview.dart';
import 'package:intl/intl.dart';
import '../app_config.dart';
import '../dtos/BookingDTO.dart';
import '../dtos/BookingDetailDTO.dart';
import '../models/cinema.dart';
import '../models/movie.dart';
import '../models/room.dart';
import '../models/seat.dart';
import '../services/BookingService.dart';
import '../services/BookingDetailService.dart';

class PaymentScreen extends StatefulWidget {
  final Movie movie;
  final Cinema cinema;
  final Room room;
  final DateTime showTime;
  final int showTimeId;
  final List<String> selectedSeats;
  final List<Seat> selectedSeatsWithId;
  final int totalPrice;

  const PaymentScreen({
    super.key,
    required this.movie,
    required this.cinema,
    required this.room,
    required this.showTime,
    required this.showTimeId,
    required this.selectedSeats,
    required this.selectedSeatsWithId,
    required this.totalPrice,
  });

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  String? _selectedPaymentMethod;
  int _starsDiscount = 0;
  bool _isLoading = false;

  Future<void> _processPayment() async {
    setState(() {
      _isLoading = true;
    });

    try {
      if (!AppConfig.isLogin) {
        throw Exception('Bạn chưa đăng nhập. Vui lòng đăng nhập để tiếp tục.');
      }
      if (AppConfig.currentUser == null || AppConfig.currentUser!.id == null) {
        throw Exception('Không tìm thấy thông tin người dùng. Vui lòng đăng nhập lại.');
      }

      final totalPriceAfterDiscount = widget.totalPrice - _starsDiscount;

      if (_selectedPaymentMethod == 'vnpay') {
        final vnPayUrl = _generateVNPayUrl(totalPriceAfterDiscount.toDouble());

        if (mounted) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => PaymentWebView(
                paymentUrl: vnPayUrl.toString(),
                paymentMethod: 'vnpay',
                showTimeId: widget.showTimeId,
                totalPrice: totalPriceAfterDiscount,
                selectedSeats: widget.selectedSeatsWithId
                    .map((seat) => {'id': seat.id, 'number': seat.seatNumber})
                    .toList(),
              ),
            ),
          );
        }
        return;
      }

      final bookingService = BookingService();
      final bookingDTO = BookingDTO(
        userId: AppConfig.currentUser!.id!,
        showtimeId: widget.showTimeId,
        totalPrice: widget.totalPrice - _starsDiscount.toDouble(),
        paymentMethod: _selectedPaymentMethod!,
        paymentStatus: 'COMPLETED',
      );

      final bookingId = await bookingService.createBooking(bookingDTO);
      final bookingDetailService = BookingDetailService();
      final pricePerSeat = (widget.totalPrice - _starsDiscount) ~/ widget.selectedSeats.length;

      for (var seat in widget.selectedSeatsWithId) {
        final bookingDetailDTO = BookingDetailDTO(
          bookingId: bookingId,
          seatId: seat.id,
          price: pricePerSeat,
        );
        await bookingDetailService.createBookingDetail(bookingDetailDTO);
      }

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Thanh toán thành công với ${_selectedPaymentMethod}!'),
          ),
        );
        // Kiểm tra xem route '/default' có tồn tại không
        bool routeExists = false;
        Navigator.popUntil(context, (route) {
          if (route.settings.name == '/default') {
            routeExists = true;
            return true;
          }
          return false;
        });
        if (!routeExists) {
          print('Route /default not found in the stack. Pushing to /default...');
          Navigator.pushNamedAndRemoveUntil(
            context,
            '/default',
                (route) => false, // Xóa toàn bộ stack và thay bằng /default
          );
        }
      }
    } catch (e, stackTrace) {
      print('Error during payment: $e');
      print('Stack trace: $stackTrace');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Lỗi khi thanh toán: $e'),
          ),
        );
        // Nếu lỗi do chưa đăng nhập, điều hướng đến màn hình đăng nhập
        if (e.toString().contains('đăng nhập')) {
          print('Redirecting to login screen...');
          Navigator.pushNamed(context, '/dangnhap').then((result) {
            if (result == true && mounted) {
              print('Login successful. Retrying payment...');
              _processPayment();
            }
          });
        }
      }
    } finally {
      if (mounted) {
        print('Setting isLoading to false...');
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  Uri _generateVNPayUrl(double amount) {
    // Replace these with your VNPay test credentials
    const String vnpTmnCode = "OZYHVEZ5"; // Test TMN Code
    const String vnpHashSecret = "8KWHSG7DRHHXBD2FR0IVE60BG1W84G0U"; // Test Hash Secret
    final String vnpTxnRef = DateTime.now().millisecondsSinceEpoch.toString();
    final String vnpOrderInfo = "Thanh toan ve xem phim";
    final String vnpAmount = (amount * 100).toInt().toString();
    final String vnpCommand = "pay";
    final String vnpCreateDate = DateFormat("yyyyMMddHHmmss").format(DateTime.now());
    final String vnpIpAddr = "127.0.0.1";
    final String vnpVersion = "2.1.0";
    final String vnpLocale = "vn";
    final String vnpCurrCode = "VND";
    final String vnpReturnUrl = "movieticketapp://vnpay_return"; // Use deep link URL scheme

    final params = {
      'vnp_Version': vnpVersion,
      'vnp_Command': vnpCommand,
      'vnp_TmnCode': vnpTmnCode,
      'vnp_Locale': vnpLocale,
      'vnp_CurrCode': vnpCurrCode,
      'vnp_TxnRef': vnpTxnRef,
      'vnp_OrderInfo': vnpOrderInfo,
      'vnp_OrderType': 'billpayment',
      'vnp_Amount': vnpAmount,
      'vnp_ReturnUrl': vnpReturnUrl,
      'vnp_IpAddr': vnpIpAddr,
      'vnp_CreateDate': vnpCreateDate,
    };

    final String vnpSecureHash = _generateVNPayHash(params, vnpHashSecret);
    return Uri.https('sandbox.vnpayment.vn', '/paymentv2/vpcpay.html', {
      ...params,
      'vnp_SecureHash': vnpSecureHash,
    });
  }

  String _generateVNPayHash(Map<String, String> params, String secretKey) {
    // Sắp xếp tham số theo thứ tự alphabet
    final sortedParams = Map.fromEntries(
        params.entries.toList()..sort((a, b) => a.key.compareTo(b.key)));

    // Tạo chuỗi hashData
    final hashData = sortedParams.entries
        .map((e) => '${e.key}=${Uri.encodeQueryComponent(e.value)}')
        .join('&');

    // Tạo chữ ký bằng HMAC-SHA512
    final hmacSha512 = Hmac(sha512, utf8.encode(secretKey));
    final hash = hmacSha512.convert(utf8.encode(hashData));

    // Chuyển đổi thành chuỗi hex và in hoa
    final hashString = hash.bytes
        .map((byte) => byte.toRadixString(16).padLeft(2, '0'))
        .join()
        .toUpperCase();

    // Debug
    print('Hash data: $hashData');
    print('Generated hash: $hashString');

    return hashString;
  }

  @override
  Widget build(BuildContext context) {
    final formatter = NumberFormat.currency(locale: 'vi_VN', symbol: 'đ');
    final totalPriceAfterDiscount = widget.totalPrice - _starsDiscount;

    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: const Text(
          'Giao dịch',
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Thông tin phim
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    color: Colors.white,
                    child: Padding(
                      padding: const EdgeInsets.all(12.0),
                      child: Row(
                        children: [
                          ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: Image.network(
                              widget.movie.posterUrl,
                              width: 80,
                              height: 120,
                              fit: BoxFit.cover,
                              errorBuilder: (context, error, stackTrace) {
                                return Container(
                                  width: 80,
                                  height: 120,
                                  color: Colors.grey[300],
                                  child: const Icon(Icons.broken_image),
                                );
                              },
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  widget.movie.name,
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.start,
                                  children: [
                                    const Text('2D PHỤ ĐỀ '),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 6,
                                        vertical: 2,
                                      ),
                                      decoration: BoxDecoration(
                                        color: Colors.redAccent,
                                        borderRadius: BorderRadius.circular(4),
                                      ),
                                      child: Text(
                                        '${widget.movie.ageRating}',
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  '${widget.cinema.name} - ${widget.room.name}',
                                  style: const TextStyle(fontSize: 14),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  DateFormat(
                                    "HH:mm - EEEE, dd/MM/yyyy",
                                    'vi_VN',
                                  ).format(widget.showTime),
                                  style: const TextStyle(
                                      color: Colors.red,
                                      fontSize: 15,
                                      fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.all(10),
                    child: const Text(
                      'Thông tin giao dịch',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    color: Colors.white,
                    child: Padding(
                      padding: const EdgeInsets.all(12.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.start,
                                children: [
                                  Text(
                                    '${widget.selectedSeats.length}x',
                                    style: const TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const Text(' - ', style: TextStyle(fontSize: 30)),
                                  Text(
                                    widget.selectedSeats.join(", "),
                                    style: const TextStyle(fontSize: 15),
                                  ),
                                ],
                              ),
                              Text(
                                formatter.format(widget.totalPrice),
                                style: const TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                          const Divider(height: 24),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                "Khuyến mãi",
                                style: const TextStyle(
                                  color: Colors.orange,
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              TextButton(
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Chức năng khuyến mãi đang phát triển!',
                                      ),
                                    ),
                                  );
                                },
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Text(
                                      "Chọn hoặc nhập mã",
                                      style: TextStyle(color: Colors.grey),
                                    ),
                                    const SizedBox(width: 8),
                                    Icon(
                                      Icons.local_offer,
                                      color: Colors.grey,
                                      size: 20,
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Row(),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.start,
                                children: [
                                  const Text(
                                    'Tổng cộng',
                                    style: TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              ),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    formatter.format(totalPriceAfterDiscount),
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.orange,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.all(10),
                    child: const Text(
                      'Thông tin thanh toán',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    color: Colors.white,
                    child: Padding(
                      padding: const EdgeInsets.all(12.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildPaymentMethodOption(
                            'ShoppePay',
                            'shopeepay',
                            icon: Image.asset(
                              'assets/images/shopeepay_icon.png',
                              width: 30,
                              height: 30,
                              errorBuilder: (context, error, stackTrace) {
                                return const Icon(Icons.account_balance, size: 30);
                              },
                            ),
                          ),
                          _buildPaymentMethodOption(
                            'ZaloPay',
                            'zalopay',
                            icon: Image.asset(
                              'assets/images/zalopay_icon.png',
                              width: 30,
                              height: 30,
                              errorBuilder: (context, error, stackTrace) {
                                return const Icon(Icons.account_balance, size: 30);
                              },
                            ),
                          ),
                          _buildPaymentMethodOption(
                            'Ví Momo',
                            'momo',
                            icon: Image.asset(
                              'assets/images/momo_icon.png',
                              width: 30,
                              height: 30,
                              errorBuilder: (context, error, stackTrace) {
                                return const Icon(Icons.account_balance, size: 30);
                              },
                            ),
                          ),
                          _buildPaymentMethodOption(
                            'VNPay - Thẻ ATM / Internet Banking',
                            'vnpay',
                            icon: Image.asset(
                              'assets/images/vnpay_icon.png',
                              width: 30,
                              height: 30,
                              errorBuilder: (context, error, stackTrace) {
                                return const Icon(Icons.account_balance, size: 30);
                              },
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          // Nút Thanh toán
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'TỔNG CỘNG: ${formatter.format(totalPriceAfterDiscount)}',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                ElevatedButton(
                  onPressed: _selectedPaymentMethod == null
                      ? null
                      : _processPayment,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.orange,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 24,
                      vertical: 12,
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text(
                    'THANH TOÁN',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodOption(String title, String value, {Widget? icon}) {
    return ListTile(
      leading: icon,
      title: Text(title, style: const TextStyle(fontSize: 14)),
      trailing: Radio<String>(
        value: value,
        groupValue: _selectedPaymentMethod,
        onChanged: (String? newValue) {
          setState(() {
            _selectedPaymentMethod = newValue;
          });
        },
      ),
    );
  }
}