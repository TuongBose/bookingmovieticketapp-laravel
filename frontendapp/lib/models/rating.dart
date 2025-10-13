class Rating {
  final int? id;
  final int movieId;
  final int userId;
  final int rating;
  final String comment;
  final String createdAt;

  Rating({
    this.id,
    required this.movieId,
    required this.userId,
    required this.rating,
    required this.comment,
    required this.createdAt,
  });

  factory Rating.fromMap(Map<String, dynamic> map) => Rating(
    id: map['ID'],
    movieId: map['MOVIEID'],
    userId: map['USERID'],
    rating: map['RATING'],
    comment: map['COMMENT'],
    createdAt: map['CREATEDAT'],
  );

  Map<String, dynamic> toMap() => {
    'ID': id,
    'MOVIEID': movieId,
    'USERID': userId,
    'RATING': rating,
    'COMMENT': comment,
    'CREATEDAT': createdAt,
  };
}
