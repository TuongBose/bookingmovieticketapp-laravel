import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';
import '../app_config.dart';
import '../models/cinema.dart';
import '../models/movie.dart';
import '../models/showtime.dart';
import '../models/room.dart';
import '../models/seat.dart';
import '../screens/seat_selection_screen.dart';

class CinemaShowTimesScreen extends StatefulWidget {
  final Cinema cinema;

  const CinemaShowTimesScreen({super.key, required this.cinema});

  @override
  _CinemaShowtimesScreenState createState() => _CinemaShowtimesScreenState();
}

class _CinemaShowtimesScreenState extends State<CinemaShowTimesScreen> {
  int selectedIndex = 0;
  DateTime selectedDate = DateTime.now();
  late Future<List<Showtime>> _showtimesFuture;
  Map<int, Movie> _moviesCache = {};
  Map<int, Room> _roomsCache = {};

  @override
  void initState() {
    super.initState();
    initializeDateFormatting('vi_VN', null).then((_) {
      setState(() {});
    });
    _showtimesFuture = _fetchShowtimes();
  }

  Future<List<Showtime>> _fetchShowtimes() async {
    try {
      final url = Uri.parse(
        '${AppConfig.BASEURL}/api/v1/showtimes/cinemaanddate?cinemaId=${widget.cinema.id}&date=${DateFormat('yyyy-MM-dd').format(selectedDate)}',
      );
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        final showtimes = data.map((json) => Showtime.fromJson(json)).toList();

        final activeShowtimes = showtimes.where((showtime) => showtime.isactive).toList();

        // Lấy thông tin phim và phòng chiếu cho từng suất chiếu
        for (var showtime in activeShowtimes) {
          // Lấy thông tin phim
          if (!_moviesCache.containsKey(showtime.movieId)) {
            final movie = await _fetchMovieById(showtime.movieId);
            if (movie != null) {
              _moviesCache[showtime.movieId] = movie;
            }
          }
          // Lấy thông tin phòng chiếu
          if (!_roomsCache.containsKey(showtime.roomId)) {
            final room = await getRoomById(showtime.roomId);
            if (room != null) {
              _roomsCache[showtime.roomId] = room;
            }
          }
        }
        return activeShowtimes;
      } else {
        throw Exception('Failed to load showtimes: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching showtimes: $e');
      return [];
    }
  }

