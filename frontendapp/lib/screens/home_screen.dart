import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shimmer/shimmer.dart';
import '../models/cinema.dart';
import '../screens/movie_detail_screen.dart';
import '../services/CinemaService.dart';
import '../services/MovieService.dart';
import '../models/movie.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => HomeScreenState();
}

class HomeScreenState extends State<HomeScreen> {
  List<Movie> _moviesNowPlaying = [];
  List<Movie> _moviesUpComing = [];
  List<Cinema> _filteredCinemas = [];
  List<Cinema> _cinemas = [];

  bool _isLoading = true;
  bool _hasError = false;
  String _errorMessage = "";
  final PageController _pageController = PageController(viewportFraction: 0.9);
  Timer? _timer;
  String _selectedLocation = "Toàn quốc";

  // Sử dụng ValueNotifier để quản lý trạng thái
  final ValueNotifier<int> _selectedIndexNotifier = ValueNotifier<int>(0);
  final ValueNotifier<int> _currentCardPageNotifier = ValueNotifier<int>(0);

  Future<void> loadData() async {
    setState(() {
      _isLoading = true;
      _hasError = false;
      _errorMessage = '';
    });

    try {
      MovieService movieService = MovieService();
      CinemaService cinemaService = CinemaService();

      final results = await Future.wait([
        movieService.getNowPlaying(),
        movieService.getUpComing(),
        cinemaService.getCinemas(),
      ]);
      setState(() {
        _moviesNowPlaying = (results[0] as List<Movie>);
        _moviesUpComing = (results[1] as List<Movie>);
        _cinemas = (results[2] as List<Cinema>);
        _filteredCinemas = _cinemas.where((cinema) =>cinema.isActive).toList();
        _isLoading = false;
      });

      _startAutoScroll();
    } catch (e) {
      setState(() {
        _isLoading = false;
        _hasError = true;
        _errorMessage = 'Không thể tải dữ liệu: $e';
      });
    }
  }

  @override
  void initState() {
    super.initState();
    loadData();
  }

