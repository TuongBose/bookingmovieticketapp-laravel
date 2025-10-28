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
                <div class="text-center showtime-container">
                    <h3>Lịch chiếu</h3>
                    <div class="date-selector my-4 d-flex justify-content-center flex-wrap gap-3" id="dateContainer">
                        {{-- Các ô ngày sẽ được render bằng JavaScript --}}
                    </div>
                    <div class="mb-3">
                        <label>Chọn rạp:</label>
                        <select id="cinemaSelect" class="form-select">
                            <option value="">-- Chọn rạp --</option>
                            @foreach($cinemas as $cinema)
                                <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === Sinh ra 5 ngày từ hôm nay ===
        const dateContainer = document.getElementById('dateContainer');
        const daysOfWeek = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];

        const today = new Date();
        for (let i = 0; i < 5; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);

            const day = daysOfWeek[date.getDay()];
            const formatted = `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`;

            const div = document.createElement('div');
            div.classList.add('date-box');
            div.innerHTML = `<strong>${formatted}</strong><br>${day}`;
            div.dataset.date = date.toISOString().split('T')[0];

            if (i === 0) div.classList.add('active');

            div.addEventListener('click', function () {
                document.querySelectorAll('.date-box').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                // có thể fetch lịch chiếu theo ngày ở đây:
                // loadShowtimeForDate(this.dataset.date);
            });

            dateContainer.appendChild(div);
        }
    </script>
@endsection