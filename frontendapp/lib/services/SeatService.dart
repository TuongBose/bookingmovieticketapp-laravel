import 'dart:convert';

import 'package:frontendapp/models/seat.dart';
import 'package:http/http.dart' as http;

import '../app_config.dart';

class SeatService{
  Future<Seat> getSeatById(int seatId) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/seats/$seatId');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Seat.fromJson(data);
      } else {
        throw Exception('Failed to load seat: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching seat: $e');
    }
  }
}