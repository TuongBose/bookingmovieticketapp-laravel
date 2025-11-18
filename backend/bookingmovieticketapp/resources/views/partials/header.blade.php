<header class="cine-header">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <div class="header-container">

        <!-- LOGO -->
        <div class="header-left">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/CineJoy.png') }}" alt="Logo" class="logo">
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
        <div class="header-right">
            <a href="{{ url('/auth') }}" class="user">
                <i class="fa-regular fa-user"></i> Đăng nhập
            </a>
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
</style>