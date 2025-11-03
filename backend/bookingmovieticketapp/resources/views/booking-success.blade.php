{{-- resources/views/booking-success.blade.php --}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/booking-success.css') }}">

<div class="success-wrapper">
    <div class="container my-5">
        <div class="success-card text-center p-5">
            <i class="fa-solid fa-circle-check text-success" style="font-size: 80px;"></i>
            <h1 class="mt-4 text-white">ĐẶT VÉ THÀNH CÔNG!</h1>
            <p class="text-light mb-2">Mã đặt vé: <strong class="text-warning fw-bold">#{{ session('success_data.bookingId') }}</strong></p>

            <div class="ticket mt-5 p-4" style="background: #1a1a1a; border: 2px dashed #ff9800; border-radius: 12px;">
                <!-- ẢNH + TÊN PHIM -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ session('success_data.posterurl') }}" 
                         alt="{{ session('success_data.movieName') }}" 
                         class="success-poster img-fluid rounded shadow"
                         style="width: 80px; height: 120px; object-fit: cover;">
                    <h4 class="text-warning mb-0">{{ session('success_data.movieName') }}</h4>
                </div>
                <div class="row text-start mt-3 text-white small">
                    <div class="col-md-6">
                        <p><strong>Rạp:</strong> {{ session('success_data.cinemaName') }}</p>
                        <p><strong>Phòng:</strong> {{ session('success_data.roomName') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Suất chiếu:</strong> {{ session('success_data.showtime') }}</p>
                        <p><strong>Ghế:</strong> {{ implode(', ', session('success_data.seats')) }}</p>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <strong class="text-warning Rạp: {{ session('success_data.cinemaName') }}</strong>
                    <p class="text-success fs-4">Tổng tiền: {{ number_format(session('success_data.totalPrice')) }} ₫</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="/" class="btn btn-outline-light me-2">Về trang chủ</a>
                <button onclick="window.print()" class="btn btn-warning">In vé</button>
            </div>
        </div>
    </div>
</div>
@endsection