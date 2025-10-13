import 'package:flutter/material.dart';
import 'package:frontendapp/screens/cinema_showtime_screen.dart';
import '../models/cinema.dart';
import '../services/CinemaService.dart';

class CinemaScreen extends StatefulWidget {
  const CinemaScreen({super.key});

  @override
  _CinemaScreenState createState() => _CinemaScreenState();
}

class _CinemaScreenState extends State<CinemaScreen> {
  final CinemaService _cinemaService = CinemaService();
  late Future<List<Cinema>> _cinemasFuture;

  @override
  void initState() {
    super.initState();
    _cinemasFuture = _cinemaService.getCinemas();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Rạp phim',
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
        centerTitle: true,
        automaticallyImplyLeading: false,
        surfaceTintColor: Colors.transparent, // Ngăn AppBar đổi màu khi cuộn
      ),
      body: Column(
        children: [
          // Nút "Toàn quốc" được đặt bên dưới tiêu đề và căn giữa
          Padding(
            padding: const EdgeInsets.all(0.0),
            child: TextButton.icon(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Chức năng lọc khu vực đang phát triển!'),
                  ),
                );
              },
              icon: const Icon(Icons.location_on, color: Colors.blue, size: 20),
              label: const Text(
                'Toàn quốc',
                style: TextStyle(color: Colors.blue, fontSize: 14),
              ),
            ),
          ),
          // Đường ngang ngắn phân cách
          Container(
            child: const Divider(height: 40, thickness: 1, color: Colors.grey),
          ),
          // Nội dung danh sách rạp chiếu phim
          Expanded(
            child: FutureBuilder<List<Cinema>>(
              future: _cinemasFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                } else if (snapshot.hasError) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text('Lỗi khi tải danh sách rạp: ${snapshot.error}'),
                        const SizedBox(height: 10),
                        ElevatedButton(
                          onPressed: () {
                            setState(() {
                              _cinemasFuture = _cinemaService.getCinemas();
                            });
                          },
                          child: const Text('Thử lại'),
                        ),
                      ],
                    ),
                  );
                } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                  return const Center(
                    child: Text('Không có rạp chiếu phim nào'),
                  );
                }

                final cinemas = snapshot.data!.where((cinema) => cinema.isActive).toList();

                return ListView.separated(
                  itemCount: cinemas.length,
                  separatorBuilder:
                      (context, index) => const Divider(
                        height: 40,
                        thickness: 1,
                        color: Colors.grey,
                      ),
                  itemBuilder: (context, index) {
                    final cinema = cinemas[index];
                    return GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder:
                                (context) =>
                                    CinemaShowTimesScreen(cinema: cinema),
                          ),
                        );
                      },
                      child: Padding(
                        padding: const EdgeInsets.symmetric(
                          vertical: 8.0,
                          horizontal: 16.0,
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child:
                                  cinema.imageName != null
                                      ? Image.network(
                                        _cinemaService.getCinemaImageUrl(
                                          cinema.id,
                                          cinema.imageName,
                                        ),
                                        width: 150,
                                        height: 100,
                                        fit: BoxFit.cover,
                                        errorBuilder: (
                                          context,
                                          error,
                                          stackTrace,
                                        ) {
                                          return Image.asset(
                                            'assets/images/bear.jpg',
                                            width: 100,
                                            height: 100,
                                            fit: BoxFit.cover,
                                          );
                                        },
                                      )
                                      : Image.network(
                                        'https://yt3.googleusercontent.com/ytc/AIdro_nml8pToD7yNeAVIPMck_emdM0lt4pFCI_i-y_k0EFUzyg=s900-c-k-c0x00ffffff-no-rj',
                                        width: 150,
                                        height: 100,
                                        fit: BoxFit.cover,
                                        errorBuilder: (
                                          context,
                                          error,
                                          stackTrace,
                                        ) {
                                          return Container(
                                            width: 80,
                                            height: 80,
                                            color: Colors.grey[300],
                                            child: const Icon(
                                              Icons.broken_image,
                                            ),
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
                                    cinema.name,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 16,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    cinema.address,
                                    style: const TextStyle(
                                      fontSize: 14,
                                      color: Colors.black87,
                                    ),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    cinema.phoneNumber,
                                    style: const TextStyle(
                                      fontSize: 14,
                                      color: Colors.black54,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
