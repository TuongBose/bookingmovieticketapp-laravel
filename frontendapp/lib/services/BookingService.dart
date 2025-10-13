import 'dart:convert';
import 'package:http/http.dart' as http;
import '../app_config.dart';
import '../dtos/BookingDTO.dart';
import '../models/booking.dart';

class BookingService {
  Future<int> createBooking(BookingDTO bookingDTO) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookings');
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json; charset=UTF-8',
          'Accept': 'application/json; charset=UTF-8',
        },
        body: jsonEncode(bookingDTO.toJson()),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['id']; // Giả sử API trả về ID của booking vừa tạo
      } else {
        throw Exception('Failed to create booking: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error creating booking: $e');
    }
  }

  Future<List<Booking>> getBookingsByShowtimeId(int showtimeId) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookings/showtimes/$showtimeId/bookings');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Booking.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load bookings: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching bookings: $e');
    }
  }

  Future<int> sumTotalPriceByUserId(int id) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookings/users/$id/totalprice');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final int data = int.parse(response.body);
        return data;
      } else {
        throw Exception('Failed to load totalprice: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching totalprice: $e');
    }
  }
  Future<List<Booking>> getBookingByUserId(int userId) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookings/users/$userId/bookings');
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Booking.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load Booking: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching booking: $e');
      return [];
    }
  }

}