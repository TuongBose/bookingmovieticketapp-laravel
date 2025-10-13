import 'dart:convert';

import 'package:frontendapp/models/showtime.dart';
import 'package:http/http.dart' as http;

import '../app_config.dart';

class ShowTimeService {
  Future<Showtime?> getShowtimeById(int showtimeId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/showtimes/$showtimeId');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Showtime.fromJson(data);
      } else {
        print('Failed to load showtime $showtimeId: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error fetching showtime $showtimeId: $e');
      return null;
    }
  }
}