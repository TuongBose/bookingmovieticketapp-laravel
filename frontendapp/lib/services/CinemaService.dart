import 'dart:convert';
import 'package:http/http.dart' as http;import '../app_config.dart';

import '../models/cinema.dart';

class CinemaService {
  Future<List<Cinema>> getCinemas() async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/cinemas');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Cinema.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load cinemas: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching cinemas: $e');
    }
  }

  Future<Cinema?> getCinemaById(int cinemaId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/cinemas/$cinemaId');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Cinema.fromJson(data);
      } else {
        print('Failed to load cinema $cinemaId: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error fetching cinema $cinemaId: $e');
      return null;
    }
  }

  String getCinemaImageUrl(int cinemaId, String? imageName) {
    if (imageName == null) {
      return ''; // Trả về rỗng nếu không có hình ảnh
    }
    return '${AppConfig.BASEURL}/api/v1/cinemas/$cinemaId/image';
  }
}