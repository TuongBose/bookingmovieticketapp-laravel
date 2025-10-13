import 'package:flutter/material.dart';
import 'package:frontendapp/screens/user_screen.dart';
import 'package:intl/intl.dart';

import '../models/bookingdetail.dart';
import '../models/seat.dart';
import '../services/BookingDetailService.dart';
import '../services/SeatService.dart'; // Nếu bạn lưu BookingDetails trong file riêng

class BookingDetailScreen extends StatelessWidget {
  final BookingDetails bookingDetails;

  const BookingDetailScreen({super.key, required this.bookingDetails});

  @override
  Widget build(BuildContext context) {
    final showtime = bookingDetails.showtime;
    final movie = bookingDetails.movie;
    final room = bookingDetails.room;
    final cinema = bookingDetails.cinema;
    final booking = bookingDetails.booking;
    final isActive = bookingDetails.isActive;

    // Khởi tạo các service
    final BookingDetailService _bookingDetailService = BookingDetailService();
    final SeatService _seatService = SeatService();

    // Định dạng ngày giờ
    final dateFormatter = DateFormat('HH:mm - EEEE, dd/MM/yyyy', 'vi_VN');
    final currencyFormatter = NumberFormat.currency(locale: 'vi_VN', symbol: 'đ');

    // Kiểm tra nếu thiếu thông tin
    if (showtime == null || movie == null || room == null || cinema == null) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Chi tiết vé'),
          backgroundColor: Colors.white,
          foregroundColor: Colors.black,
          elevation: 0,
        ),
        body: const Center(
          child: Text(
            'Không thể tải thông tin vé.',
            style: TextStyle(fontSize: 16, color: Colors.red),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.blue),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: isActive
          ? Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
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
                    // Phần trên: Poster và thông tin phim
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Poster phim
                        ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(
                            movie.posterUrl,
                            width: 80,
                            height: 120,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) {
                              print('Error loading movie poster: $error');
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
                        // Tên phim, định dạng và độ tuổi
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Tên phim
                              Text(
                                movie.name,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 4),
                              // Định dạng và độ tuổi
                              Row(
                                mainAxisAlignment: MainAxisAlignment.start,
                                children: [
                                  const Text(
                                    '2D PHỤ ĐỀ',
                                    style: TextStyle(fontSize: 14),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 6,
                                  vertical: 2,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.red,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  '${movie.ageRating}',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 15),
                    // Phần dưới poster: Tên rạp và thời gian chiếu
                    Text(
                      '${cinema.name} - ${room.name}',
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Suất ${dateFormatter.format(showtime.startTime)}',
                      style: const TextStyle(
                        color: Colors.red,
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    // Đường kẻ ngang
                    const Divider(
                      height: 20,
                      thickness: 1,
                      color: Colors.grey,
                    ),
                    const SizedBox(height: 8),

                    // Xử lý hiển số ghế đã chọn
                    FutureBuilder<List<BookingDetail>>(
                      future: _bookingDetailService.getBookingDetailsByBookingId(booking.id),
                      builder: (context, snapshot) {
                        if (snapshot.connectionState == ConnectionState.waiting) {
                          return const Center(child: CircularProgressIndicator());
                        } else if (snapshot.hasError) {
                          return Text(
                            'Lỗi khi tải ghế: ${snapshot.error}',
                            style: const TextStyle(fontSize: 14, color: Colors.red),
                          );
                        } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                          return const Text(
                            'Không có ghế nào được chọn.',
                            style: TextStyle(fontSize: 14, color: Colors.grey),
                          );
                        }

                        final bookingDetailsList = snapshot.data!;

                        // Lấy danh sách ghế từ seatId
                        return FutureBuilder<List<Seat>>(
                          future: Future.wait(
                            bookingDetailsList.map((detail) => _seatService.getSeatById(detail.seatId)).toList(),
                          ),
                          builder: (context, seatSnapshot) {
                            if (seatSnapshot.connectionState == ConnectionState.waiting) {
                              return const Center(child: CircularProgressIndicator());
                            } else if (seatSnapshot.hasError) {
                              return Text(
                                'Lỗi khi tải thông tin ghế: ${seatSnapshot.error}',
                                style: const TextStyle(fontSize: 14, color: Colors.red),
                              );
                            } else if (!seatSnapshot.hasData || seatSnapshot.data!.isEmpty) {
                              return const Text(
                                'Không có thông tin ghế.',
                                style: TextStyle(fontSize: 14, color: Colors.grey),
                              );
                            }

                            final seats = seatSnapshot.data!;

                            // Hiển thị danh sách ghế (hàng + số ghế)
                            return Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Ghế - ',
                                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                ),
                                Text(
                                  seats.map((seat) => seat.seatNumber).join(', '),
                                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                ),
                              ],
                            );
                          },
                        );
                      },
                    ),

                    const SizedBox(height: 8),
                    // Đường kẻ ngang
                    const Divider(
                      height: 20,
                      thickness: 1,
                      color: Colors.grey,
                    ),
                    const SizedBox(height: 8),
                    // Thông tin bổ sung: Mã vé, Stars, Đã thanh toán
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start, // Đổi từ spaceBetween thành start
                      children: [
                        const SizedBox(width: 25), // Thêm khoảng cách từ rìa trái
                        // Mã vé
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Mã Vé',
                                style: TextStyle(fontSize: 16),
                              ),
                              Text(
                                booking.id.toString(), // Giả sử booking có trường id
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                        // Đã thanh toán
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              const Text(
                                'Đã Thanh Toán',
                                style: TextStyle(fontSize: 16),
                              ),
                              Text(
                                currencyFormatter.format(booking.totalPrice ?? 0), // Giả sử booking có trường totalPrice
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 25), // Thêm khoảng cách từ rìa phải
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const Spacer(),
            // Nút "Đóng"
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.orange,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'Đóng',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                ),
              ),
            ),
          ],
        ),
      )
          :Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
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
                    // Phần trên: Poster và thông tin phim
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Poster phim
                        ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(
                            movie.posterUrl,
                            width: 80,
                            height: 120,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) {
                              print('Error loading movie poster: $error');
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
                        // Tên phim, định dạng và độ tuổi
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Tên phim
                              Text(
                                movie.name,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 4),
                              // Định dạng và độ tuổi
                              Row(
                                mainAxisAlignment: MainAxisAlignment.start,
                                children: [
                                  const Text(
                                    '2D PHỤ ĐỀ',
                                    style: TextStyle(fontSize: 14),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 6,
                                  vertical: 2,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.red,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  '${movie.ageRating}',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 15),
                    // Phần dưới poster: Tên rạp và thời gian chiếu
                    Text(
                      cinema.name,
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Suất ${dateFormatter.format(showtime.startTime)}',
                      style: const TextStyle(
                        color: Colors.red,
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    // Đường kẻ ngang
                    const Divider(
                      height: 20,
                      thickness: 1,
                      color: Colors.grey,
                    ),
                    const SizedBox(height: 8),
                    // Thông tin bổ sung: Mã vé, Stars, Đã thanh toán
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start, // Đổi từ spaceBetween thành start
                      children: [
                        const SizedBox(width: 25), // Thêm khoảng cách từ rìa trái
                        // Mã vé
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Mã Vé',
                                style: TextStyle(fontSize: 16),
                              ),
                              Text(
                                booking.id.toString(), // Giả sử booking có trường id
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                        // Đã thanh toán
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              const Text(
                                'Đã Thanh Toán',
                                style: TextStyle(fontSize: 16),
                              ),
                              Text(
                                currencyFormatter.format(booking.totalPrice ?? 0), // Giả sử booking có trường totalPrice
                                style: const TextStyle(
                                  fontSize: 17,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 25), // Thêm khoảng cách từ rìa phải
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const Spacer(),
            // Nút "Đóng"
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.orange,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'Đóng',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}