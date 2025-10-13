import 'dart:convert';
import 'package:http/http.dart' as http;
import '../app_config.dart';
import 'package:frontendapp/models/bookingdetail.dart';

import '../dtos/BookingDetailDTO.dart';

class BookingDetailService{
  Future<void> createBookingDetail(BookingDetailDTO bookingDetailDTO) async {
    try {
      final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookingdetails');
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json; charset=UTF-8',
          'Accept': 'application/json; charset=UTF-8',
        },
        body: jsonEncode(bookingDetailDTO.toJson()),
      );
      if (response.statusCode != 200) {
        throw Exception('Failed to create booking detail: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error creating booking detail: $e');
    }
  }

  Future<List<BookingDetail>> getBookingDetailsByBookingId(int bookingId) async {
    final url = Uri.parse('${AppConfig.BASEURL}/api/v1/bookingdetails/$bookingId/details');
    try {
      final response = await http.get(
        url,
        headers: {'Accept': 'application/json; charset=UTF-8'},
      );
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => BookingDetail.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load booking details: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching booking details: $e');
    }
  }
}