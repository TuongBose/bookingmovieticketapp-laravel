@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . ($keyword ?? 'Phim'))

@section('content')
<div class="search-results-page">
    <div class="container py-5">

        <!-- TIÊU ĐỀ -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-uppercase text-white mb-3" style="letter-spacing: 2px;">
                KẾT QUẢ TÌM KIẾM PHIM
            </h1>

            <p class="fs-5 text-light">
                @if(!empty($keyword))
                    Tìm thấy <strong class="text-warning">{{ $movies->count() }}</strong> phim cho từ khóa 
                    <span class="text-info">“{{ $keyword }}”</span>
                @else
                    <span class="text-warning">Bạn chưa nhập từ khóa tìm kiếm</span>
                @endif
            </p>
        </div>

        <!-- CHƯA NHẬP TỪ KHÓA -->
        @if(empty($keyword))
            <div class="text-center py-5">
                <i class="fa fa-search fa-5x text-muted mb-4 opacity-50"></i>
                <h3 class="text-white">Hãy nhập tên phim để tìm kiếm</h3>
                <p class="text-light mb-4">
                    Ví dụ: <strong>Avengers</strong>, <strong>Sư Thầy</strong>, <strong>Nhà Có 5 Nàng Tiên</strong>
                </p>
                <a href="{{ url('/') }}" class="btn btn-outline-light px-4 rounded-pill">
                    <i class="fa fa-home me-2"></i>Quay về trang chủ
                </a>
            </div>

        <!-- KHÔNG CÓ KẾT QUẢ -->
        @elseif($movies->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-film fa-5x text-muted mb-4 opacity-50"></i>
                <h3 class="text-white">Không tìm thấy phim nào</h3>
                <p class="text-light">
                    Không có kết quả cho "<strong class="text-info">{{ $keyword }}</strong>"
                </p>
                <a href="{{ route('search.page') }}" class="btn btn-outline-warning px-4 rounded-pill">
                    <i class="fa fa-sync me-2"></i>Thử lại với từ khóa khác
                </a>
            </div>

        <!-- HIỂN THỊ KẾT QUẢ -->
        @else
            <div class="row g-4 justify-content-center">
                @foreach($movies as $movie)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="cinestar-movie-card position-relative overflow-hidden rounded-3 shadow-lg h-100">
                            <a href="{{ url('/' . $movie['id']) }}" class="text-decoration-none d-block">

                                <!-- POSTER -->
                                <div class="poster position-relative">
                                    <img 
                                        src="{{ $movie->posterurl ?? asset('images/no-poster.png') }}" 
                                        alt="{{ $movie->name }}"
                                        class="w-100 h-100 rounded-3"
                                        style="object-fit: cover;"
                                        loading="lazy"
                                    >

                                    <!-- NHÃN ĐỘ TUỔI -->
                                    <div class="age-badge-new position-absolute top-0 start-0 m-2 d-flex overflow-hidden">
                                        <div class="badge-right bg-dark text-warning fw-bold d-flex align-items-center justify-content-center px-2 text-uppercase">
                                            {{ $movie->agerating ?? 'P' }}
                                        </div>
                                    </div>

                                    <!-- OVERLAY PLAY -->
                                    <div class="play-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-70 opacity-0 transition">
                                        <i class="fa fa-play-circle text-warning fa-4x"></i>
                                    </div>
                                </div>

                                <!-- THÔNG TIN PHIM -->
                                <div class="info p-3 text-center">
                                    <h6 class="movie-title text-white fw-bold text-uppercase mb-1 transition">
                                        {{ Str::limit($movie['name'], 40) }}
                                    </h6>
                                    <p class="movie-date text-muted small mb-3 transition">
                                        {{ $movie['duration'] }} phút
                                    </p>

                                    <a href="{{ url('/' . $movie['id']) }}" class="btn-datve btn btn-sm fw-bold rounded-pill px-4">
                                        ĐẶT VÉ NGAY
                                    </a>
                                </div>

                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection

@section('styles')
<style>


.text-muted {
    color: #b0b0b0 !important;
}
.cinestar-movie-card {
    height: 570px; 
    background: #111829;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s ease;
    display: flex;
    flex-direction: column;
}

/* Hover */
.cinestar-movie-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 25px 45px rgba(255, 193, 7, 0.18) !important;
}

/* ============================
   POSTER
   ============================ */
.poster {
    height: 420px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.cinestar-movie-card:hover .poster img {
    transform: scale(1.07);
}

/* Overlay play */
.play-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.65);
    opacity: 0;
    transition: 0.3s ease;
}
.cinestar-movie-card:hover .play-overlay {
    opacity: 0.7 !important;
}

/* ============================
   INFO
   ============================ */
.info {
    height: 150px;
    background: #111829;
    padding: 10px 10px 14px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.movie-title {
    font-size: 0.92rem;
    font-weight: 600;
    height: 45px;
    overflow: hidden;
    color: #fff;
    text-transform: uppercase;
    transition: 0.3s ease;
}

.movie-date {
    font-size: 0.78rem;
    color: #9aa0b5;
    transition: 0.3s ease;
    margin-top: -4px; /* kéo gần lên */
    margin-bottom: 6px; /* giữ đẹp */
}

/* Hover đổi màu */
.cinestar-movie-card:hover .movie-title {
    color: #ffc107 !important;
}

.cinestar-movie-card:hover .movie-date {
    color: #ffffff !important;
}

/* ============================
   AGE BADGE
   ============================ */
.age-badge-new {
    height: 22px;
    border-radius: 6px;
    overflow: hidden;
    font-size: 0.65rem;
    z-index: 3;
}
.age-badge-new .badge-right {
    min-width: 28px;
    background: #1a1a1a !important;
    color: #ffc107 !important;
}

/* ============================
   BUTTON ĐẶT VÉ
   ============================ */
.btn-datve {
    min-width: 100px;
    padding: 8px 26px;
    border-radius: 40px;
    background: linear-gradient(135deg, #ffc107, #ff9f1c) !important;
    color: #000 !important;
    font-weight: 700;
    border: none;
    transition: 0.3s ease;
}

.btn-datve:hover {
    background: linear-gradient(135deg, #ffcd39, #ffb52c) !important;
    transform: scale(1.07);
    color: #000 !important;
}

/* ============================
   RESPONSIVE
   ============================ */
@media (max-width: 768px) {
    .cinestar-movie-card {
        height: 330px;
    }
    .poster {
        height: 210px;
    }
}

@media (max-width: 480px) {
    .cinestar-movie-card {
        height: 320px;
    }
    .poster {
        height: 200px;
    }
}

</style>
@endsection
