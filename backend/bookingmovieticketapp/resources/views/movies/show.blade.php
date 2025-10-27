@extends('layouts.app')

@section('content')
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

                <p class="text-muted">
                    <strong>Thể loại:</strong> Gia đình, Tâm lý <br>
                    <strong>Thời lượng:</strong> {{ $movie->duration }} phút <br>
                    <strong>Quốc gia:</strong> Việt Nam <br>
                    <strong>Đạo diễn:</strong> {{ $movie->director }} <br>
                    <strong>Diễn viên:</strong> {{ $movie->casts }}
                </p>

                <hr>

                <h3>Mô tả</h3>
                <p>{{ $movie->description ?? 'Chưa có thông tin' }}</p>

                <hr>

                <!-- Lịch chiếu -->
                <h3>Lịch chiếu</h3>
                <div class="mb-3">
                    <label>Chọn rạp:</label>
                    <select id="cinemaSelect" class="form-select">
                        <option value="">-- Chọn rạp --</option>
                        @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="showtimes-container">
                    <p class="text-muted">Vui lòng chọn rạp để xem lịch chiếu.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('cinemaSelect').addEventListener('change', function () {
            const cinemaId = this.value;
            const movieId = {{ $movie->id }};
            const today = new Date().toISOString().split('T')[0];

            if (!cinemaId) {
                document.getElementById('showtimes-container').innerHTML = '<p class="text-muted">Vui lòng chọn rạp.</p>';
                return;
            }

            fetch(`/api/showtimes?movieId=${movieId}&cinemaId=${cinemaId}&date=${today}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if (data.length === 0) {
                        html = '<p class="text-muted">Không có suất chiếu nào.</p>';
                    } else {
                        html = '<div class="row">';
                        data.forEach(show => {
                            html += `
                            <div class="col-auto mb-2">
                                <a href="/book/{{ $movie->id }}/${show.id}" class="btn btn-outline-primary">
                                    ${show.starttime}
                                </a>
                            </div>`;
                        });
                        html += '</div>';
                    }
                    document.getElementById('showtimes-container').innerHTML = html;
                });
        });
    </script>
@endsection