  void _startAutoScroll() {
    if (_moviesNowPlaying.isNotEmpty) {
      _timer?.cancel();
      _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
        if (_pageController.hasClients) {
          int nextPage = (_currentCardPageNotifier.value + 1) % (_moviesNowPlaying.length > 5 ? 5 : _moviesNowPlaying.length);
          _pageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 500),
            curve: Curves.easeInOut,
          );
        }
      });
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    _selectedIndexNotifier.dispose();
    _currentCardPageNotifier.dispose();
    super.dispose();
  }

  Widget _buildTabItem(String title, int index) {
    return ValueListenableBuilder<int>(
      valueListenable: _selectedIndexNotifier,
      builder: (context, selectedIndex, child) {
        final bool isSelected = selectedIndex == index;
        return InkWell(
          onTap: () {
            _selectedIndexNotifier.value = index;
          },
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 12.0),
            child: Text(
              title,
              style: TextStyle(
                fontSize: 16,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                color: isSelected ? Colors.blue.shade700 : Colors.grey.shade500,
              ),
            ),
          ),
        );
      },
    );
  }

  List<String> getUniqueCities() {
    // Sử dụng _cinemas (dữ liệu gốc) thay vì _filteredCinemas
    final cities = _filteredCinemas.map((cinema) => cinema.city).toSet().toList();
    cities.insert(0, "Toàn quốc");
    return cities;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const SizedBox(height: 30),

            // Banner và chấm điều hướng
            Padding(
              padding: const EdgeInsets.all(10),
              child: Column(
                children: [
                  SizedBox(
                    height: 200,
                    child: _isLoading
                        ? Shimmer.fromColors(
                      baseColor: Colors.grey[300]!,
                      highlightColor: Colors.grey[100]!,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: 3,
                        itemBuilder: (context, index) {
                          return Container(
                            width: MediaQuery.of(context).size.width * 0.9,
                            margin: const EdgeInsets.symmetric(horizontal: 8.0),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(8),
                            ),
                          );
                        },
                      ),
                    )
                        : PageView.builder(
                      controller: _pageController,
                      onPageChanged: (index) {
                        _currentCardPageNotifier.value = index;
                      },
                      itemCount: _moviesNowPlaying.length > 5 ? 5 : _moviesNowPlaying.length,
                      itemBuilder: (context, index) {
                        final movie = _moviesNowPlaying[index];
                        return _buildBannerItem(movie);
                      },
                    ),
                  ),
                  const SizedBox(height: 10),
                  ValueListenableBuilder<int>(
                    valueListenable: _currentCardPageNotifier,
                    builder: (context, currentCardPage, child) {
                      return Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: List.generate(
                          _moviesNowPlaying.length > 5 ? 5 : _moviesNowPlaying.length,
                              (index) => buildIndicatorDot(currentCardPage == index),
                        ),
                      );
                    },
                  ),
                ],
              ),
            ),

            // Tab và Location
            Padding(
              padding: const EdgeInsets.only(
                top: 16.0,
                left: 12.0,
                right: 12.0,
                bottom: 8.0,
              ),
              child: Row(
                children: [
                  IntrinsicHeight(
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        _buildTabItem('Đang chiếu', 0),
                        VerticalDivider(
                          width: 16,
                          thickness: 1,
                          color: Colors.grey.shade300,
                        ),
                        _buildTabItem('Sắp chiếu', 1),
                      ],
                    ),
                  ),
                  const Spacer(),
                  TextButton.icon(
                    onPressed: () {
                      final cities = getUniqueCities();
                      showDialog(
                        context: context,
                        builder: (context) => AlertDialog(
                          title: const Text('Chọn khu vực'),
                          content: SingleChildScrollView(
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: cities.map((city) {
                                return ListTile(
                                  title: Text(city),
                                  onTap: () {
                                    Navigator.pop(context, city);
                                  },
                                );
                              }).toList(),
                            ),
                          ),
                        ),
                      ).then((selectedCity) {
                        if (selectedCity != null) {
                          setState(() {
                            _selectedLocation = selectedCity;
                            if (_selectedLocation == "Toàn quốc") {
                              _filteredCinemas = _cinemas;
                            } else {
                              _filteredCinemas = _cinemas.where((cinema) => cinema.city == _selectedLocation).toList();
                            }
                          });
                        }
                      });
                    },
                    style: TextButton.styleFrom(
                      padding: const EdgeInsets.symmetric(horizontal: 8.0),
                      tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      foregroundColor: Colors.blue.shade700,
                      backgroundColor: Colors.transparent,
                      splashFactory: NoSplash.splashFactory,
                    ),
                    icon: Icon(
                      Icons.location_on,
                      color: Colors.blue.shade700,
                      size: 18,
                    ),
                    label: Text(
                      _selectedLocation,
                      style: TextStyle(
                        color: Colors.blue.shade700,
                        fontSize: 14,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Danh sách phim
            ValueListenableBuilder<int>(
              valueListenable: _selectedIndexNotifier,
              builder: (context, selectedIndex, child) {
                return IndexedStack(
                  index: selectedIndex,
                  children: [
                    buildMoviesNowPlayingGrid(),
                    buildMoviesUpComingGrid(),
                  ],
                );
              },
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildBannerItem(Movie movie) {
    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => MovieDetailScreen(
              movie: movie,
              selectedLocation: _selectedLocation,
              cities: getUniqueCities(),
            ),
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 8.0),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8),
          image: DecorationImage(
            image: NetworkImage(movie.bannerUrl),
            fit: BoxFit.cover,
          ),
        ),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(8),
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Colors.transparent, Colors.black.withOpacity(0.7)],
            ),
          ),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  movie.name,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  'Khởi chiếu: ${DateFormat('dd/MM/yyyy').format(DateTime.parse(movie.releaseDate))}',
                  style: const TextStyle(color: Colors.white70, fontSize: 14),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget buildIndicatorDot(bool isActive) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),
      width: isActive ? 10 : 8,
      height: isActive ? 10 : 8,
      decoration: BoxDecoration(
        color: isActive ? Colors.black54 : Colors.grey,
        shape: BoxShape.circle,
      ),
    );
  }

  Widget buildMoviesUpComingGrid() {
    if (_isLoading) {
      return Padding(
        padding: const EdgeInsets.all(8.0),
        child: GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 4,
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.65,
            crossAxisSpacing: 10,
            mainAxisSpacing: 10,
          ),
          itemBuilder: (context, index) {
            return Shimmer.fromColors(
              baseColor: Colors.grey[300]!,
              highlightColor: Colors.grey[100]!,
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            );
          },
        ),
      );
    }
    if (_moviesUpComing.isEmpty) {
      return const Center(child: Text("Không có phim sắp chiếu"));
    }
    return Padding(
      padding: const EdgeInsets.all(8.0),
      child: GridView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _moviesUpComing.length,
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          childAspectRatio: 0.65,
          crossAxisSpacing: 10,
          mainAxisSpacing: 10,
        ),
        itemBuilder: (context, index) {
          final movie = _moviesUpComing[index];
          return MovieCard(
            movie: movie,
            selectedLocation: _selectedLocation,
            cities: getUniqueCities(),
          );
        },
      ),
    );
  }

  Widget buildMoviesNowPlayingGrid() {
    if (_isLoading) {
      return Padding(
        padding: const EdgeInsets.all(8.0),
        child: GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 4,
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.65,
            crossAxisSpacing: 10,
            mainAxisSpacing: 10,
          ),
          itemBuilder: (context, index) {
            return Shimmer.fromColors(
              baseColor: Colors.grey[300]!,
              highlightColor: Colors.grey[100]!,
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            );
          },
        ),
      );
    }
    if (_moviesNowPlaying.isEmpty) {
      return const Center(child: Text("Không có phim đang chiếu"));
    }
    if (_hasError) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(_errorMessage),
            const SizedBox(height: 10),
            ElevatedButton(
              onPressed: loadData,
              child: const Text('Thử lại'),
            ),
          ],
        ),
      );
    }
    return Padding(
      padding: const EdgeInsets.all(8.0),
      child: GridView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _moviesNowPlaying.length,
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          childAspectRatio: 0.65,
          crossAxisSpacing: 10,
          mainAxisSpacing: 10,
        ),
        itemBuilder: (context, index) {
          final movie = _moviesNowPlaying[index];
          return MovieCard(
            movie: movie,
            selectedLocation: _selectedLocation,
            cities: getUniqueCities(),
          );
        },
      ),
    );
  }
}

