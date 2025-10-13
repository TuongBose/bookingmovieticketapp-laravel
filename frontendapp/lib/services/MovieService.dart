import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:frontendapp/app_config.dart';
import '../models/movie.dart';

class MovieService {
  Future<List<Movie>> _fetchMovies(String endpoint) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/movies/$endpoint');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        // Giải mã dữ liệu với UTF-8
        final decodedBody = utf8.decode(response.bodyBytes);
        final List<dynamic> data = jsonDecode(decodedBody);
        return data.map((json) => Movie.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load movies from $endpoint: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching movies from $endpoint: $e');
    }
  }

  Future<List<Movie>> getNowPlaying() async {
    return await _fetchMovies('nowplaying');
  }

  Future<List<Movie>> getUpComing() async {
    return await _fetchMovies('upcoming');
  }

  Future<List<Movie>> getSimilarMovies(int movieId) async {
    return await _fetchMovies('similar/$movieId');
  }

  Future<Movie?> getMovieById(int movieId) async {
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
}