@extends('layouts.app')

@section('content')
<div class="home-page">

    {{-- 🎞️ Banner Slider --}}
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($nowPlaying as $index => $movie)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $movie['bannerurl'] }}" class="d-block w-100" alt="{{ $movie['name'] }}">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-3 rounded">
                        <h5>{{ $movie['name'] }}</h5>
                        <p>{{ Str::limit($movie['description'], 100) }}</p>
                        <a href="#" class="btn btn-warning">Đặt vé ngay</a>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    {{-- 🍿 Phim đang chiếu --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4">🎬 Phim đang chiếu</h2>
        <div class="row">
            @foreach ($nowPlaying as $movie)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $movie['posterurl'] }}" class="card-img-top" alt="{{ $movie['name'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $movie['name'] }}</h5>
                            <p class="card-text text-muted">⏱ {{ $movie['duration'] }} phút</p>
                            <a href="{{ url('/' . $movie['id']) }}" class="btn btn-primary w-100">Chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 🎟️ Phim sắp chiếu --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4">🎞️ Phim sắp chiếu</h2>
        <div class="row">
            @foreach ($upComing as $movie)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $movie['posterurl'] }}" class="card-img-top" alt="{{ $movie['name'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $movie['name'] }}</h5>
                            <p class="card-text text-muted">📅 {{ \Carbon\Carbon::parse($movie['releasedate'])->format('d/m/Y') }}</p>
                            <a href="{{ url('/' . $movie['id']) }}" class="btn btn-outline-secondary w-100">Chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