class MovieCard extends StatelessWidget {
  final Movie movie;
  final String selectedLocation;
  final List<String> cities;

  const MovieCard({super.key, required this.movie, required this.selectedLocation, required this.cities});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => MovieDetailScreen(
              movie: movie,
              selectedLocation: selectedLocation,
              cities: cities,
            ),
          ),
        );
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Stack(
              fit: StackFit.expand,
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: Image.network(
                    movie.posterUrl,
                    fit: BoxFit.cover,
                    frameBuilder: (context, child, frame, wasSynchronouslyLoaded) {
                      if (wasSynchronouslyLoaded) return child;
                      return AnimatedOpacity(
                        opacity: frame == null ? 0 : 1,
                        duration: const Duration(seconds: 1),
                        curve: Curves.easeOut,
                        child: child,
                      );
                    },
                    errorBuilder: (context, error, stackTrace) {
                      return Container(
                        decoration: BoxDecoration(
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Center(
                          child: Icon(
                            Icons.movie_creation_outlined,
                            color: Colors.grey[400],
                            size: 40,
                          ),
                        ),
                      );
                    },
                  ),
                ),
                Positioned(
                  bottom: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                    decoration: BoxDecoration(
                      color: Colors.orange,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.star, color: Colors.white, size: 14),
                        const SizedBox(width: 3),
                        Text(
                          '${movie.voteAverage}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.redAccent,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      '${movie.ageRating}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 6),
          Text(
            movie.name,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          ),
        ],
      ),
    );
  }
}