@extends('layouts.app')

@section('content')
<div class="home-page">

    {{-- 🎁 KHU VỰC CHÀO MỪNG --}}
    @auth
    <div class="container mt-4 mb-4">
        <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm p-3 rounded-lg">
            <span class="fw-bold fs-5 text-dark">
                Xin chào, <strong class="text-primary">{{ Auth::user()->name }}</strong>!
            </span>

            <form action="{{ route('logout') }}" method="POST" class="m-0 d-inline-block">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </div>

        @if(Auth::user()->rolename == 1)
        <div class="alert alert-warning text-center">
            Bạn đang đăng nhập với vai trò: <strong>Quản trị viên</strong>.
        </div>
        @endif
    </div>
    @endauth

    {{-- 🎞️ BANNER --}}
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            @forelse ($nowPlaying as $index => $movie)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ $movie['bannerurl'] }}" class="d-block w-100" alt="{{ $movie['name'] }}">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-3 rounded">
                    <h5>{{ $movie['name'] }}</h5>
                    <p>{{ Str::limit($movie['description'] ?? 'Đang cập nhật...', 100) }}</p>
                    <a href="{{ url('/' . $movie['id']) }}" class="btn btn-warning">Đặt vé ngay</a>
                </div>
            </div>
            @empty
            <div class="carousel-item active">
                <img src="https://placehold.co/1920x600/3B0066/FFFFFF?text=Không+có+phim+đang+chiếu" class="d-block w-100">
                <div class="carousel-caption">
                    <h5>Phim đang chiếu</h5>
                    <p>Dữ liệu đang cập nhật...</p>
                </div>
            </div>
            @endforelse

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    {{-- 🍿 PHIM ĐANG CHIẾU --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4 text-white fw-bold">PHIM ĐANG CHIẾU</h2>

        <div class="row g-4">
            @forelse ($nowPlaying as $movie)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="cinestar-movie-card position-relative overflow-hidden rounded-3 shadow-lg h-100">

                    <a href="{{ url('/' . $movie['id']) }}" class="text-decoration-none d-block">

                        {{-- POSTER --}}
                        <div class="poster position-relative">
                            <img src="{{ $movie['posterurl'] }}" class="w-100 h-100 rounded-3" style="object-fit: cover;" loading="lazy">

                            <div class="age-badge-new position-absolute top-0 start-0 m-2 d-flex overflow-hidden">
                                <div class="badge-right bg-dark text-warning fw-bold px-2 text-uppercase">
                                    {{ $movie['agerating'] ?? 'P' }}
                                </div>
                            </div>

                            <div class="play-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-70 opacity-0 transition">
                                <i class="fa fa-play-circle text-warning fa-4x"></i>
                            </div>
                        </div>

                        {{-- INFO --}}
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
            @empty
            <p class="text-light">Hiện tại không có phim nào đang chiếu.</p>
            @endforelse
        </div>
    </section>

    {{-- 🎟️ PHIM SẮP CHIẾU --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4 text-white fw-bold">PHIM SẮP CHIẾU</h2>

        <div class="row g-4">
            @forelse ($upComing as $movie)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="cinestar-movie-card position-relative overflow-hidden rounded-3 shadow-lg h-100">

                    <a href="{{ url('/' . $movie['id']) }}" class="text-decoration-none d-block">

                        <div class="poster position-relative">
                            <img src="{{ $movie['posterurl'] }}" class="w-100 h-100 rounded-3" style="object-fit: cover;" loading="lazy">

                            <div class="age-badge-new position-absolute top-0 start-0 m-2 d-flex overflow-hidden">
                                <div class="badge-right bg-dark text-warning fw-bold px-2 text-uppercase">
                                    {{ $movie['agerating'] ?? 'P' }}
                                </div>
                            </div>

                            <div class="play-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-70 opacity-0 transition">
                                <i class="fa fa-play-circle text-warning fa-4x"></i>
                            </div>
                        </div>

                        <div class="info p-3 text-center">
                            <h6 class="movie-title text-white fw-bold text-uppercase mb-1 transition">
                                {{ Str::limit($movie['name'], 40) }}
                            </h6>

                            <p class="movie-date text-muted small mb-3 transition">
                                {{ \Carbon\Carbon::parse($movie['releasedate'])->format('d/m/Y') }}
                            </p>

                            <a href="{{ url('/' . $movie['id']) }}" class="btn-datve btn btn-sm fw-bold rounded-pill px-4">
                                XEM CHI TIẾT
                            </a>
                        </div>

                    </a>

                </div>
            </div>
            @empty
            <p class="text-light">Không có phim sắp chiếu.</p>
            @endforelse
        </div>

    </section>

</div>
@endsection
@section('styles')
<style>

.text-muted {
    color: #b0b0b0 !important;
}


.cinestar-movie-card {
    height: 570px; /* cố định */
    background: #111829;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s ease;
    display: flex;
    flex-direction: column;
}

/* HOVER */
.cinestar-movie-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 25px 45px rgba(255, 193, 7, 0.18) !important;
}

/* ============================
   POSTER
   ============================ */
.poster {
    height: 420px; /* cố định */
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

/* Overlay play icon */
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
    padding: 10px;
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
    margin: 2px 0 6px 0;
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
.home-page{
    padding: 10px;
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

