@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">

    <div class="checkout-page-wrapper">
        <div class="checkout-container">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h3>XÁC NHẬN ĐẶT VÉ</h3>
                </div>

                <div class="checkout-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <!-- Phim -->
                    <div class="checkout-movie-info">
                        <h5>
                            <i class="fa-solid fa-film"></i> {{ $data['movieName'] }}
                            <span class="checkout-age-badge">{{ $data['ageRating'] }}</span>
                        </h5>
                    </div>

                    <!-- Thông tin -->
                    <div class="checkout-info-grid">
                        <div class="checkout-info-item">
                            <small>Rạp chiếu</small>
                            <p>{{ $data['cinemaName'] }}</p>
                        </div>
                        <div class="checkout-info-item">
                            <small>Phòng chiếu</small>
                            <p>{{ $data['roomName'] }}</p>
                        </div>
                        <div class="checkout-info-item">
                            <small>Suất chiếu</small>
                            <p>{{ $data['showtime'] }}</p>
                        </div>
                        <div class="checkout-info-item">
                            <small>Ghế đã chọn</small>
                            <p class="checkout-seats">
                                {{ implode(', ', $data['seats']) }}
                                <small>({{ count($data['seats']) }} ghế)</small>
                            </p>
                        </div>
                    </div>

                    <!-- Tổng tiền -->
                    <div class="checkout-total-box">
                        <div class="checkout-total-row">
                            <div>
                                <h5>Tổng cộng</h5>
                                <small>{{ count($data['seats']) }} vé × {{ number_format($data['pricePerSeat']) }} ₫</small>
                            </div>
                            <h3>{{ number_format($data['totalPrice']) }} ₫</h3>
                        </div>
                    </div>

                    <!-- Nút -->
                    <form action="{{ route('checkout.confirm') }}" method="POST">
                        @csrf
                        <div class="checkout-actions">
                            <button type="submit" class="checkout-btn-confirm">
                                XÁC NHẬN THANH TOÁN
                            </button>
                            <a href="javascript:history.back()" class="checkout-btn-back">
                                Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection