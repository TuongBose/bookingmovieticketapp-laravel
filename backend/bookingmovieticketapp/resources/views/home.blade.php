@extends('layouts.app')

@section('content')
<div class="home-page">

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
                    <img src="https://placehold.co/1920x600/3B0066/FFFFFF?text=Không+có+phim+đang+chiếu"
                         class="d-block w-100">
                    <div class="carousel-caption">
                        <h5>Phim đang chiếu</h5>
                        <p>Dữ liệu đang cập nhật...</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 🍿 PHIM ĐANG CHIẾU --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4 text-white fw-bold">PHIM ĐANG CHIẾU</h2>
        <div class="row g-4">
            @forelse ($nowPlaying as $movie)
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
                            </div>
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

        {{-- Pagination Now Playing --}}
        @if ($nowPlaying->lastPage() > 1)
            <div class="d-flex justify-content-center align-items-center mt-4 gap-2">
                {{-- Prev --}}
                @if ($nowPlaying->onFirstPage())
                    <span class="icon-pagination disabled"><i class="fa fa-chevron-left"></i></span>
                @else
                    <a href="{{ $nowPlaying->previousPageUrl() }}" class="icon-pagination"><i class="fa fa-chevron-left"></i></a>
                @endif

                {{-- Dots --}}
                <div class="dots-wrapper d-flex gap-2">
                    @for ($i = 1; $i <= $nowPlaying->lastPage(); $i++)
                        <a href="{{ $nowPlaying->url($i) }}" class="dot {{ $nowPlaying->currentPage() == $i ? 'active' : '' }}"></a>
                    @endfor
                </div>

                {{-- Next --}}
                @if ($nowPlaying->hasMorePages())
                    <a href="{{ $nowPlaying->nextPageUrl() }}" class="icon-pagination"><i class="fa fa-chevron-right"></i></a>
                @else
                    <span class="icon-pagination disabled"><i class="fa fa-chevron-right"></i></span>
                @endif
            </div>
        @endif
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

        {{-- Pagination Upcoming --}}
        @if ($upComing->lastPage() > 1)
            <div class="d-flex justify-content-center align-items-center mt-4 gap-2">
                {{-- Prev --}}
                @if ($upComing->onFirstPage())
                    <span class="icon-pagination disabled"><i class="fa fa-chevron-left"></i></span>
                @else
                    <a href="{{ $upComing->previousPageUrl() }}" class="icon-pagination"><i class="fa fa-chevron-left"></i></a>
                @endif

                {{-- Dots --}}
                <div class="dots-wrapper d-flex gap-2">
                    @for ($i = 1; $i <= $upComing->lastPage(); $i++)
                        <a href="{{ $upComing->url($i) }}" class="dot {{ $upComing->currentPage() == $i ? 'active' : '' }}"></a>
                    @endfor
                </div>

                {{-- Next --}}
                @if ($upComing->hasMorePages())
                    <a href="{{ $upComing->nextPageUrl() }}" class="icon-pagination"><i class="fa fa-chevron-right"></i></a>
                @else
                    <span class="icon-pagination disabled"><i class="fa fa-chevron-right"></i></span>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection

@section('styles')
<style>
.home-page { padding: 10px; }
.text-muted {
    color: #b0b0b0 !important;
}
/* Movie card */
.cinestar-movie-card { 
    height: 570px; 
    background: #111829; 
    border-radius: 12px; 
    overflow: hidden; 
    border: 1px solid rgba(255,255,255,0.05); 
    display: flex; 
    flex-direction: column; 
    transition: all 0.4s ease; 
}
.cinestar-movie-card:hover { 
    transform: translateY(-6px) scale(1.03); 
    box-shadow: 0 25px 45px rgba(255,193,7,0.18)!important; 
}
.poster { height: 420px; width: 100%; position: relative; overflow: hidden; }
.poster img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.cinestar-movie-card:hover .poster img { transform: scale(1.07); }
.info { height: 150px; background: #111829; padding: 10px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; }
.movie-title { font-size: 0.92rem; font-weight: 600; color: #fff; text-transform: uppercase; transition: 0.3s ease; height: 45px; overflow: hidden; }
.movie-date { font-size: 0.78rem; color: #9aa0b5; margin: 2px 0 6px 0; transition: 0.3s ease; }
.cinestar-movie-card:hover .movie-title { color: #ffc107 !important; }
.cinestar-movie-card:hover .movie-date { color: #fff !important; }
.age-badge-new { height: 22px; border-radius: 6px; overflow: hidden; font-size: 0.65rem; z-index: 3; }
.age-badge-new .badge-right { min-width: 28px; background: #1a1a1a !important; color: #ffc107 !important; }
.btn-datve { min-width: 100px; padding: 8px 26px; border-radius: 40px; background: linear-gradient(135deg,#ffc107,#ff9f1c)!important; color:#000!important; font-weight:700; border:none; transition:0.3s ease; }
.btn-datve:hover { background: linear-gradient(135deg,#ffcd39,#ffb52c)!important; transform: scale(1.07); color:#000!important; }

/* Pagination dots */
.dots-wrapper { display: flex; gap: 6px; align-items: center; }
.dots-wrapper .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #555;
    display: inline-block;
    transition: background-color 0.3s, transform 0.3s;
}
.dots-wrapper .dot.active { background-color: #ffc107; transform: scale(1.3); }
.dots-wrapper .dot:hover { background-color: #ffcd39; }

/* Prev/Next icon */
.icon-pagination {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #222;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s;
}
.icon-pagination i { pointer-events: none; }
.icon-pagination:hover { background-color: #ffc107; color: #222; }
.icon-pagination.disabled { background-color: #555; color: #888; pointer-events: none; }

/* Responsive */
@media (max-width:768px){.cinestar-movie-card{height:330px}.poster{height:210px}}
@media (max-width:480px){.cinestar-movie-card{height:320px}.poster{height:200px}}
</style>
@endsection
