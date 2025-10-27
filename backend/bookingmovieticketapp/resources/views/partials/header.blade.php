<header class="site-header">
  <div class="container">
    <div class="header-logo">
      <a href="{{ url('/') }}">
        <img src="{{ asset('images/logo-cinestar.png') }}" alt="Cinestar Logo" height="40">
      </a>
    </div>
    <nav class="header-nav">
      <ul>
        <li><a href="{{ url('/showtimes') }}">Lịch chiếu</a></li>
        <li><a href="{{ url('/promotions') }}">Khuyến mãi</a></li>
        <li><a href="{{ url('/cinemas') }}">Chọn rạp</a></li>
        <li><a href="{{ url('/login') }}">Đăng nhập</a></li>
      </ul>
    </nav>
    <div class="header-action">
      <a class="btn btn-primary" href="{{ url('/book') }}">Đặt vé ngay</a>
    </div>
  </div>
</header>
