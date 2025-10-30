<?php
// HOME.BLADE.PHP (Trang chủ)

use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str; 
?>

@extends('layouts.app')

@section('content')
<div class="home-page">

    {{-- 🎁 KHU VỰC CHÀO MỪNG CÁ NHÂN HÓA VÀ ĐĂNG XUẤT --}}
    @auth
        <div class="container mt-4 mb-4">
            <div class="alert alert-info d-flex justify-content-between align-items-center shadow-sm p-3 rounded-lg">
                {{-- Lấy tên người dùng đang đăng nhập --}}
                <span class="fw-bold fs-5 text-dark">
                    Xin chào, <strong class="text-primary">{{ Auth::user()->name }}</strong>! 
                </span>
                
                {{-- Nút Đăng xuất --}}
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

    {{-- 🎞️ Banner Slider --}}
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            {{-- Kiểm tra xem $nowPlaying có dữ liệu không trước khi lặp --}}
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
                    <img src="https://placehold.co/1920x600/3B0066/FFFFFF?text=Không+có+phim+đang+chiếu" class="d-block w-100" alt="Không có phim đang chiếu">
                    <div class="carousel-caption">
                         <h5>Phim đang chiếu</h5>
                         <p>Dữ liệu phim đang được cập nhật.</p>
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

    {{-- 🍿 Phim đang chiếu --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4">🎬 Phim đang chiếu</h2>
        <div class="row">
            @forelse ($nowPlaying as $movie)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $movie['posterurl'] }}" class="card-img-top" alt="{{ $movie['name'] }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $movie['name'] }}</h5>
                            <p class="card-text text-muted">⏱ {{ $movie['duration'] }} phút</p>
                            <a href="{{ url('/' . $movie['id']) }}" class="btn btn-primary w-100 mt-auto">Chi tiết</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Hiện tại không có phim nào đang chiếu.</p>
            @endforelse
        </div>
    </section>

    {{-- 🎟️ Phim sắp chiếu --}}
    <section class="container my-5">
        <h2 class="section-title text-uppercase mb-4">🎞️ Phim sắp chiếu</h2>
        <div class="row">
            @forelse ($upComing as $movie)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $movie['posterurl'] }}" class="card-img-top" alt="{{ $movie['name'] }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $movie['name'] }}</h5>
                            <p class="card-text text-muted">📅 {{ \Carbon\Carbon::parse($movie['releasedate'])->format('d/m/Y') }}</p>
                            <a href="{{ url('/' . $movie['id']) }}" class="btn btn-outline-secondary w-100 mt-auto">Chi tiết</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Hiện tại không có phim nào sắp chiếu.</p>
            @endforelse
        </div>
    </section>

</div>
@endsection
