import 'package:frontendapp/models/user.dart';

class AppConfig{
  static final String BASEURL = 'http://10.0.2.2:8080'; // Thay bằng IP của máy tính
  static bool isLogin = false;
  static User? currentUser;
}