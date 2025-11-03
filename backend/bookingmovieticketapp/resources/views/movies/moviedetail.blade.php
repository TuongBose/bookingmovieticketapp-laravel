@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/moviedetail.css') }}">
    <div class="movie-detail-wrapper">
        <div class="container my-5">
            <div class="row">
                <!-- Poster + Info -->
                <div class="col-md-4">
                    <img src="{{ $movie->posterurl }}" class="img-fluid rounded shadow" alt="{{ $movie->name }}">
                    <div class="mt-3">
                        <a href="#" class="btn btn-warning w-100">Mua vé ngay</a>
                    </div>
                </div>

                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">{{ $movie->name }} <span
                            class="badge bg-secondary">{{ $movie->agerating }}</span></h1>

                    @php
                        $ageDesc = getAgeRatingDescription($movie->agerating);
                    @endphp

                    <div class="mb-4">
                        <div class="info-item">
                            <i class="fa-solid fa-tags"></i>
                            <span>Gia đình, Tâm lý</span>
                        </div>
                        <div class="info-item">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $movie->duration }} phút</span>
                        </div>

                        <div class="info-item age-rating-info">
                            <i class="fa-solid fa-circle-info"></i>
                            <span><strong>{{ $movie->agerating }}:</strong> {{ $ageDesc }}</span>
                        </div>
                    </div>

                    <hr>

                    <h3>Mô tả</h3>
                    <p>Đạo diễn: {{ $movie->director ?? 'Chưa có thông tin' }}</p>
                    <p>Diễn viên:
                        @if($casts->count() > 0)
                            {{ $casts->take(5)->pluck('actorname')->join(', ') }}
                            @if($casts->count() > 5)
                                <em>, và nhiều hơn...</em>
                            @endif
                        @else
                            Chưa có thông tin
                        @endif
                    </p>
                    <p>Khởi chiếu: {{ $movie->releasedate ?? 'Chưa có thông tin' }}</p>
                    <hr>

                    <h3>Nội dung phim</h3>
                    <p>{{ $movie->description ?? 'Chưa có thông tin' }}</p>
                    <hr>

                    <h3>Trailer</h3>
                    @if($trailer && isset($trailer['key']))
                        <div class="trailer-container mb-4">
                            <iframe width="100%" height="480" src="https://www.youtube.com/embed/{{ $trailer['key'] }}"
                                title="{{ $trailer['name'] ?? 'Trailer phim' }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <p class="text-muted">Chưa có trailer cho phim này.</p>
                    @endif
                </div>

                <!-- Lịch chiếu -->
                <div class="text-center showtime-container" data-movie-id="{{ $movie->id }}">
                    <h3>Lịch chiếu</h3>

                    <div class="date-selector my-4 d-flex justify-content-center flex-wrap gap-3" id="dateContainer">
                        {{-- Các ô ngày sẽ được render bằng JavaScript --}}
                    </div>

                    <h2 class="text-uppercase fw-bold text-white">Danh sách rạp</h2>
                    <div class="showtime-wrapper">
                        <!-- CHỌN THÀNH PHỐ -->
                        <div class="d-flex justify-content-center align-items-center gap-3 my-4 city-select-wrapper">
                            <div class="flex-fill">
                                <select id="citySelect" class="form-select form-select-sm">
                                    <option value="">-- Tất cả thành phố --</option>
                                </select>
                            </div>
                        </div>

                        <!-- SUẤT CHIẾU -->
                        <div id="showtimes-container" class="mt-4">
                            <p class="text-muted">Vui lòng chọn ngày và rạp.</p>
                        </div>

                        <!-- SƠ ĐỒ GHẾ - HIỆN KHI CHỌN SUẤT -->
                        <div id="seat-selection-container" class="mt-5 p-4 bg-dark rounded d-none">
                            <div class="text-center mb-3">
                                <h5 class="text-white">Chọn ghế - <span id="selected-cinema-name"></span></h5>
                                <small class="text-muted">Màn hình</small>
                                <div class="screen mb-4"></div>
                            </div>
                            <div id="seat-map" class="d-flex justify-content-center flex-wrap gap-2 mb-4"></div>
                            <div class="seat-legend d-flex justify-content-center gap-4 flex-wrap mt-3 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="seat-legend-box" style="background: white; border: 1px solid #555;"></div>
                                    <span class="text-white small">Ghế Thường</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="seat-legend-box" style="background: #ff9800;"></div>
                                    <span class="text-white small">Ghế chọn</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="seat-legend-box" style="background: #555; border: 1px solid #666;"></div>
                                    <span class="text-white small">Ghế đã đặt</span>
                                </div>
                            </div>
                        </div>
                        <div id="booking-summary" class="mt-5 p-4 bg-dark rounded d-none"
                            style="border: 2px solid #ff9800;">
                            <h5 class="text-white mb-3 text-center">Xác nhận đặt vé</h5>
                            <div class="row text-white small">
                                <div class="col-6">
                                    <strong>Rạp:</strong> <span id="summary-cinema"></span>
                                </div>
                                <div class="col-6">
                                    <strong>Phòng:</strong> <span id="summary-room"></span>
                                </div>
                                <div class="col-6 mt-2">
                                    <strong>Giờ chiếu:</strong> <span id="summary-time"></span>
                                </div>
                                <div class="col-6 mt-2">
                                    <strong>Ghế:</strong> <span id="summary-seats">Chưa chọn</span>
                                </div>
                                <div class="col-12 mt-2 text-end">
                                    <strong class="text-warning" style="font-size: 18px;">
                                        Tổng tiền: <span id="summary-total">0 ₫</span>
                                    </strong>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button id="final-confirm" class="btn btn-warning px-5" disabled>Xác nhận</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const showtimeContainer = document.querySelector('.showtime-container');
        const movieId = showtimeContainer.dataset.movieId;
        const dateContainer = document.getElementById('dateContainer');
        const citySelect = document.getElementById('citySelect');
        const showtimesContainer = document.getElementById('showtimes-container');

        let selectedShowId = null;
        let selectedSeats = [];

        const daysOfWeek = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
        let selectedDate = null;
        let selectedCity = 'all'; // Mặc định là ALL

        let selectedShowtimeInfo = {
            cinemaName: '',
            roomName: '',
            showtime: '',
            seats: [],
            price: 0
        };

        // === 1. TẠO 5 NGÀY ===
        function renderDates() {
            const today = new Date();
            for (let i = 0; i < 5; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);

                const dayName = daysOfWeek[date.getDay()];
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const fullDate = date.toISOString().split('T')[0];

                const div = document.createElement('div');
                div.className = 'date-box' + (i === 0 ? ' active' : '');
                div.dataset.date = fullDate;
                div.innerHTML = `<strong>${day}/${month}</strong><br><small>${dayName}</small>`;

                div.addEventListener('click', () => {
                    document.querySelectorAll('.date-box').forEach(el => el.classList.remove('active'));
                    div.classList.add('active');
                    selectedDate = fullDate;
                    loadAllShowtimes(); // TẢI TẤT CẢ SUẤT CHIẾU
                });

                dateContainer.appendChild(div);
            }
            selectedDate = today.toISOString().split('T')[0]; // Hôm nay
        }

        // === 2. LẤY TẤT CẢ THÀNH PHỐ TỪ API CINEMA ===
        async function loadCities() {
            try {
                const res = await fetch('/api/v1/cinemas');
                const cinemas = await res.json();

                const cities = ['all', ...new Set(cinemas.map(c => c.city))].sort((a, b) => {
                    if (a === 'all') return -1;
                    if (b === 'all') return 1;
                    return a.localeCompare(b);
                });

                citySelect.innerHTML = '';
                cities.forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city === 'all' ? 'all' : city;
                    opt.textContent = city === 'all' ? 'Tất cả thành phố' : city;
                    citySelect.appendChild(opt);
                });

                citySelect.value = 'all'; // Mặc định ALL
            } catch (err) {
                console.error('Lỗi tải thành phố:', err);
            }
        }

        // === 3. LẤY TẤT CẢ RẠP + SUẤT CHIẾU (city = all hoặc cụ thể) ===
        async function loadAllShowtimes() {
            showtimesContainer.innerHTML = '<p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Đang tải lịch chiếu...</p>';

            const encodedCity = encodeURIComponent(selectedCity);
            try {
                // B1: Lấy danh sách rạp theo movie + city + date
                const cinemaRes = await fetch(`/api/v1/cinemas/movieandcityanddate?movieId=${movieId}&city=${encodedCity}&date=${selectedDate}`);
                const cinemas = await cinemaRes.json();

                if (cinemas.length === 0) {
                    showtimesContainer.innerHTML = '<p class="text-muted">Không có rạp nào chiếu phim này vào ngày này.</p>';
                    return;
                }

                let html = '';

                // B2: Với mỗi rạp → gọi API showtimes
                for (const cinema of cinemas) {
                    try {
                        const showRes = await fetch(`/api/v1/showtimes?movieId=${movieId}&cinemaId=${cinema.id}&date=${selectedDate}`);
                        const showtimes = await showRes.json();

                        if (showtimes.length === 0) continue;

                        html += `
                                             <div class="cinema-showtime-block mb-4 p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid #444;" data-cinema-id="${cinema.id}">
                                                 <h5 class="text-warning mb-2">${cinema.name}</h5>
                                                 <p class="text-muted small mb-2">${cinema.address}</p>
                                                 <div class="d-flex flex-wrap gap-2 justify-content-start">
                                                                                                      `;

                        showtimes.forEach(show => {
                            const time = new Date(show.starttime).toLocaleTimeString('vi-VN', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            html += `
                                                                                                                                                                                                                    <a href="/book/${movieId}/${show.id}" class="btn btn-outline-light btn-sm">
                                                                                                                                                                                                                        ${time}
                                                                                                                                                                                                                    </a>`;
                        });

                        html += `</div></div>`;
                    } catch (err) {
                        console.error(`Lỗi lấy suất chiếu rạp ${cinema.id}:`, err);
                    }
                }

                if (!html) {
                    showtimesContainer.innerHTML = '<p class="text-muted">Không có suất chiếu nào.</p>';
                } else {
                    showtimesContainer.innerHTML = html;

                    attachShowtimeClick();
                }
            } catch (err) {
                showtimesContainer.innerHTML = '<p class="text-danger">Lỗi tải dữ liệu</p>';
                console.error(err);
            }
        }

        // === 4. KHI CLICK VÀO SUẤT CHIẾU ===
        function attachShowtimeClick() {
            document.querySelectorAll('#showtimes-container a').forEach(btn => {
                btn.addEventListener('click', async function (e) {
                    e.preventDefault();
                    document.querySelectorAll('#showtimes-container a').forEach(b => {
                        b.classList.remove('active-showtime');
                    });
                    this.classList.add('active-showtime');
                    document.getElementById('seat-selection-container').classList.add('d-none');

                    const href = this.getAttribute('href');
                    selectedShowId = href.split('/').pop();

                    const cinemaBlock = this.closest('.cinema-showtime-block');
                    const cinemaName = cinemaBlock.querySelector('h5').textContent;
                    const cinemaId = cinemaBlock.dataset.cinemaId;
                    const showtimeText = this.textContent.trim();

                    // LẤY ROOM NAME
                    const showRes = await fetch(`/api/v1/showtimes/${selectedShowId}`);
                    const show = await showRes.json();
                    const roomRes = await fetch(`/api/v1/rooms/${show.roomId}`);
                    const room = await roomRes.json();

                    let fullCinemaName = cinemaName;
                    try {
                        const cinemaRes = await fetch(`/api/v1/cinemas/${cinemaId}`);
                        const cinema = await cinemaRes.json();
                        fullCinemaName = `${cinema.name} (${cinema.city})`;
                    } catch (err) {
                        console.warn('Lỗi lấy city:', err);
                    }

                    // CẬP NHẬT THÔNG TIN
                    selectedShowtimeInfo = {
                        cinemaId,
                        cinemaName: fullCinemaName,
                        roomName: room.name || `Rạp ${room.id}`,
                        showtime: showtimeText,
                        seats: [],
                        price: show.price
                    };

                    document.getElementById('selected-cinema-name').textContent =
                        `${cinemaName} - ${selectedShowtimeInfo.roomName}`;

                    await loadAndRenderSeats(selectedShowId);
                    document.getElementById('seat-selection-container').classList.remove('d-none');
                    document.getElementById('seat-selection-container').scrollIntoView({ behavior: 'smooth' });
                });
            });
        }

        // === 5. LẤY ROOM + RENDER GHẾ ===
        async function loadAndRenderSeats(showId) {
            const seatMap = document.getElementById('seat-map');
            seatMap.innerHTML = '<p class="text-muted">Đang tải sơ đồ ghế...</p>';

            try {
                // === B1: Lấy thông tin suất chiếu → roomId + room info
                const showRes = await fetch(`/api/v1/showtimes/${showId}`);
                const show = await showRes.json();
                const roomId = show.roomId;

                const roomRes = await fetch(`/api/v1/rooms/${roomId}`);
                const room = await roomRes.json();
                const rows = room.seatrowmax;
                const cols = room.seatcolumnmax;
                const bookedSeatIds = new Set();

                // Tính chiều rộng cần thiết
                const estimatedWidth = cols * 46; // 38px + gap ~8px
                const containerWidth = 660; // 700px - padding
                let seatSize = 38;
                let fontSize = 12;

                if (estimatedWidth > containerWidth) {
                    seatSize = Math.floor(containerWidth / cols) - 8; // trừ gap
                    seatSize = Math.max(28, seatSize); // không nhỏ quá
                    fontSize = seatSize < 34 ? 10 : 11;
                }

                // Áp dụng style động
                const style = document.createElement('style');
                style.textContent = `
                                                                                                                                                        #seat-map .seat-btn {
                                                                                                                                                            width: ${seatSize}px !important;
                                                                                                                                                            height: ${seatSize}px !important;
                                                                                                                                                            font-size: ${fontSize}px !important;
                                                                                                                                                        }
                                                                                                                                                    `;
                document.head.appendChild(style);

                let seatIdMap = {};
                try {
                    const seatRes = await fetch(`/api/v1/seats?roomId=${roomId}`);
                    const seats = await seatRes.json(); // ← MẢNG [{id: 4748, seatnumber: "A1"}]
                    seats.forEach(s => {
                        seatIdMap[s.seatnumber] = s.id; // {"A1": 4748}
                    });
                    console.log('seatIdMap:', seatIdMap); // DEBUG
                } catch (err) {
                    console.error('Lỗi lấy seats:', err);
                }

                // === B2: LẤY GHẾ ĐÃ ĐẶT THEO showtimeId
                const bookedSet = new Set();

                try {
                    const bookingRes = await fetch(`/api/v1/bookings/showtimes/${showId}/bookings`);
                    const bookings = await bookingRes.json(); // Mảng booking

                    // Lặp từng booking → lấy chi tiết ghế
                    for (const booking of bookings) {
                        const details = await (await fetch(`/api/v1/bookingdetails/${booking.id}/details`)).json();
                        details.forEach(d => {
                            bookedSeatIds.add(d.seatId); // ← 4748, 4750
                        });
                    }
                } catch (err) {
                    console.warn('Lỗi lấy ghế đã đặt (có thể chưa có booking):', err);
                    // Không lỗi → vẫn render ghế trống
                }

                // === B3: RENDER GHẾ
                let html = '';
                const rowsLabel = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.slice(0, rows);

                for (let r = 0; r < rows; r++) {
                    const rowLetter = rowsLabel[r];
                    html += `<div class="d-flex gap-1 align-items-center mb-1">`;
                    html += `<span class="text-warning me-2" style="width:20px;">${rowLetter}</span>`;

                    for (let c = 1; c <= cols; c++) {
                        const seatNum = `${rowLetter}${c}`;
                        const seatId = seatIdMap[seatNum] || null;
                        const isBooked = seatId ? bookedSeatIds.has(seatId) : false;

                        html += `
                              <button class="seat-btn ${isBooked ? 'booked' : 'available'}"
                                      data-seat="${seatNum}"
                                      data-seat-id="${seatId}"
                                      ${isBooked ? 'disabled' : ''}>
                                  ${c}
                              </button>`;
                    }
                    html += `</div>`;
                }

                seatMap.innerHTML = html;
                attachSeatClick();

            } catch (err) {
                seatMap.innerHTML = '<p class="text-danger">Lỗi tải sơ đồ ghế</p>';
                console.error(err);
            }
        }

        // === 6. CHỌN GHẾ ===
        function attachSeatClick() {
            selectedSeats = [];
            selectedSeatIds = [];

            document.querySelectorAll('.seat-btn.available').forEach(btn => {
                const seatNum = btn.dataset.seat;
                const seatId = btn.dataset.seatId ? parseInt(btn.dataset.seatId, 10) : null;

                if (!seatId) {
                    btn.disabled = true;
                    btn.title = "Ghế lỗi dữ liệu";
                    console.warn(`Ghế ${seatNum} không có ID`);
                    return;
                }

                btn.addEventListener('click', function () {
                    const seat = this.dataset.seat;
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        selectedSeats = selectedSeats.filter(s => s !== seatNum);
                        selectedSeatIds = selectedSeatIds.filter(id => id !== seatId);
                    } else {
                        this.classList.add('selected');
                        selectedSeats.push(seatNum);
                        selectedSeatIds.push(seatId);
                    }
                    selectedShowtimeInfo.seats = selectedSeats;
                    selectedShowtimeInfo.seatIds = selectedSeatIds;
                    updateSummaryBox();
                });
            });
        }

        function updateSummaryBox() {
            const summaryBox = document.getElementById('booking-summary');
            if (!selectedShowtimeInfo.cinemaName) {
                summaryBox.classList.add('d-none');
                return;
            }

            document.getElementById('summary-cinema').textContent = selectedShowtimeInfo.cinemaName;
            document.getElementById('summary-room').textContent = selectedShowtimeInfo.roomName;
            document.getElementById('summary-time').textContent = selectedShowtimeInfo.showtime;
            document.getElementById('summary-seats').textContent =
                selectedShowtimeInfo.seats.length > 0 ? selectedShowtimeInfo.seats.join(', ') : 'Chưa chọn';

            // === TÍNH TỔNG TIỀN ===
            const total = selectedShowtimeInfo.price * selectedShowtimeInfo.seats.length;
            document.getElementById('summary-total').textContent =
                total.toLocaleString('vi-VN') + ' ₫';

            summaryBox.classList.remove('d-none');

            // Bật nút thanh toán
            document.getElementById('final-confirm').disabled = selectedSeats.length === 0;
        }

        document.getElementById('final-confirm').onclick = async () => {
            if (selectedSeats.length === 0) {
                alert('Vui lòng chọn ít nhất 1 ghế!');
                return;
            }

            try {
                const authRes = await fetch('/api/v1/auth/check');
                const authData = await authRes.json();

                if (!authData.authenticated) {
                    // CHƯA ĐĂNG NHẬP → CHUYỂN ĐẾN TRANG LOGIN
                    alert('Vui lòng đăng nhập để tiếp tục thanh toán!');
                    window.location.href = '/auth'; // ← Trang đăng nhập
                    return;
                }

                // ĐÃ ĐĂNG NHẬP → TIẾP TỤC
                const user = authData.user;

                const checkoutData = {
                    movieId: "{{ $movie->id }}",
                    movieName: "{{ addslashes($movie->name) }}",
                    posterurl:"{{ $movie->posterurl }}",
                    ageRating: "{{ $movie->agerating }}",
                    showtimeId: selectedShowId,
                    cinemaId: selectedShowtimeInfo.cinemaId,
                    cinemaName: selectedShowtimeInfo.cinemaName,
                    roomName: selectedShowtimeInfo.roomName,
                    showtime: selectedShowtimeInfo.showtime,
                    seats: selectedSeats,
                    seatIds: selectedSeatIds,
                    pricePerSeat: selectedShowtimeInfo.price,
                    totalPrice: selectedShowtimeInfo.price * selectedSeats.length,
                    userId: user.id
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('Lỗi: Thiếu CSRF token');
                    return;
                }

                const response = await fetch('/api/v1/checkout/prepare', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(checkoutData)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Lỗi server');
                }

                // CHUYỂN HƯỚNG QUA CHECKOUT
                window.location.href = '/checkout';

            } catch (err) {
                console.error('Checkout prepare failed:', err);
                alert('Lỗi: ' + err.message);
            }
        };

        // === SỰ KIỆN ===
        citySelect.addEventListener('change', () => {
            selectedCity = citySelect.value;
            loadAllShowtimes();
        });

        // === KHỞI ĐỘNG ===
        window.addEventListener('load', () => {
            renderDates();
            loadCities().then(() => {
                loadAllShowtimes(); // TỰ ĐỘNG HIỂN THỊ KHI VÀO TRANG
            });
        });
    </script>
@endsection