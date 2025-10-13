import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../dtos/BookingDTO.dart';
import '../dtos/BookingDetailDTO.dart';
import '../services/BookingService.dart';
import '../services/BookingDetailService.dart';
import '../app_config.dart';

class PaymentWebView extends StatefulWidget {
  final String paymentUrl;
  final String paymentMethod;
  final int showTimeId;
  final int totalPrice;
  final List<Map<String, dynamic>> selectedSeats;

  const PaymentWebView({
    Key? key,
    required this.paymentUrl,
    required this.paymentMethod,
    required this.showTimeId,
    required this.totalPrice,
    required this.selectedSeats,
  }) : super(key: key);

  @override
  State<PaymentWebView> createState() => _PaymentWebViewState();
}

class _PaymentWebViewState extends State<PaymentWebView> {
  late final WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _initializeWebView();
  }

  void _initializeWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            if (url.contains('movieticketapp://vnpay_return')) {
              _handleVNPayCallback(Uri.parse(url));
            }
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.paymentUrl));
  }

  Future<void> _handleVNPayCallback(Uri uri) async {
    final vnpResponseCode = uri.queryParameters['vnp_ResponseCode'];

    if (vnpResponseCode == '00') { // Success code from VNPay
      try {
        final bookingService = BookingService();
        final bookingDTO = BookingDTO(
          userId: AppConfig.currentUser!.id!,
          showtimeId: widget.showTimeId,
          totalPrice: widget.totalPrice.toDouble(),
          paymentMethod: widget.paymentMethod,
          paymentStatus: 'COMPLETED',
        );

        final bookingId = await bookingService.createBooking(bookingDTO);
        final bookingDetailService = BookingDetailService();
        final pricePerSeat = widget.totalPrice ~/ widget.selectedSeats.length;

        for (var seat in widget.selectedSeats) {
          final bookingDetailDTO = BookingDetailDTO(
            bookingId: bookingId,
            seatId: seat['id'],
            price: pricePerSeat,
          );
          await bookingDetailService.createBookingDetail(bookingDetailDTO);
        }

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Thanh toán VNPay thành công!')),
          );
          Navigator.of(context).pushNamedAndRemoveUntil('/default', (route) => false);
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Lỗi khi xử lý thanh toán: $e')),
          );
          Navigator.of(context).pop();
        }
      }
    } else {
      String errorMessage = 'Thanh toán thất bại';
      switch (vnpResponseCode) {
        case '07':
          errorMessage = 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).';
          break;
        case '09':
          errorMessage = 'Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.';
          break;
        case '10':
          errorMessage = 'Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần';
          break;
        case '11':
          errorMessage = 'Đã hết hạn chờ thanh toán';
          break;
        case '12':
          errorMessage = 'Thẻ/Tài khoản của khách hàng bị khóa.';
          break;
        case '24':
          errorMessage = 'Khách hàng hủy giao dịch';
          break;
        default:
          errorMessage = 'Lỗi thanh toán. Vui lòng thử lại sau.';
      }

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(errorMessage)),
        );
        Navigator.of(context).pop();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        final shouldPop = await showDialog<bool>(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text('Hủy thanh toán?'),
            content: const Text('Bạn có chắc muốn hủy thanh toán không?'),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Không'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, true),
                child: const Text('Có'),
              ),
            ],
          ),
        );
        return shouldPop ?? false;
      },
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Thanh toán VNPay'),
          leading: IconButton(
            icon: const Icon(Icons.close),
            onPressed: () async {
              final shouldClose = await showDialog<bool>(
                context: context,
                builder: (context) => AlertDialog(
                  title: const Text('Hủy thanh toán?'),
                  content: const Text('Bạn có chắc muốn hủy thanh toán không?'),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(context, false),
                      child: const Text('Không'),
                    ),
                    TextButton(
                      onPressed: () => Navigator.pop(context, true),
                      child: const Text('Có'),
                    ),
                  ],
                ),
              );
              if (shouldClose == true && mounted) {
                Navigator.of(context).pop();
              }
            },
          ),
        ),
        body: Stack(
          children: [
            WebViewWidget(controller: _controller),
            if (_isLoading)
              const Center(
                child: CircularProgressIndicator(),
              ),
          ],
        ),
      ),
    );
  }
}