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
                    <!-- PHIM + ẢNH -->
                    <div class="checkout-movie-info d-flex align-items-center gap-3">
                        <img src="{{ $data['posterurl'] }}" alt="{{ $data['movieName'] }}"
                            class="checkout-poster img-fluid rounded shadow" style="width: 30%; object-fit: cover;">
                        <div>
                            <h5 class="mb-0">
                                <i class="fa-solid fa-film"></i> {{ $data['movieName'] }}
                                <span class="checkout-age-badge">{{ $data['ageRating'] }}</span>
                            </h5>
                        </div>
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

                    <!-- PHƯƠNG THỨC THANH TOÁN -->
                    <div class="mt-4">
                        <h5 class="mb-3">Chọn phương thức thanh toán</h5>
                        <form id="payment-form" action="{{ route('checkout.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" id="payment-method-input">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="payment-option d-block p-3" >
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="https://img.mservice.com.vn/app/img/portal_documents/mini-app_design-guideline_branding-guide-2-2.png"
                                                alt="Momo" width="40">
                                            <div>
                                                <strong>Ví Momo</strong>
                                                <small class="d-block">Thanh toán nhanh</small>
                                            </div>
                                        </div>
                                        <input type="radio" name="payment_method" value="momo" class="d-none" onchange="selectPayment('momo')">
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="payment-option d-block p-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="https://vinadesign.vn/uploads/images/2023/05/vnpay-logo-vinadesign-25-12-57-55.jpg"
                                                alt="VNPay" width="40">
                                            <div>
                                                <strong>VNPay</strong>
                                                <small class="d-block">Thẻ ATM / QR</small>
                                            </div>
                                        </div>
                                        <input type="radio" name="payment_method" value="vnpay" class="d-none"  onchange="selectPayment('vnpay')">
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="payment-option d-block p-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="https://play-lh.googleusercontent.com/oXs9tsmauo4_xFDsovB7i3ONfNWZ9FR8shrnegcYC4tHCjybZexXa0fpe9N_3kYqw-U"
                                                alt="ShopeePay" width="40">
                                            <div>
                                                <strong>ShopeePay</strong>
                                                <small class="d-block">Ví Shopee</small>
                                            </div>
                                        </div>
                                        <input type="radio" name="payment_method" value="shopeepay" class="d-none" onchange="selectPayment('shopeepay')">
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="payment-option d-block p-3" >
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-university fa-2x text-warning"></i>
                                            <div>
                                                <strong>Chuyển khoản</strong>
                                                <small class="d-block">Ngân hàng</small>
                                            </div>
                                        </div>
                                        <input type="radio" name="payment_method" value="bank_transfer" class="d-none" onchange="selectPayment('bank_transfer')">
                                    </label>
                                </div>
                            </div>

                            <!-- Tổng tiền -->
                            <div class="checkout-total-box">
                                <div class="checkout-total-row">
                                    <div>
                                        <h5>Tổng cộng</h5>
                                        <small>{{ count($data['seats']) }} vé × {{ number_format($data['pricePerSeat']) }}
                                            ₫</small>
                                    </div>
                                    <h3>{{ number_format($data['totalPrice']) }} ₫</h3>
                                </div>
                            </div>

                            <!-- Nút -->
                            <div class="checkout-actions mt-4">
                                <button type="submit" form="payment-form" class="checkout-btn-confirm w-100">
                                    XÁC NHẬN THANH TOÁN
                                </button>
                                <a href="javascript:history.back()" class="checkout-btn-back d-block text-center mt-2">
                                    Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(method) {
            if (!method) return; // ✅ Nếu rỗng thì bỏ qua

            // 1. Xóa chọn cũ
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('selected');
                const radio = el.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
            });

            // 2. Tìm input mới
            const input = document.querySelector(`input[value="${method}"]`);
            if (!input) {
                console.warn(`Không tìm thấy input với value="${method}"`);
                return;
            }

            // 3. Tìm label cha
            const label = input.closest('.payment-option');
            if (!label) {
                console.warn(`Không tìm thấy label cha cho ${method}`);
                return;
            }

            // 4. Đánh dấu chọn
            label.classList.add('selected');
            input.checked = true;

            // 5. Cập nhật hidden
            const hidden = document.getElementById('payment-method-input');
            if (hidden) hidden.value = method;
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Lấy phương thức cũ nếu có, mặc định momo
            const saved = '{{ old('payment_method') ?: 'momo' }}';
            if (saved) selectPayment(saved);
        });
    </script>

@endsection