import '../models/movie_news.dart';

class MovieNewsService {
  final String apiKey = '5fffce961921b470c26eb34749b33ce4';

  // Hàm lấy danh sách tin tức điện ảnh
  Future<List<MovieNews>> getMovieNews() async {
    // Giả lập dữ liệu (có thể thay bằng API thực )
    await Future.delayed(const Duration(seconds: 1));

    // Dữ liệu mẫu với movieId
    List<Map<String, dynamic>> mockNewsData = [
      {
        'id': 1,
        'title': '[Review] Địa Đạo Mặt Trời Trong Bóng Tối: Bản Anh Hùng Ca Kỉ Niệm 50 Năm Thống Nhất Đất Nước',
        'image_url': 'https://www.galaxycine.vn/media/2025/4/5/dia-dao-mat-troi-trong-bong-toi-ban-anh-hung-ca-ki-niem-50-nam-thong-nhat-dat-nuoc-4_1743859602604.jpg',
        'type': 'review',
        'publish_date': '2025-04-10',
        'movie_id': 12345,
      },
      {
        'id': 2,
        'title': '[Review] Âm Dương Lộ: Tốn Vinh Tài Xế Xe Cứu Thương Thông Qua Truyền Thuyết Đô Thị',
        'image_url': 'https://image.tienphong.vn/w1966/Uploaded/2025/ttf-cgkztztg/2025_03_28/soft-brown-minimalist-makeup-look-magazine-cover-1500-x-1000-px-2-4836-934.png',
        'type': 'review',
        'publish_date': '2025-04-09',
        'movie_id': 67890,
      },
      {
        'id': 3,
        'title': 'Bồi Thêu Chuyện Về 11 Năm Tâm Huyết Với Địa Đạo: Mặt Trời Trong Bóng Tối',
        'image_url': 'https://www.galaxycine.vn/media/2025/3/14/1135-3_1741937948229.jpg',
        'type': 'article',
        'publish_date': '2025-04-08',
        'movie_id': 12345,
      },
      {
        'id': 4,
        'title': 'Tổng Hợp Oscar 2025: Arora Thắng Lớn',
        'image_url': 'https://www.galaxycine.vn/media/2025/3/3/oscars-2025_1740991345175.jpg',
        'type': 'news',
        'publish_date': '2025-04-07',
        'movie_id': null,
      },
      {
        'id': 5,
        'title': '[Review] Mufasa: Vua Sư Tử - Hành Trình Trở Thành Huyền Thoại',
        'image_url': 'https://i.imgur.com/ssPAvfF.jpg',
        'type': 'review',
        'publish_date': '2025-04-06',
        'movie_id': 54321,
      },
      {
        'id': 6,
        'title': 'Phim Việt Chiếu Tết 2025: Cuộc Đua Của Những Tên Tuổi Lớn',
        'image_url': 'https://image.nhandan.vn/Uploaded/2025/genaghlrgybna/2025_01_30/nuhon-6830-2842.jpg',
        'type': 'news',
        'publish_date': '2025-04-05',
        'movie_id': null,
      },
      {
        'id': 7,
        'title': '[Review] Hành Tinh Cát: Phần 3 - Đỉnh Cao Của Khoa Học Viễn Tưởng',
        'image_url': 'https://www.galaxycine.vn/media/2024/2/15/dune-1_1707970610764.jpg',
        'type': 'review',
        'publish_date': '2025-04-04',
        'movie_id': 98765,
      },
      {
        'id': 8,
        'title': 'Diễn Viên Trẻ Việt Nam Nổi Bật Nhất 2025: Ai Sẽ Lên Ngôi?',
        'image_url': 'https://vcdn1-giaitri.vnecdn.net/2025/01/26/bo-tu-bao-thu-review-3-1737896-5541-4674-1737897722.jpg?w=0&h=0&q=100&dpr=2&fit=crop&s=PE_tl8ujO85EyCpRXCx0Fg',
        'type': 'article',
        'publish_date': '2025-04-03',
        'movie_id': null,
      },
      {
        'id': 9,
        'title': 'LHP Cannes 2025: Phim Việt Gây Ấn Tượng Mạnh',
        'image_url': 'https://i1-giaitri.vnecdn.net/2024/09/04/sic39-20240903-film-dontcrybut-9555-5479-1725438359.jpg?w=680&h=0&q=100&dpr=2&fit=crop&s=IkVzSLQz_BeklT9SubnjHQ',
        'type': 'news',
        'publish_date': '2025-04-02',
        'movie_id': null,
      },
      {
        'id': 10,
        'title': '[Review] Kẻ Trộm Mặt Trăng 4: Hài Hước Nhưng Thiếu Sáng Tạo',
        'image_url': 'https://i1-giaitri.vnecdn.net/2024/07/10/Copy-of-03-2912-1720597518.jpg?w=680&h=0&q=100&dpr=2&fit=crop&s=nw3KTIKk9AIiFO51_3flyg',
        'type': 'review',
        'publish_date': '2025-04-01',
        'movie_id': 45678,
      },
      {
        'id': 11,
        'title': 'Phim Siêu Anh Hùng 2025: Marvel Có Lấy Lại Phong Độ?',
        'image_url': 'https://thethaovanhoa.mediacdn.vn/372676912336973824/2025/5/4/super1-17463247857631628319004.jpg',
        'type': 'news',
        'publish_date': '2025-03-31',
        'movie_id': null,
      },
      {
        'id': 12,
        'title': 'Hậu Trường Phim Địa Đạo: Những Bí Mật Chưa Từng Tiết Lộ',
        'image_url': 'https://kenh14cdn.com/203336854389633024/2025/4/4/30-1743747599346527576346-1743754490476-17437544905901596481572.jpg',
        'type': 'article',
        'publish_date': '2025-03-30',
        'movie_id': 12345,
      },
    ];

    return mockNewsData.map((data) => MovieNews.fromJson(data)).toList();
  }

