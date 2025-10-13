class Cast{
  final int id;
  final int movieId;
  final String actorName;

  Cast({
    required this.id,
    required this.movieId,
    required this.actorName,
  });

  factory Cast.fromJson(Map<String, dynamic> json) => Cast(
    id: json['id'],
    movieId: json['movieId'],
    actorName: json['actorname'],
  );
}