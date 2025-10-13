import 'package:flutter/material.dart';
import '../models/movie_news.dart';
import '../models/movie.dart';
import '../services/MovieService.dart';
import '../services/movie_news_service.dart';

class NewsDetailScreen extends StatelessWidget {
  final MovieNews news;
  final MovieNewsService _newsService = MovieNewsService();
  final MovieService _movieService = MovieService();

  NewsDetailScreen({Key? key, required this.news}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(news.title)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Image.network(
              news.imageUrl,
              errorBuilder: (context, error, stackTrace) {
                return Container(
                  height: 200,
                  color: Colors.grey,
                  child: const Icon(Icons.broken_image),
                );
              },
            ),
            const SizedBox(height: 16),
            Text(
              news.title,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Text(news.content ?? 'Nội dung đang cập nhật...'),
            const SizedBox(height: 12),
            Text(
              'Ngày đăng: ${news.publishDate ?? 'Không rõ'}',
              style: const TextStyle(fontStyle: FontStyle.italic, color: Colors.grey),
            ),
            const SizedBox(height: 24),
            // Phần Đọc thêm
            Text(
              'Đọc thêm',
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            // Kiểm tra nếu news có movieId thì gọi API lấy similar movies
            news.movieId != null
                ? FutureBuilder<List<Movie>>(
              future: _movieService.getSimilarMovies(news.movieId!), // Lấy phim tương tự
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                } else if (snapshot.hasError) {
                  return const Text('Lỗi khi tải dữ liệu');
                } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                  return const Text('Không có nội dung liên quan');
                }

                final relatedMovies = snapshot.data!;

                return SizedBox(
                  height: 200, // Chiều cao của danh sách cuộn ngang
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal, // Cuộn ngang
                    itemCount: relatedMovies.length,
                    itemBuilder: (context, index) {
                      final movie = relatedMovies[index];
                      return GestureDetector(
                        onTap: () {
                          // Điều hướng đến màn hình chi tiết phim (nếu có)
                          // Ví dụ: Navigator.push(context, MaterialPageRoute(builder: (context) => MovieDetailScreen(movie: movie)));
                        },
                        child: Container(
                          width: 150, // Chiều rộng của mỗi item
                          margin: const EdgeInsets.only(right: 12),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Hình ảnh phim
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
                              // Tên phim
                              Text(
                                movie.name,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                              ),
                              const SizedBox(height: 4),
                              // Ngày phát hành
                              Text(
                                movie.releaseDate,
                                style: const TextStyle(fontSize: 12, color: Colors.grey),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            )
                : const Text('Không có nội dung liên quan'), // Nếu không có movieId
          ],
        ),
      ),
    );
  }
}