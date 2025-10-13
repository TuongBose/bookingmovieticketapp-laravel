import 'package:flutter/material.dart';
import 'package:frontendapp/app_config.dart';
import 'package:frontendapp/screens/cinema_screen.dart';
import 'package:frontendapp/screens/home_screen.dart';
import 'package:frontendapp/screens/movie_news_screen.dart';
import 'package:frontendapp/screens/user_screen.dart';
import 'package:frontendapp/screens/account_screen.dart';
import 'package:intl/date_symbol_data_local.dart';

class DefaultScreen extends StatelessWidget {
  final int initialIndex;

  const DefaultScreen({super.key, this.initialIndex = 0});

  @override
  Widget build(BuildContext context) {
    return MyDefaultScreen(initialIndex: initialIndex);
  }
}

class MyDefaultScreen extends StatefulWidget {
  final int initialIndex;

  const MyDefaultScreen({super.key, this.initialIndex = 0});

  @override
  State<MyDefaultScreen> createState() => MyDefaultScreenState();
}

class MyDefaultScreenState extends State<MyDefaultScreen> {
  late int _selectedIndex;

  @override
  void initState() {
    super.initState();
    initializeDateFormatting('vi_VN', null).then((_) {
      setState(() {});
    });

    // Đặt _selectedIndex ban đầu từ initialIndex
    _selectedIndex = widget.initialIndex;
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> widgetOptions = <Widget>[
      const HomeScreen(),
      CinemaScreen(),
      const MovieNewsScreen(),
      AppConfig.isLogin ? const UserScreen() : const AccountScreen(),
    ];

    return Scaffold(
      body: IndexedStack(
        index: _selectedIndex,
        children: widgetOptions,
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: const <BottomNavigationBarItem>[
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            label: 'Trang chủ',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.business_sharp),
            label: 'Rạp phim',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.newspaper),
            label: 'Điện ảnh',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.account_circle_outlined),
            label: 'Tài khoản',
          ),
        ],
        currentIndex: _selectedIndex,
        selectedItemColor: Colors.blueAccent,
        unselectedItemColor: Colors.grey,
        onTap: _onItemTapped,
        showUnselectedLabels: true,
      ),
    );
  }
}