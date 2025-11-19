<header class="cine-header">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <div class="header-container">

        <!-- LOGO -->
        <div class="header-left">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/CineJoy.png') }}" alt="Logo" class="logo" >
            </a>

            <!-- BUTTONS -->
            <!-- <a href="{{ url('/book') }}" class="btn-ticket">ĐẶT VÉ NGAY</a> -->
        </div>

        <!-- SEARCH BAR - HOẠT ĐỘNG  + CHUYỂN TRANG KHI ENTER -->
        <div class="header-search-wrapper">
            <form 
                action="{{ route('search.page') }}" 
                method="GET" 
                class="search-form d-flex align-items-center"
                style="position: relative; width: 340px;"
                onsubmit="return true;"
            >
                <input 
                    type="text" 
                    name="q" 
                    id="searchMovie" 
                    class="form-control" 
                    placeholder="Tìm phim..." 
                    value="{{ request('q') }}" 
                    autocomplete="off"
                    style="
                        width: 100%;
                        padding: 10px 40px 10px 16px;
                        border-radius: 25px;
                        border: none;
                        font-size: 15px;
                        outline: none;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    "
                >
                <button 
                    type="submit"
                    style="
                        position: absolute;
                        right: 12px;
                        top: 50%;
                        transform: translateY(-50%);
                        background: none;
                        border: none;
                        color: #666;
                        font-size: 18px;
                        cursor: pointer;
                        z-index: 10;
                        padding: 0;
                        width: 30px;
                        height: 30px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    "
                    title="Tìm kiếm"
                >
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>

        <!-- USER-->
        <!-- <div class="header-right">
            <a href="{{ url('/auth') }}" class="user">
                <i class="fa-regular fa-user"></i> Đăng nhập
            </a>
        </div> -->
        <!-- USER AREA -->
        <div class="header-right position-relative">
            @guest
                <a href="{{ route('auth') }}" class="d-flex align-items-center text-decoration-none text-white fw-medium">
                    <i class="fa-regular fa-user me-2"></i>
                    <span>Đăng nhập</span>
                </a>
            @endguest

            @auth
                <!-- Khu vực hover để hiện dropdown -->
                <div class="user-dropdown-hover">
                    <a href="javascript:void(0)" 
                    class="d-flex align-items-center text-decoration-none text-white fw-medium">

                        <!-- Avatar -->
                        @if(Auth::user()->imagename && file_exists(public_path('images/users/' . Auth::user()->imagename)))
                            <img src="{{ asset('images/users/' . Auth::user()->imagename) }}" 
                                alt="Avatar" 
                                class="rounded-circle me-2"
                                style="width: 36px; height: 36px; object-fit: cover; border: 2px solid #fff;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-dark"></i>
                            </div>
                        @endif

                        <!-- Tên người dùng -->
                        <span>{{ Auth::user()->name }}</span>
                    </a>

                    <!-- Dropdown hiện khi hover -->
                    <ul class="dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user-circle me-3"></i>
                                Thông tin cá nhân
                            </a>
                        </li>

                        @if(Auth::user()->rolename == 1 || Auth::user()->rolename === true)
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="#">
                                    <i class="fas fa-crown me-3"></i>
                                    Quản trị hệ thống
                                </a>
                            </li>
                        @endif

                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent py-2">
                                    <i class="fas fa-sign-out-alt me-3"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>

    </div>
</header>

<style>
    .header-search-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-form input:focus {
        box-shadow: 0 0 0 3px rgba(246, 231, 29, 0.3) !important;
        border: 1px solid #f6e71d !important;
    }

    
    .header-search-wrapper *,
    .search-form *,
    .search-form input,
    .search-form button {
        pointer-events: auto !important;
    }
.user-dropdown-hover {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

/* Hover vào tên → hiện menu */
.user-dropdown-hover:hover .dropdown-menu-custom {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    transition-delay: 0s;
}

/* Delay khi ẩn */
.user-dropdown-hover .dropdown-menu-custom {
    transition: all 0.35s ease;
    transition-delay: 0.3s;
}

.user-dropdown-hover .dropdown-menu-custom:hover {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    transition-delay: 0s !important;
}

/* MENU CHÍNH */
.dropdown-menu-custom {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 220px;
    background: #0c1325;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    padding: 8px 0;
    list-style: none;
    z-index: 9999;
    overflow: hidden;

    display: block;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.35s ease;
    transition-delay: 0s;
}

/* ĐẸP NHẤT Ở ĐÂY: Hover từng item → vàng + hiệu ứng */
.dropdown-menu-custom .dropdown-item {
    position: relative;
    color: #e0e0e0 !important;
    padding: 14px 24px !important;
    font-size: 0.98rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Nền trượt từ trái sang khi hover */
.dropdown-menu-custom .dropdown-item::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.12));
    transition: left 0.5s;
    z-index: -1;
}

/* Khi hover */
.dropdown-menu-custom .dropdown-item:hover {
    color: #FFD700 !important;           /* Vàng ánh kim */
    background: rgba(255, 215, 0, 0.08) !important;
    padding-left: 32px !important;
    transform: translateX(4px);
}

/* Nền trượt chạy khi hover */
.dropdown-menu-custom .dropdown-item:hover::before {
    left: 100%;
}

/* Icon phóng to nhẹ + đổi màu vàng */
.dropdown-menu-custom .dropdown-item i {
    transition: all 0.3s ease;
    min-width: 24px;
    font-size: 1.1rem;
}

.dropdown-menu-custom .dropdown-item:hover i {
    transform: scale(1.25) translateX(2px);
    color: #FFD700 !important;
}

/* Riêng mục Đăng xuất & Quản trị: đỏ → vàng đỏ */
.dropdown-menu-custom .dropdown-item.text-danger:hover {
    color: #ff6b6b !important;
    background: rgba(255, 107, 107, 0.12) !important;
}
.dropdown-menu-custom .dropdown-item.text-danger:hover i {
    color: #ff6b6b !important;
}

/* Đường kẻ */
.dropdown-menu-custom hr {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 8px 0;
}
</style>