  Future<Movie?> _fetchMovieById(int movieId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/movies/$movieId');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Movie.fromJson(data);
      } else {
        print('Failed to load movie $movieId: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error fetching movie $movieId: $e');
      return null;
    }
  }

  Future<Room?> getRoomById(int roomId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/rooms/$roomId');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Room.fromJson(data);
      } else {
        throw Exception('Error fetching room: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching room: $e');
      return null;
    }
  }

  Future<List<Seat>> getSeatByRoomId(int roomId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/seats?roomId=$roomId');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Seat.fromJson(json)).toList();
      } else {
        throw Exception('Error fetching seats: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching seats: $e');
      return [];
    }
  }

  // Hàm mở ứng dụng Google Maps trên điện thoại
  Future<void> _openGoogleMaps() async {
    if (widget.cinema.coordinates.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Tọa độ của rạp không khả dụng')),
      );
      return;
    }

    try {
      // Tách tọa độ từ chuỗi "latitude,longitude"
      final coordinates = widget.cinema.coordinates.split(',');
      if (coordinates.length != 2) {
        throw Exception('Định dạng tọa độ không hợp lệ');
      }

      final latitude = double.parse(coordinates[0].trim());
      final longitude = double.parse(coordinates[1].trim());
      final cinemaName = Uri.encodeComponent(widget.cinema.name);

      // URL cho trình duyệt web (sẽ được sử dụng nếu ứng dụng Google Maps không khả dụng)
      final Uri fallbackUrl = Uri.parse(
          'https://www.google.com/maps/search/?api=1&query=$latitude,$longitude&query_place_id=${widget.cinema.name}'
      );

      // Thử mở ứng dụng Google Maps trước
      final Uri mapsUrl = Uri.parse(
          'geo:$latitude,$longitude?q=$latitude,$longitude($cinemaName)'
      );

      print('Trying to launch URL: $mapsUrl');

      if (await canLaunchUrl(mapsUrl)) {
        await launchUrl(
          mapsUrl,
          mode: LaunchMode.externalApplication,
        );
      }
      // Nếu không mở được, thử dùng URL schema của Google Maps
      else {
        final Uri googleMapsUrl = Uri.parse(
            'https://www.google.com/maps/search/?api=1&query=$latitude,$longitude'
        );

        if (await canLaunchUrl(googleMapsUrl)) {
          await launchUrl(
            googleMapsUrl,
            mode: LaunchMode.externalApplication,
          );
        }
        // Nếu không, mở bản đồ trong trình duyệt
        else if (await canLaunchUrl(fallbackUrl)) {
          await launchUrl(
            fallbackUrl,
            mode: LaunchMode.externalApplication,
          );
        } else {
          throw 'Không thể mở ứng dụng bản đồ hoặc trình duyệt web';
        }
      }
    } catch (e) {
      print('Error launching Google Maps: $e');
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Lỗi khi mở Google Maps: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              widget.cinema.name,
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.black,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 5,),
            Row(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.location_on, size: 16, color: Colors.grey),
                const SizedBox(width: 4),
                Flexible(
                  // Thêm Flexible
                  fit: FlexFit.loose,
                  child: Text(
                    widget.cinema.address,
                    style: const TextStyle(fontSize: 14, color: Colors.grey),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    textAlign: TextAlign.center,
                  ),
                ),
              ],
            ),
          ],
        ),
        automaticallyImplyLeading: true,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.gps_fixed, color: Colors.blue),
            onPressed: _openGoogleMaps,
            tooltip: 'Xem trên bản đồ',
          ),
        ],
      ),
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Danh sách ngày
          Padding(
            padding: const EdgeInsets.symmetric(
              horizontal: 16.0,
              vertical: 8.0,
            ),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: List.generate(7, (index) {
                  final date = DateTime.now().add(Duration(days: index));
                  final isSelected = selectedIndex == index;
                  return InkWell(
                    onTap: () {
                      setState(() {
                        selectedIndex = index;
                        selectedDate = date;
                        _moviesCache.clear();
                        _roomsCache.clear();
                        _showtimesFuture = _fetchShowtimes();
                      });
                    },
                    child: Container(
                      margin: const EdgeInsets.only(right: 8),
                      padding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 8,
                      ),
                      decoration: BoxDecoration(
                        color: isSelected ? Colors.blue : Colors.grey[200],
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            index == 0
                                ? 'Hôm nay'
                                : DateFormat('EEE', 'vi_VN').format(date),
                            style: TextStyle(
                              fontSize: 12,
                              color: isSelected ? Colors.white : Colors.black54,
                            ),
                          ),
                          Text(
                            DateFormat('dd/MM').format(date),
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: isSelected ? Colors.white : Colors.black,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }),
              ),
            ),
          ),
          // Ngày được chọn
          Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(vertical: 8.0),
              child: Text(
                DateFormat(
                  "EEEE, 'ngày' dd 'tháng' MM yyyy",
                  'vi_VN',
                ).format(selectedDate),
                style: TextStyle(color: Colors.grey[600]),
              ),
            ),
          ),
          // Danh sách phim và suất chiếu
          Expanded(
            child: FutureBuilder<List<Showtime>>(
              future: _showtimesFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                } else if (snapshot.hasError) {
                  return const Center(child: Text('Lỗi khi tải suất chiếu'));
                } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                  return const Center(
                    child: Text('Không có suất chiếu vào ngày này'),
                  );
                }

                final showtimes = snapshot.data!;

                // Nhóm suất chiếu theo phim
                Map<int, List<Showtime>> showtimesByMovie = {};
                for (var showtime in showtimes) {
                  if (!showtimesByMovie.containsKey(showtime.movieId)) {
                    showtimesByMovie[showtime.movieId] = [];
                  }
                  showtimesByMovie[showtime.movieId]!.add(showtime);
                }

                return ListView.builder(
                  padding: const EdgeInsets.all(16.0),
                  itemCount: showtimesByMovie.length,
                  itemBuilder: (context, index) {
                    final movieId = showtimesByMovie.keys.elementAt(index);
                    final movieShowtimes = showtimesByMovie[movieId]!;
                    final movie = _moviesCache[movieId];

                    if (movie == null) {
                      return const SizedBox(); // Bỏ qua nếu không lấy được thông tin phim
                    }

                    // Nhóm suất chiếu theo phòng chiếu
                    Map<int, List<Showtime>> showtimesByRoom = {};
                    for (var showtime in movieShowtimes) {
                      if (!showtimesByRoom.containsKey(showtime.roomId)) {
                        showtimesByRoom[showtime.roomId] = [];
                      }
                      showtimesByRoom[showtime.roomId]!.add(showtime);
                    }

                    return Padding(
                      padding: const EdgeInsets.only(bottom: 16.0),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: Image.network(
                              movie.posterUrl,
                              width: 100,
                              height: 150,
                              fit: BoxFit.cover,
                              errorBuilder: (context, error, stackTrace) {
                                return Container(
                                  width: 100,
                                  height: 150,
                                  color: Colors.grey[300],
                                  child: const Icon(Icons.broken_image),
                                );
                              },
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  movie.name,
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  children: [
                                    _buildInfoTag(
                                      movie.ageRating,
                                      Colors.redAccent,
                                    ),
                                    const SizedBox(width: 8),
                                    _buildInfoTag(
                                      '${movie.duration} Phút',
                                      Colors.grey,
                                      icon: Icons.access_time,
                                    ),
                                    const SizedBox(width: 8),
                                    _buildInfoTag(
                                      DateFormat('dd/MM/yyyy').format(
                                        DateTime.parse(movie.releaseDate),
                                      ),
                                      Colors.grey,
                                      icon: Icons.calendar_today,
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  children: [
                                    const Icon(
                                      Icons.star,
                                      color: Colors.orange,
                                      size: 20,
                                    ),
                                    const SizedBox(width: 4),
                                    Text(
                                      '${movie.voteAverage}',
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                // Hiển thị các nhóm suất chiếu theo phòng
                                ...showtimesByRoom.entries.map((entry) {
                                  final roomId = entry.key;
                                  final showtimesForRoom = entry.value;
                                  final room = _roomsCache[roomId];
                                  final roomName =
                                      room?.name ?? 'Phòng không xác định';

                                  return Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        roomName,
                                        style: const TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Wrap(
                                        spacing: 8.0,
                                        runSpacing: 8.0,
                                        children:
                                            showtimesForRoom.map((show) {
                                              // Tính thời gian kết thúc dựa trên startTime và duration
                                              final endTime = show.startTime
                                                  .add(
                                                    Duration(
                                                      minutes: movie.duration,
                                                    ),
                                                  );
                                              final time =
                                                  "${DateFormat('HH:mm').format(show.startTime)} - ${DateFormat('HH:mm').format(endTime)}";
                                              return OutlinedButton(
                                                onPressed: () async {
                                                  // Kiểm tra trạng thái đăng nhập
                                                  if (!AppConfig.isLogin) {
                                                    ScaffoldMessenger.of(
                                                      context,
                                                    ).showSnackBar(
                                                      const SnackBar(
                                                        content: Text(
                                                          'Vui lòng đăng nhập để chọn ghế!',
                                                        ),
                                                      ),
                                                    );
                                                    final result =
                                                        await Navigator.pushNamed(
                                                          context,
                                                          '/dangnhap',
                                                        );
                                                    if (result == true &&
                                                        mounted) {
                                                      _navigateToSeatSelection(
                                                        show,
                                                        movie,
                                                      );
                                                    }
                                                    return;
                                                  }
                                                  _navigateToSeatSelection(
                                                    show,
                                                    movie,
                                                  );
                                                },
                                                style: OutlinedButton.styleFrom(
                                                  padding:
                                                      const EdgeInsets.symmetric(
                                                        horizontal: 12,
                                                        vertical: 8,
                                                      ),
                                                  side: BorderSide(
                                                    color: Colors.grey[400]!,
                                                  ),
                                                  textStyle: const TextStyle(
                                                    fontSize: 14,
                                                  ),
                                                  foregroundColor:
                                                      Colors.black87,
                                                ),
                                                child: Text(time),
                                              );
                                            }).toList(),
                                      ),
                                      const SizedBox(height: 8),
                                    ],
                                  );
                                }).toList(),
                              ],
                            ),
                          ),
                        ],
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

  void _navigateToSeatSelection(Showtime showtime, Movie movie) async {
    final room = await getRoomById(showtime.roomId);
    if (room != null) {
      final seats = await getSeatByRoomId(showtime.roomId);
      Navigator.push(
        context,
        MaterialPageRoute(
          builder:
              (context) => SeatSelectionScreen(
                room: room,
                allSeats: seats,
                showtime: showtime,
                movie: movie,
                cinema: widget.cinema,
                listshowtime: [], // Truyền danh sách showtimes nếu cần
              ),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Không thể tải thông tin phòng chiếu')),
      );
    }
  }

  Widget _buildInfoTag(String text, Color color, {IconData? icon}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) Icon(icon, size: 12, color: color),
          if (icon != null) const SizedBox(width: 4),
          Text(
            text,
            style: TextStyle(
              fontSize: 11,
              color: color,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}
