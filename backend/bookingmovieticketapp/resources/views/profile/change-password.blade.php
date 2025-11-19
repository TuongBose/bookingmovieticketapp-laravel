@extends('layouts.app')

@section('title', 'Đổi mật khẩu - CINESTAR')

@section('content')
<div class="profile-container min-vh-100" style="background: linear-gradient(to bottom, #0f0f1a, #1a1a2e);">
    <div class="container py-5">
        <div class="row g-5 justify-content-center">

            <!-- Sidebar trái-->
            <div class="col-lg-4">
                <div class="user-card rounded-4 overflow-hidden shadow-2xl border border-purple-500 border-opacity-30"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="p-5 text-white text-center">

                        <!-- Avatar-->
                        <div class="position-relative d-inline-block mb-4">
                            <div class="avatar-container">
                                @if(Auth::user()->imagename && file_exists(public_path('images/users/'.Auth::user()->imagename)))
                                    <img src="{{ asset('images/users/'.Auth::user()->imagename) }}" 
                                         class="avatar-img" alt="Avatar">
                                @else
                                    <div class="avatar-default">
                                        <i class="fas fa-user fa-4x opacity-70"></i>
                                    </div>
                                @endif
                            </div>
                            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="avatar-form">
                                @csrf
                                <label class="avatar-btn">
                                    <i class="fas fa-camera"></i>
                                    <input type="file" name="avatar" accept="image/*" onchange="this.form.submit()">
                                </label>
                            </form>
                        </div>

                        <h3 class="fw-bold mb-1 text-shadow">{{ Auth::user()->name }}</h3>
                        <p class="mb-3 opacity-75">Thành viên của CineJoy</p>
                        <div class="membership-badge mb-4">
                            <span class="badge-text">C'Friends</span>
                        </div>

                        <div class="menu mt-5">
                            <a href="{{ route('profile') }}" class="menu-item">
                                <i class="fas fa-user-circle"></i> Thông tin khách hàng
                            </a>
                            <a href="{{ route('profile.change-password') }}" class="menu-item active">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="menu-item text-danger border-0 bg-transparent w-100 text-start">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form đổi mật khẩu -->
            <div class="col-lg-8">
                <div class="form-card rounded-4 shadow-2xl border-0" style="background: #1a1a2e;">
                    <div class="p-5">

                        <h2 class="text-center mb-5 title-gradient fw-bold">
                            ĐỔI MẬT KHẨU
                        </h2>

                        <!-- Success -->
                        @if(session('success'))
                            <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <!-- Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-triangle me-2"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update-password') }}" method="POST">
                            @csrf
                            <div class="row g-4">

                                <div class="col-12">
                                    <label class="form-label text-warning fw-bold">Mật khẩu cũ *</label>
                                    <div class="position-relative">
                                        <input type="password" name="current_password" class="form-control form-control-lg custom-input" required>
                                        <i class="fas fa-eye toggle-password position-absolute end-0 top-50 translate-middle-y me-3"></i>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-warning fw-bold">Mật khẩu mới *</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="form-control form-control-lg custom-input" required>
                                        <i class="fas fa-eye toggle-password position-absolute end-0 top-50 translate-middle-y me-3"></i>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-warning fw-bold">Xác nhận mật khẩu mới *</label>
                                    <div class="position-relative">
                                        <input type="password" name="password_confirmation" class="form-control form-control-lg custom-input" required>
                                        <i class="fas fa-eye toggle-password position-absolute end-0 top-50 translate-middle-y me-3"></i>
                                    </div>
                                </div>

                            </div>

                            <div class="text-center mt-5">
                                <button type="submit" class="btn-save px-7 py-3 rounded-pill shadow-lg">
                                    ĐỔI MẬT KHẨU
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



@if(session('success') || $errors->any())
<div id="customModal" 
     style="position:fixed; top:0; left:0; width:100%; height:100%; 
            background:rgba(0,0,0,0.7); display:flex; justify-content:center; 
            align-items:center; z-index:9999; backdrop-filter:blur(5px);">
    
    <div style="background: linear-gradient(135deg, #8E2DE2, #4A00E0); 
                padding:50px 40px; border-radius:24px; width:440px; max-width:90%; 
                text-align:center; color:white; box-shadow:0 20px 60px rgba(0,0,0,0.6);
                border:1px solid rgba(255,255,255,0.1);">
        
        <p style="margin:0 0 35px 0; font-size:24px; font-weight:600; text-shadow:0 2px 10px rgba(0,0,0,0.5);">
            {{ session('success') ?? 'Vui lòng kiểm tra lại thông tin!' }}
        </p>

        <button onclick="document.getElementById('customModal').remove()" 
                style="padding:14px 50px; border:none; border-radius:50px; 
                       font-weight:bold; font-size:18px; cursor:pointer;
                       background: linear-gradient(45deg, #FFD700, #FFB800);
                       color:#000; box-shadow:0 8px 25px rgba(255,215,0,0.5);
                       transition:all 0.4s ease;">
            OK
        </button>
    </div>
</div>

<script>
    setTimeout(() => {
        const modal = document.getElementById('customModal');
        if (modal) modal.remove();
    }, 6000);
</script>
@endif
@endsection

@section('styles')
<style>
    .text-shadow { text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    .title-gradient {
        background: linear-gradient(90deg, #ffd700, #ff8c00, #ff6b6b);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        font-size: 2.5rem;
    }

    .avatar-container { width: 140px; height: 140px; margin: 0 auto; position: relative; }
    .avatar-img, .avatar-default {
        width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
        border: 6px solid rgba(255,255,255,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    }
    .avatar-default { background: rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; }

    .avatar-btn { position:absolute; bottom:8px; right:8px; width:44px; height:44px;
        background:#ffd700; border-radius:50%; display:flex; justify-content:center; align-items:center;
        cursor:pointer; transition:0.3s; }
    .avatar-btn input{ display:none; }

    .membership-badge{ background:linear-gradient(45deg,#ffd700,#ffb800); padding:10px 30px; border-radius:50px; font-weight:bold; }
    .menu-item{ display:flex; align-items:center; padding:14px 20px; margin-bottom:8px; color:white; transition:0.3s; border-radius:12px; }
    .menu-item:hover, .menu-item.active{ background:rgba(255,255,255,0.2); transform:translateX(12px); }

    .custom-input{ background:rgba(255,255,255,0.08)!important; color:white!important; border-radius:12px!important; }
    .btn-save{ background:linear-gradient(45deg,#ffd700,#ffb800); font-weight:bold; }
    .btn-save:hover{ transform:translateY(-3px); }

    .toggle-password { cursor:pointer; color:#bbb; font-size:1.2rem; }
    .toggle-password:hover { color:#ffd700; }
</style>
@endsection
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".toggle-password").forEach(icon => {
        icon.addEventListener("click", function () {
            const input = this.parentElement.querySelector("input");

            if (input.type === "password") {
                input.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    });
});
</script>
@endsection
