import 'package:flutter/material.dart';
import '../models/movie_news.dart';
import '../services/movie_news_service.dart';
import 'news_detail_screen.dart'; // Add this import

class MovieNewsScreen extends StatefulWidget {
  const MovieNewsScreen({Key? key}) : super(key: key);

  @override
  State<MovieNewsScreen> createState() => _MovieNewsScreenState();
}

class _MovieNewsScreenState extends State<MovieNewsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final MovieNewsService _newsService = MovieNewsService();
  List<MovieNews> movieNewsList = [];
  List<Map<String, dynamic>> celebrities = [];
  List<MovieNews> filteredNewsList = [];
  List<Map<String, dynamic>> filteredCelebrities = [];
  bool isLoading = true;
  String searchQuery = '';
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _fetchData();
    _searchController.addListener(() {
      setState(() {
        searchQuery = _searchController.text;
        _filterData();
      });
    });
  }

  Future<void> _fetchData() async {
    try {
      final news = await _newsService.getMovieNews();
      final celebs = await _newsService.getCelebrities();

      setState(() {
        movieNewsList = news;
        celebrities = celebs;
        filteredNewsList = news;
        filteredCelebrities = celebs;
        isLoading = false;
      });
    } catch (e) {
      setState(() {
        isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Lỗi khi tải dữ liệu: $e')),
      );
    }
  }

  void _filterData() {
    setState(() {
      if (searchQuery.isEmpty) {
        filteredNewsList = movieNewsList;
        filteredCelebrities = celebrities;
      } else {
        filteredNewsList = movieNewsList.where((news) {
          return news.title.toLowerCase().contains(searchQuery.toLowerCase());
        }).toList();

        filteredCelebrities = celebrities.where((celebrity) {
          return celebrity['name'].toLowerCase().contains(searchQuery.toLowerCase()) ||
              celebrity['description'].toLowerCase().contains(searchQuery.toLowerCase());
        }).toList();
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Điện Ảnh',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            fontSize: 20,
          ),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              showSearch(
                context: context,
                delegate: NewsSearchDelegate(
                  newsList: movieNewsList,
                  celebrities: celebrities,
                  onSearch: (query) {
                    setState(() {
                      searchQuery = query;
                      _filterData();
                    });
                  },
                ),
              );
            },
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold),
          tabs: const [
            Tab(text: 'Bình luận'),
            Tab(text: 'Tin tức'),
            Tab(text: 'Nhân vật'),
          ],
          labelColor: Colors.blue,
          unselectedLabelColor: Colors.grey,
          indicatorColor: Colors.blue,
        ),
        automaticallyImplyLeading: false,
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
        controller: _tabController,
        children: [
          _buildNewsListView(filterType: 'review'),
          _buildNewsListView(filterType: 'news'),
          _buildCelebritiesView(),
        ],
      ),
    );
  }

  Widget _buildNewsListView({String? filterType}) {
    List<MovieNews> filteredList = filterType != null
        ? filteredNewsList.where((news) => news.type == filterType).toList()
        : filteredNewsList;

    if (filteredList.isEmpty) {
      return const Center(child: Text('Không có dữ liệu'));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(8),
      itemCount: filteredList.length,
      itemBuilder: (context, index) {
        final news = filteredList[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          elevation: 2,
          clipBehavior: Clip.antiAlias,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              AspectRatio(
                aspectRatio: 16 / 9,
                child: Image.network(
                  news.imageUrl,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) {
                    return Container(
                      color: Colors.grey[300],
                      child: const Center(
                        child: Icon(Icons.image, size: 50, color: Colors.grey),
                      ),
                    );
                  },
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(12),
                child: Text(
                  news.title,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                  maxLines: 3,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(left: 12, right: 12, bottom: 12),
                child: TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => NewsDetailScreen(news: news),
                      ),
                    );
                  },
                  style: TextButton.styleFrom(
                    padding: EdgeInsets.zero,
                    alignment: Alignment.centerLeft,
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  ),
                  child: const Text(
                    'Đọc thêm',
                    style: TextStyle(
                      color: Colors.blue,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildCelebritiesView() {
    if (filteredCelebrities.isEmpty) {
      return const Center(child: Text('Không có dữ liệu'));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(8),
      itemCount: filteredCelebrities.length,
      itemBuilder: (context, index) {
        final celebrity = filteredCelebrities[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          elevation: 2,
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(4),
                  bottomLeft: Radius.circular(4),
                ),
                child: SizedBox(
                  width: 120,
                  height: 120,
                  child: Image.network(
                    celebrity['image_url'],
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) {
                      return Container(
                        color: Colors.grey[300],
                        child: const Center(
                          child: Icon(Icons.person, size: 40, color: Colors.grey),
                        ),
                      );
                    },
                  ),
                ),
              ),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        celebrity['name'],
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        celebrity['description'],
                        maxLines: 3,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 14,
                          color: Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        'Đọc thêm',
                        style: TextStyle(
                          color: Colors.blue,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

class NewsSearchDelegate extends SearchDelegate<String> {
  final List<MovieNews> newsList;
  final List<Map<String, dynamic>> celebrities;
  final Function(String) onSearch;

  NewsSearchDelegate({
    required this.newsList,
    required this.celebrities,
    required this.onSearch,
  });

  @override
  List<Widget>? buildActions(BuildContext context) {
    return [
      IconButton(
        icon: const Icon(Icons.clear),
        onPressed: () {
          query = '';
          onSearch(query);
        },
      ),
    ];
  }

  @override
  Widget? buildLeading(BuildContext context) {
    return IconButton(
      icon: const Icon(Icons.arrow_back),
      onPressed: () {
        close(context, '');
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    onSearch(query);
    return Container();
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    final suggestionList = query.isEmpty
        ? newsList
        : newsList.where((news) {
      return news.title.toLowerCase().contains(query.toLowerCase());
    }).toList();

    return ListView.builder(
      itemCount: suggestionList.length,
      itemBuilder: (context, index) {
        final news = suggestionList[index];
        return ListTile(
          title: Text(news.title),
          onTap: () {
            query = news.title;
            onSearch(query);
            close(context, query);
          },
        );
      },
    );
  }
}