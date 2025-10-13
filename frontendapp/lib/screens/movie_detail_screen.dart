import 'dart:convert';
import 'package:frontendapp/screens/seat_selection_screen.dart';
import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:intl/intl.dart';
import '../app_config.dart';
import '../models/cast.dart';
import '../models/movie.dart';
import '../models/cinema.dart';
import '../models/room.dart';
import '../models/seat.dart';
import '../models/showtime.dart';
import '../services/MovieService.dart';
import '../services/movie_news_service.dart';
import '../screens/news_detail_screen.dart';
import '../models/movie_news.dart';
import 'package:http/http.dart' as http;

class MovieDetailScreen extends StatefulWidget {
  const MovieDetailScreen({
    super.key,
    required this.movie,
    required this.selectedLocation,
    required this.cities,
  });

  final Movie movie;
  final String selectedLocation;
  final List<String> cities;

  @override
  State<MovieDetailScreen> createState() => _MovieDetailScreenState();
}

class _MovieDetailScreenState extends State<MovieDetailScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  Cinema? selectedCinema;
  int selectedIndex = 0;
  DateTime selectedDate = DateTime.now();
  final ScrollController _scrollController = ScrollController();
  final ValueNotifier<double> _titleOpacityNotifier = ValueNotifier<double>(0.0);
  final MovieService _movieService = MovieService();
  final MovieNewsService _newsService = MovieNewsService();
  late Future<List<Cast>> _castsFuture;
  Map<int, int> roomToCinemaMap = {};
  String currentLocation = '';
  late Future<List<Cinema>> _cinemasFuture;

  @override
  void initState() {
    super.initState();
    currentLocation = widget.selectedLocation;
    _cinemasFuture = getCinemaByMovieIdAndCityAndDate();
    _castsFuture = getCastsByMovieId(widget.movie.id);

    initializeDateFormatting('vi_VN', null).then((_) {
      setState(() {});
    });

    _scrollController.addListener(() {
      double offset = _scrollController.offset;
      _titleOpacityNotifier.value = (offset > 180) ? 1.0 : 0.0;
    });

    _tabController = TabController(length: 3, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    _scrollController.dispose();
    _titleOpacityNotifier.dispose();
    super.dispose();
  }

  Future<List<Cast>> getCastsByMovieId(int movieId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/casts/$movieId');
      print('Request URL for casts: $url');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Cast.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load casts: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching casts: $e');
      return [];
    }
  }

  Future<List<Cinema>> getCinemaByMovieIdAndCityAndDate() async {
    try {
      String city = currentLocation == "Toàn quốc" ? "all" : currentLocation;
      String encodedLocation = Uri.encodeQueryComponent(city);
      final url = await Uri.parse(
        '${AppConfig.BASEURL}/api/v1/cinemas/movieandcityanddate?movieId=${widget.movie.id}&city=$encodedLocation&date=${DateFormat('yyyy-MM-dd').format(selectedDate)}',
      );
      print('Request URL for cinemas: $url');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        print('Cinemas data: $data');
        return data.map((json) => Cinema.fromJson(json)).toList();
      } else {
        throw Exception("Error fetching cinemas: ${response.statusCode}");
      }
    } catch (e) {
      print('Error fetching cinemas: $e');
      return [];
    }
  }

  Future<List<Showtime>> getShowTimeByMovieIdAndCinemaIdAndDate(int cinemaId, DateTime date) async {
    try {
      final url = await Uri.parse(
        '${AppConfig.BASEURL}/api/v1/showtimes?movieId=${widget.movie.id}&cinemaId=$cinemaId&date=${DateFormat('yyyy-MM-dd').format(date)}',
      );
      print('Request URL for showtimes: $url');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        print('Showtimes data for cinemaId $cinemaId: $data');
        final newShowtimes = data.map((json) => Showtime.fromJson(json)).toList();
        for (var show in newShowtimes) {
          if (!roomToCinemaMap.containsKey(show.roomId)) {
            final room = await getRoomById(show.roomId);
            if (room != null) {
              roomToCinemaMap[show.roomId] = room.cinemaId;
              print('Mapped roomId ${show.roomId} to cinemaId ${room.cinemaId}');
            } else {
              print('Failed to fetch room for roomId ${show.roomId}');
            }
          }
        }
        print('Current roomToCinemaMap: $roomToCinemaMap');
        return newShowtimes;
      } else {
        throw Exception('Error fetching showtimes: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching showtimes: $e');
      return [];
    }
  }

  Future<Room?> getRoomById(int roomId) async {
    try {
      final url = await Uri.parse('${AppConfig.BASEURL}/api/v1/rooms/$roomId');
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
      final url = await Uri.parse('${AppConfig.BASEURL}/api/v1/seats?roomId=$roomId');
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        controller: _scrollController,
        slivers: <Widget>[
          SliverAppBar(
            expandedHeight: 250.0,
            pinned: true,
            floating: false,
            backgroundColor: Colors.white,
            elevation: 0,
            title: ValueListenableBuilder<double>(
              valueListenable: _titleOpacityNotifier,
              builder: (context, titleOpacity, child) {
                return AnimatedOpacity(
                  duration: const Duration(milliseconds: 300),
                  opacity: titleOpacity,
                  child: Center(
                    child: Text(
                      widget.movie.name,
                      style: TextStyle(color: Colors.black),
                    ),
                  ),
                );
              },
            ),
            leading: ValueListenableBuilder<double>(
              valueListenable: _titleOpacityNotifier,
              builder: (context, titleOpacity, child) {
                return IconButton(
                  icon: Icon(
                    Icons.arrow_back,
                    color: Color.lerp(Colors.white, Colors.black, titleOpacity),
                  ),
                  onPressed: () => Navigator.of(context).pop(),
                  tooltip: 'Quay lại',
                );
              },
            ),
            actions: [
              ValueListenableBuilder<double>(
                valueListenable: _titleOpacityNotifier,
                builder: (context, titleOpacity, child) {
                  return IconButton(
                    icon: Icon(
                      Icons.share_outlined,
                      color: Color.lerp(Colors.white, Colors.black, titleOpacity),
                    ),
                    onPressed: () {},
                    tooltip: 'Chia sẻ',
                  );
                },
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    widget.movie.bannerUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => Container(color: Colors.grey),
                  ),
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [
                          Colors.black.withOpacity(0.6),
                          Colors.transparent,
                          Colors.black.withOpacity(0.8),
                        ],
                        stops: const [0.0, 0.4, 1.0],
                      ),
                    ),
                  ),
                  Positioned.fill(
                    child: Center(
                      child: Container(
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.8),
                          shape: BoxShape.circle,
                        ),
                        child: IconButton(
                          icon: const Icon(
                            Icons.play_arrow,
                            size: 40,
                            color: Colors.black,
                          ),
                          onPressed: () {},
                          tooltip: 'Xem trailer',
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      widget.movie.posterUrl,
                      height: 150,
                      width: 100,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        height: 150,
                        width: 100,
                        color: Colors.grey[300],
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.movie.name,
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            const Icon(
                              Icons.star,
                              color: Colors.orange,
                              size: 20,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              '${widget.movie.voteAverage}',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const Spacer(),
                            TextButton.icon(
                              onPressed: () {},
                              icon: const Icon(
                                Icons.edit_outlined,
                                size: 16,
                                color: Colors.blue,
                              ),
                              label: const Text(
                                'Đánh Giá',
                                style: TextStyle(color: Colors.blue),
                              ),
                              style: TextButton.styleFrom(
                                padding: EdgeInsets.zero,
                                visualDensity: VisualDensity.compact,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            _buildInfoTag(
                              '${widget.movie.ageRating}',
                              Colors.redAccent,
                            ),
                            const SizedBox(width: 8),
                            _buildInfoTag(
                              '${widget.movie.duration} Phút',
                              Colors.grey,
                              icon: Icons.access_time,
                            ),
                            const SizedBox(width: 8),
                            _buildInfoTag(
                              DateFormat('dd/MM/yyyy').format(
                                DateTime.parse(widget.movie.releaseDate),
                              ),
                              Colors.grey,
                              icon: Icons.calendar_today,
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          SliverPersistentHeader(
            delegate: _SliverAppBarDelegate(
              TabBar(
                controller: _tabController,
                labelColor: Colors.blue,
                unselectedLabelColor: Colors.grey,
                indicatorColor: Colors.blue,
                tabs: const [
                  Tab(text: 'Suất Chiếu'),
                  Tab(text: 'Thông Tin'),
                  Tab(text: 'Tin Tức'),
                ],
              ),
            ),
            pinned: true,
          ),
          SliverFillRemaining(
            hasScrollBody: true,
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildSuatChieuTabContent(),
                _buildThongTinTabContent(widget.movie),
                _buildTinTucTabContent(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSuatChieuTabContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () {
                    showModalBottomSheet(
                      context: context,
                      isScrollControlled: true,
                      shape: const RoundedRectangleBorder(
                        borderRadius: BorderRadius.vertical(top: Radius.circular(25.0)),
                      ),
                      builder: (context) {
                        return Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Text(
                                "Chọn địa điểm",
                                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 10),
                              Flexible(
                                child: ListView.builder(
                                  shrinkWrap: true,
                                  itemCount: widget.cities.length,
                                  itemBuilder: (context, index) {
                                    final city = widget.cities[index];
                                    final isSelected = currentLocation == city;
                                    return ListTile(
                                      leading: const Icon(Icons.location_city),
                                      title: Text(
                                        city,
                                        style: TextStyle(
                                          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                          color: isSelected ? Colors.blue : Colors.black,
                                        ),
                                      ),
                                      onTap: () {
                                        setState(() {
                                          currentLocation = city;
                                          selectedCinema = null;
                                          roomToCinemaMap.clear();
                                          _cinemasFuture = getCinemaByMovieIdAndCityAndDate();
                                        });
                                        Navigator.pop(context);
                                      },
                                    );
                                  },
                                ),
                              ),
                              const SizedBox(height: 10),
                              ElevatedButton(
                                onPressed: () {
                                  Navigator.pop(context);
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.blueAccent,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                ),
                                child: const Text("Done", style: TextStyle(color: Colors.white)),
                              ),
                            ],
                          ),
                        );
                      },
                    );
                  },
                  icon: const Icon(Icons.location_city),
                  label: Text(currentLocation),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () {
                    showModalBottomSheet(
                      context: context,
                      isScrollControlled: true,
                      shape: const RoundedRectangleBorder(
                        borderRadius: BorderRadius.vertical(top: Radius.circular(25.0)),
                      ),
                      builder: (context) {
                        return FutureBuilder<List<Cinema>>(
                          future: _cinemasFuture,
                          builder: (context, snapshot) {
                            if (snapshot.connectionState == ConnectionState.waiting) {
                              return const Center(child: CircularProgressIndicator());
                            } else if (snapshot.hasError) {
                              return const Center(child: Text('Lỗi khi tải danh sách rạp'));
                            } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                              return const Center(child: Text('Không có rạp nào'));
                            }

                            final filteredCinemas = snapshot.data!;

                            return Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Text(
                                    "Chọn rạp chiếu",
                                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 10),
                                  Flexible(
                                    child: ListView.builder(
                                      shrinkWrap: true,
                                      itemCount: filteredCinemas.length + 1,
                                      itemBuilder: (context, index) {
                                        if (index == 0) {
                                          final isSelected = selectedCinema == null;
                                          return ListTile(
                                            leading: const Icon(Icons.theaters),
                                            title: Text(
                                              'Tất cả rạp',
                                              style: TextStyle(
                                                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                                color: isSelected ? Colors.blue : Colors.black,
                                              ),
                                            ),
                                            onTap: () {
                                              setState(() {
                                                selectedCinema = null;
                                              });
                                              Navigator.pop(context);
                                            },
                                          );
                                        } else {
                                          final cinema = filteredCinemas[index - 1];
                                          final isSelected = cinema.name == selectedCinema?.name;
                                          return ListTile(
                                            leading: const Icon(Icons.local_movies),
                                            title: Text(
                                              cinema.name,
                                              style: TextStyle(
                                                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                                color: isSelected ? Colors.blue : Colors.black,
                                              ),
                                            ),
                                            onTap: () {
                                              setState(() {
                                                selectedCinema = cinema;
                                              });
                                              Navigator.pop(context);
                                            },
                                          );
                                        }
                                      },
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  ElevatedButton(
                                    onPressed: () {
                                      Navigator.pop(context);
                                    },
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: Colors.blueAccent,
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                    ),
                                    child: const Text("Done", style: TextStyle(color: Colors.white)),
                                  ),
                                ],
                              ),
                            );
                          },
                        );
                      },
                    );
                  },
                  icon: const Icon(Icons.theaters),
                  label: Text(selectedCinema?.name ?? "Cinema"),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          SingleChildScrollView(
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
                      roomToCinemaMap.clear();
                      _cinemasFuture = getCinemaByMovieIdAndCityAndDate();
                    });
                  },
                  child: Container(
                    margin: const EdgeInsets.only(right: 8),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      color: isSelected ? Colors.blue : Colors.grey[200],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          index == 0 ? 'Hôm nay' : DateFormat('EEE', 'vi_VN').format(date),
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
          const SizedBox(height: 8),
          Center(
            child: Text(
              DateFormat("EEEE, 'ngày' dd 'tháng' MM yyyy", 'vi_VN').format(selectedDate),
              style: TextStyle(color: Colors.grey[600]),
            ),
          ),
          const SizedBox(height: 16),
          FutureBuilder<List<Cinema>>(
            future: _cinemasFuture,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.hasError) {
                return const Center(child: Text('Lỗi khi tải danh sách rạp'));
              } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                return Center(
                  child: Column(
                    children: [
                      const Text(
                        'Không có rạp chiếu phim tại khu vực này',
                        style: TextStyle(fontSize: 16, color: Colors.grey),
                      ),
                      const SizedBox(height: 10),
                      ElevatedButton(
                        onPressed: () {
                          showModalBottomSheet(
                            context: context,
                            isScrollControlled: true,
                            shape: const RoundedRectangleBorder(
                              borderRadius: BorderRadius.vertical(top: Radius.circular(25.0)),
                            ),
                            builder: (context) {
                              return Padding(
                                padding: const EdgeInsets.all(16.0),
                                child: Column(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Text(
                                      "Chọn địa điểm",
                                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                                    ),
                                    const SizedBox(height: 10),
                                    Flexible(
                                      child: ListView.builder(
                                        shrinkWrap: true,
                                        itemCount: widget.cities.length,
                                        itemBuilder: (context, index) {
                                          final city = widget.cities[index];
                                          final isSelected = currentLocation == city;
                                          return ListTile(
                                            leading: const Icon(Icons.location_city),
                                            title: Text(
                                              city,
                                              style: TextStyle(
                                                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                                color: isSelected ? Colors.blue : Colors.black,
                                              ),
                                            ),
                                            onTap: () {
                                              setState(() {
                                                currentLocation = city;
                                                selectedCinema = null;
                                                roomToCinemaMap.clear();
                                                _cinemasFuture = getCinemaByMovieIdAndCityAndDate();
                                              });
                                              Navigator.pop(context);
                                            },
                                          );
                                        },
                                      ),
                                    ),
                                    const SizedBox(height: 10),
                                    ElevatedButton(
                                      onPressed: () {
                                        Navigator.pop(context);
                                      },
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.blueAccent,
                                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                      ),
                                      child: const Text("Done", style: TextStyle(color: Colors.white)),
                                    ),
                                  ],
                                ),
                              );
                            },
                          );
                        },
                        child: const Text('Chọn khu vực khác'),
                      ),
                    ],
                  ),
                );
              }

              final filteredCinemas = snapshot.data!;

              return Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: filteredCinemas.map((cinema) {
                  if (selectedCinema != null && cinema.id != selectedCinema!.id) {
                    return const SizedBox();
                  }
                  return FutureBuilder<List<Showtime>>(
                    future: getShowTimeByMovieIdAndCinemaIdAndDate(cinema.id, selectedDate),
                    builder: (context, showtimeSnapshot) {
                      if (showtimeSnapshot.connectionState == ConnectionState.waiting) {
                        return const Center(child: CircularProgressIndicator());
                      } else if (showtimeSnapshot.hasError) {
                        return const Center(child: Text('Lỗi khi tải suất chiếu'));
                      } else if (!showtimeSnapshot.hasData || showtimeSnapshot.data!.isEmpty) {
                        return const SizedBox();
                      }

                      final showtimes = showtimeSnapshot.data!.where((show) =>show.isactive).toList();

                      if(showtimes.isEmpty){
                        return const SizedBox();
                      }

                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            cinema.name,
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                          Text(
                            "2D PHỤ ĐỀ",
                            style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                          ),
                          const SizedBox(height: 8),
                          Wrap(
                            spacing: 8.0,
                            runSpacing: 8.0,
                            children: showtimes.map((show) {
                              final time = DateFormat('h:mm a').format(show.startTime);
                              return OutlinedButton(
                                onPressed: () async {
                                  // Kiểm tra trạng thái đăng nhập
                                  if (!AppConfig.isLogin) {
                                    // Hiển thị thông báo yêu cầu đăng nhập
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('Vui lòng đăng nhập để chọn ghế!')),
                                    );
                                    // Chưa đăng nhập: Điều hướng đến DangNhapScreen và chờ kết quả
                                    final result = await Navigator.pushNamed(context, '/dangnhap');
                                    if (result == true && mounted) {
                                      // Đăng nhập thành công, tiếp tục đến SeatSelectionScreen
                                      _navigateToSeatSelectionScreen(cinema, show, showtimes);
                                    }
                                    // Nếu đăng nhập thất bại hoặc bị hủy, không làm gì thêm
                                    return;
                                  }
                                  // Đã đăng nhập: Điều hướng trực tiếp đến SeatSelectionScreen
                                  _navigateToSeatSelectionScreen(cinema, show, showtimes);
                                },
                                style: OutlinedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                  side: BorderSide(color: Colors.grey[400]!),
                                  textStyle: const TextStyle(fontSize: 14),
                                  foregroundColor: Colors.black87,
                                ),
                                child: Text(time),
                              );
                            }).toList(),
                          ),
                          const SizedBox(height: 20),
                        ],
                      );
                    },
                  );
                }).toList(),
              );
            },
          ),
        ],
      ),
    );
  }

  void _navigateToSeatSelectionScreen(Cinema cinema, Showtime show, List<Showtime> showtimes) async {
    final room = await getRoomById(show.roomId);
    if (room != null) {
      final seats = await getSeatByRoomId(show.roomId);
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => SeatSelectionScreen(
            room: room,
            allSeats: seats,
            showtime: show,
            movie: widget.movie,
            cinema: cinema,
            listshowtime: showtimes,
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

  Widget _buildThongTinTabContent(Movie movie) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Nội dung phim',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            movie.description,
            style: TextStyle(
              fontSize: 15,
              color: Colors.grey[700],
              height: 1.4,
            ),
          ),
          const SizedBox(height: 24),
          _buildInfoRow('Đạo diễn:', '${widget.movie.director}'),
          FutureBuilder<List<Cast>>(
            future: _castsFuture,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return _buildInfoRow('Diễn viên:', 'Đang tải...');
              } else if (snapshot.hasError) {
                return _buildInfoRow('Diễn viên:', 'Lỗi khi tải dữ liệu');
              } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                return _buildInfoRow('Diễn viên:', 'Không có thông tin');
              }

              final casts = snapshot.data!;
              final castNames = casts.map((cast) => cast.actorName).join(", ");
              return _buildInfoRow('Diễn viên:', '$castNames, ...');
            },
          ),
          _buildInfoRow('Thể loại:', 'Hành động, Phiêu lưu'),
          _buildInfoRow(
            'Ngày phát hành:',
            DateFormat('dd/MM/yyyy').format(DateTime.parse(movie.releaseDate)),
          ),
          _buildInfoRow('Thời lượng:', '${movie.duration} phút'),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 110,
            child: Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.bold,
                color: Colors.grey[800],
              ),
            ),
          ),
          Expanded(
            child: Text(value, style: TextStyle(color: Colors.grey[700])),
          ),
        ],
      ),
    );
  }

  Widget _buildTinTucTabContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Phim tương tự',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),
          FutureBuilder<List<Movie>>(
            future: _movieService.getSimilarMovies(widget.movie.id),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.hasError) {
                return const Text('Lỗi khi tải dữ liệu');
              } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                return const Text('Không có phim tương tự');
              }

              final relatedMovies = snapshot.data!;

              return SizedBox(
                height: 200,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: relatedMovies.length,
                  itemBuilder: (context, index) {
                    final movie = relatedMovies[index];
                    return GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => MovieDetailScreen(
                              movie: movie,
                              selectedLocation: currentLocation,
                              cities: widget.cities,
                            ),
                          ),
                        );
                      },
                      child: Container(
                        width: 150,
                        margin: const EdgeInsets.only(right: 12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: Image.network(
                                movie.posterUrl,
                                height: 100,
                                width: 150,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) {
                                  return Container(
                                    height: 100,
                                    width: 150,
                                    color: Colors.grey,
                                    child: const Icon(Icons.broken_image),
                                  );
                                },
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              movie.name,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              movie.releaseDate ?? 'Không rõ',
                              style: const TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              );
            },
          ),
          const SizedBox(height: 24),
          const Text(
            'Đọc thêm',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),
          FutureBuilder<List<MovieNews>>(
            future: _newsService.getMovieNews(),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.hasError) {
                return const Text('Lỗi khi tải dữ liệu tin tức');
              } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                return const Text('Không có tin tức liên quan');
              }

              final relatedNews = snapshot.data!.where((news) => news.movieId == widget.movie.id).toList();

              if (relatedNews.isEmpty) {
                return const Text('Không có tin tức liên quan');
              }

              return SizedBox(
                height: 200,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: relatedNews.length,
                  itemBuilder: (context, index) {
                    final news = relatedNews[index];
                    return GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => NewsDetailScreen(news: news),
                          ),
                        );
                      },
                      child: Container(
                        width: 200,
                        margin: const EdgeInsets.only(right: 12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: Image.network(
                                news.imageUrl,
                                height: 100,
                                width: 200,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) {
                                  return Container(
                                    height: 100,
                                    width: 200,
                                    color: Colors.grey,
                                    child: const Icon(Icons.broken_image),
                                  );
                                },
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              news.title,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              news.publishDate ?? 'Không rõ',
                              style: const TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}

class _SliverAppBarDelegate extends SliverPersistentHeaderDelegate {
  _SliverAppBarDelegate(this._tabBar);

  final TabBar _tabBar;

  @override
  double get minExtent => _tabBar.preferredSize.height;

  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      color: Theme.of(context).scaffoldBackgroundColor,
      child: _tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverAppBarDelegate oldDelegate) {
    return true;
  }
}