  // Hàm lấy tin tức theo loại (review, news, article)
  Future<List<MovieNews>> getMovieNewsByType(String type) async {
    final allNews = await getMovieNews();
    return allNews.where((news) => news.type == type).toList();
  }

  // Hàm lấy danh sách người nổi tiếng (tab Nhân vật)
  Future<List<Map<String, dynamic>>> getCelebrities() async {
    await Future.delayed(const Duration(seconds: 1));
    return [
      {
        'name': 'Chris Evans',
        'image_url': 'https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcS2mP36NECnrEedO_w0MzrWuGk0JjKu1-QHVk6h9R6ROcqfZXLDEIelBQw-k49wkOPMDIBGf1ONtt8zdf5p_94OZA',
        'description': 'Khác với Chris Hemsworth vẫn đang loay hoay trong hình tượng vị thần sấm sét, Chris Evans đã tìm được hướng đi mới sau Captain America...',
      },
      {
        'name': 'Margot Robbie',
        'image_url': 'https://hips.hearstapps.com/hmg-prod/images/margot-robbie-attends-the-new-york-premiere-of-asteroid-news-photo-1689698979.jpg?crop=0.733xw:0.489xh;0.107xw,0.0363xh&resize=640:*',
        'description': 'Nữ diễn viên tài năng của Hollywood, nổi tiếng với vai Harley Quinn và các dự án sản xuất ấn tượng...',
      },
      {
        'name': 'Trần Nghĩa',
        'image_url': 'https://images2.thanhnien.vn/528068263637045248/2023/11/9/edit-tran-nghia-16995093917121977605904.jpeg',
        'description': 'Nam diễn viên trẻ của Việt Nam, gây ấn tượng mạnh với vai chính trong Địa Đạo Mặt Trời Trong Bóng Tối...',
      },
      {
        'name': 'Zendaya',
        'image_url': 'https://media.cnn.com/api/v1/images/stellar/prod/211029165028-18-zendaya-style-evolution.jpg?q=w_1110,c_fill',
        'description': 'Ngôi sao trẻ của Hollywood, nổi bật trong Hành Tinh Cát: Phần 3 và các dự án thời trang đình đám...',
      },
      {
        'name': 'Đạo diễn Trấn Thành',
        'image_url': 'https://danviet.ex-cdn.com/files/f1/296231569849192448/2024/2/23/z5180799307234ad1a44a4fce5ad957bc49ff242b1e340-1708656051384-17086560516351592527566.jpg',
        'description': 'Đạo diễn kiêm diễn viên Việt Nam, tiếp tục ghi dấu ấn với các dự án phim Tết 2025...',
      },
      {
        'name': 'Timothée Chalamet',
        'image_url': 'https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcSaArcSwlCyTRE5l6DPnKSLud9-S-qWHVjoYq6huDWwRvTG0xj0I0_66iColRkkqZmOy0fvNAyUzXz0NraJMl-mLQ',
        'description': 'Diễn viên trẻ tài năng, tiếp tục gây sốt với vai diễn trong các bộ phim nghệ thuật tại Cannes 2025...',
      },
      {
        'name': 'Ngô Thanh Vân',
        'image_url': 'https://saigonsao.com.vn/uploads/tieu-su/tieu-su-ngo-thanh-van-3.jpg',
        'description': 'Nữ diễn viên và nhà sản xuất Việt Nam, người đứng sau thành công của nhiều dự án phim Việt quốc tế...',
      },
      {
        'name': 'Anya Taylor-Joy',
        'image_url': 'https://bazaarvietnam.vn/wp-content/uploads/2024/07/harper-bazaar-cac-phim-va-chuong-trinh-truyen-hinh-co-su-tham-gia-cua-anya-taylor-joy-anyataylorjo.jpg',
        'description': 'Nữ diễn viên nổi tiếng với vai diễn trong các bộ phim kinh dị và tâm lý, được săn đón tại Hollywood...',
      },
    ];
  }
}