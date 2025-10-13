import 'dart:convert';
import 'package:http/http.dart' as http;
import '../app_config.dart';
import '../models/room.dart';
import '../models/seat.dart';

class RoomService {
  Future<Room> getRoomById(int roomId) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/rooms/$roomId');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Room.fromJson(data);
      } else {
        throw Exception('Failed to load room: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching room: $e');
    }
  }

  Future<List<Seat>> getSeatsByRoomId(int roomId) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/rooms/$roomId/seats');
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Seat.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load seats: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching seats: $e');
    }
  }
}