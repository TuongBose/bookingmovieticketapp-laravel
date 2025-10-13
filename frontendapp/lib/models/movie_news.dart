class MovieNews {
  final int id;
  final String title;
  final String imageUrl;
  final String type;
  final String? publishDate;
  final String? content;
  final int? movieId; // Thêm trường movieId để liên kết với phim

  MovieNews({
    required this.id,
    required this.title,
    required this.imageUrl,
    required this.type,
    this.publishDate,
    this.content,
    this.movieId,
  });

  factory MovieNews.fromJson(Map<String, dynamic> json) {
    return MovieNews(
      id: json['id'],
      title: json['title'],
      imageUrl: json['image_url'],
      type: json['type'],
      publishDate: json['publish_date'],
      content: json['content'],
      movieId: json['movie_id'],
    );
  }
}