@extends('layouts.app')

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="tabs">
            <div class="tab active" id="loginTab">Đăng nhập</div>
            <div class="tab" id="registerTab">Đăng ký</div>
        </div>

        {{-- Hiển thị thông báo chung từ Controller (error, success, status) --}}
        @if(session('error'))
        <div class="msg">{{ session('error') }}</div>
        @endif
        @if(session('success'))
        <div class="msg success">{{ session('success') }}</div>
        @endif
        @if (session('status'))
        <div class="msg success">{{ session('status') }}</div>
        @endif

        {{-- Form Đăng nhập --}}
        <form method="POST" action="{{ route('login') }}" class="form active" id="loginForm">
            @csrf

            <label>Số điện thoại</label>
            <div class="input-group">
                <input type="text" name="phone" placeholder="Số điện thoại" required value="{{ old('phone') }}">
            </div>
            {{-- Hiển thị lỗi xác thực cho trường 'phone' --}}
            @error('phone')
            <span class="error-message">{{ $message }}</span>
            @enderror

            <label>Mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mật khẩu" required>
            </div>
            @error('password')
            <span class="error-message">{{ $message }}</span>
            @enderror

            <button type="submit">Đăng nhập</button>
        </form>

        {{-- Form Đăng ký --}}
        {{-- Class 'old-register' giúp JS xác định form nào có lỗi khi chuyển hướng từ Controller --}}
        <form method="POST" action="{{ route('register') }}" class="form {{ old('name') || $errors->has('register') ? 'old-register' : '' }}" id="registerForm">
            @csrf
            <label>Họ và tên</label>
            <div class="input-group">
                <input type="text" name="name" placeholder="Họ và tên" required value="{{ old('name') }}">
            </div>
            @error('name')<span class="error-message">{{ $message }}</span>@enderror

            <label>Email</label>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
            </div>
            @error('email')<span class="error-message">{{ $message }}</span>@enderror

            <label>Số điện thoại</label>
            <div class="input-group">
                <input type="text" name="phone" placeholder="Số điện thoại" required value="{{ old('phone') }}">
            </div>
            @error('phone')<span class="error-message">{{ $message }}</span>@enderror

            <div class="gender-group">
                <label>Giới tính</label>
                <div class="gender-options">
                    <label><input type="radio" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}> Nam</label>
                    <label><input type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}> Nữ</label>
                    <label><input type="radio" name="gender" value="other" {{ old('gender') == 'other' ? 'checked' : '' }}> Chưa xác định</label>
                </div>
            </div>

            <label>Ngày sinh</label>
            <div class="input-group">
                <input type="date" name="dob" required value="{{ old('dob') }}">
            </div>
            @error('dob')<span class="error-message">{{ $message }}</span>@enderror

            <label>Mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mật khẩu" required>
            </div>
            @error('password')<span class="error-message">{{ $message }}</span>@enderror

            <label>Xác nhận mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
            </div>

            <div class="terms-group">
                <label class="terms-label">
                    <input type="checkbox" id="agree" name="agree" {{ old('agree') ? 'checked' : '' }}>
                    <span>
                        Bằng việc đăng ký tài khoản, tôi đồng ý với
                        <a href="#">điều khoản dịch vụ</a> và
                        <a href="#">chính sách bảo mật</a> của CineJoy.
                    </span>
                </label>
            </div>
            @error('agree')<span class="error-message">{{ $message }}</span>@enderror

            <button type="submit">Đăng ký</button>
        </form>

        {{-- Form Quên mật khẩu --}}
        <form method="POST" action="{{ route('password.phone') }}" class="form" id="forgotForm">
            @csrf
            <h3>Khôi phục mật khẩu</h3>
            <p style="font-size: 13px; color: #555;">
                Nhập số điện thoại bạn đã đăng ký để nhận hướng dẫn đặt lại mật khẩu.
            </p>

            <label>Số điện thoại</label>
            <div class="input-group">
                <input type="text" name="phone" placeholder="Nhập số điện thoại của bạn" required value="{{ old('phone') }}">
            </div>
            @error('phone')<span class="error-message">{{ $message }}</span>@enderror


            <button type="submit">Gửi yêu cầu khôi phục</button>

            <div class="forgot-link" style="margin-top: 12px;">
                <a href="#" id="backToLogin">← Quay lại đăng nhập</a>
            </div>
        </form>

    </div>
</div>

{{-- Thêm CSS trực tiếp vào đây vì nó là form độc lập, hoặc bạn nên chuyển nó vào file CSS chung --}}
<style>
    /* 🌌 Nền toàn trang – dùng ảnh nền phía sau */
    .auth-page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: url('/images/rapchieuphim.png') no-repeat center center/cover;
        position: relative;
        overflow: hidden;
    }

    /* 🌫️ Lớp phủ làm dịu màu nền */
    .auth-page-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(50, 0, 80, 0.45);
        backdrop-filter: blur(4px);
        z-index: 1;
    }

    /* 📦 Form container – nổi trên nền */
    .container {
        position: relative;
        z-index: 2;
        width: 420px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        color: white;
    }

    /* Tabs */
    .tabs {
        display: flex;
        justify-content: space-between;
        background: linear-gradient(90deg, rgba(170, 0, 255, 0.6), rgba(120, 0, 200, 0.5));
        backdrop-filter: blur(10px);
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        overflow: hidden;
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .tab {
        flex: 1;
        text-align: center;
        padding: 14px 0;
        cursor: pointer;
        font-weight: 600;
        color: #e5d9ff;
        transition: all 0.3s ease;
        background: transparent;
        border: none;
    }

    .tab.active {
        background: linear-gradient(90deg, #b000ff, #7a00ff);
        color: #fff;
        box-shadow: inset 0 -2px 0 rgba(255, 255, 255, 0.4);
    }

    .tab:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    /* Form */
    .form {
        padding: 28px;
        display: none;
        color: #fff;
    }

    .form.active {
        display: block;
    }

    .input-group {
        position: relative;
        margin-bottom: 16px;
    }

    .input-group input {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 8px;
        outline: none;
        background: rgba(255, 255, 255, 0.25);
        color: white;
        font-size: 15px;
    }

    .input-group input::placeholder {
        color: #ddd;
    }

    .error-message {
        color: #ff7777;
        font-size: 13px;
        margin-top: -8px;
        margin-bottom: 10px;
        display: block;
    }

    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(90deg, #a000ff, #6f00ff);
        border: none;
        color: white;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        font-weight: bold;
    }

    button:hover {
        background: linear-gradient(90deg, #bf00ff, #9000ff);
        transform: scale(1.02);
    }

    .msg {
        text-align: center;
        padding: 10px 25px;
        color: #ffbbbb;
        background-color: rgba(255, 0, 0, 0.1);
        border-bottom: 1px solid #ff0000;
    }

    .msg.success {
        color: #00ffae;
        background-color: rgba(0, 255, 100, 0.1);
        border-bottom: 1px solid #00aa00;
    }

    .gender-group label,
    .terms-label {
        color: #eee;
    }

    .terms-label a {
        color: #ffccff;
        text-decoration: underline;
    }

    .forgot-link a {
        color: #ffccff;
        font-size: 14px;
        text-decoration: underline;
    }

    /* Căn giãn và căn giữa nhóm giới tính */
    .gender-options {
        display: flex;
        justify-content: space-between;
        /* giãn đều các lựa chọn */
        align-items: center;
        gap: 18px;
        /* tạo khoảng cách đều */
        margin: 6px 0 16px;
    }

    .gender-options label {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        font-size: 15px;
        white-space: nowrap;
        /* không bị xuống dòng */
    }

    /* Tùy chỉnh radio để trông đẹp hơn */
    .gender-options input[type="radio"] {
        accent-color: #a64aff;
        /* màu tím CineJoy */
        width: 16px;
        height: 16px;
    }

    .auth-page-wrapper .container {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
</style>


<script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const forgotForm = document.getElementById('forgotForm');
    const forgotLink = document.getElementById('forgotLink');
    const backToLogin = document.getElementById('backToLogin');

    // Hàm chuyển form (formToActivate là element, tabToActivate là element hoặc null)
    const switchForm = (formToActivate, tabToActivate) => {
        // Ẩn tất cả form
        document.querySelectorAll('.form').forEach(form => form.classList.remove('active'));
        // Bỏ active tất cả tab
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));

        if (formToActivate) formToActivate.classList.add('active');
        if (tabToActivate) tabToActivate.classList.add('active');
    };

    // Gắn sự kiện chuyển tab
    loginTab.onclick = () => switchForm(loginForm, loginTab);
    registerTab.onclick = () => switchForm(registerForm, registerTab);

    // Chuyển sang form Quên mật khẩu
    forgotLink.onclick = (e) => {
        e.preventDefault();
        switchForm(forgotForm, null); // Không active tab nào khi ở form quên MK
    };

    // Quay lại Đăng nhập
    backToLogin.onclick = (e) => {
        e.preventDefault();
        switchForm(loginForm, loginTab);
    };

    // --- Xử lý trạng thái mặc định khi có lỗi/thông báo (Quan trọng) ---
    document.addEventListener('DOMContentLoaded', () => {
        // Kiểm tra lỗi (errors) hoặc status (session status/success)
        const hasRegisterErrors = registerForm.querySelector('.error-message');
        const hasLoginErrors = loginForm.querySelector('.error-message');
        const hasForgotErrors = forgotForm.querySelector('.error-message');
        const hasStatus = document.querySelector('.msg.success');
        const hasError = document.querySelector('.msg:not(.success)');

        // 1. Ưu tiên: Lỗi Đăng ký (vì có nhiều trường nhất)
        if (hasRegisterErrors) {
            switchForm(registerForm, registerTab);
        }
        // 2. Tiếp theo: Lỗi Đăng nhập
        else if (hasLoginErrors) {
            switchForm(loginForm, loginTab);
        }
        // 3. Tiếp theo: Lỗi Quên mật khẩu hoặc thông báo trạng thái Quên mật khẩu
        else if (hasForgotErrors || (hasStatus && forgotForm.contains(document.activeElement))) {
            switchForm(forgotForm, null);
        }
        // 4. Tiếp theo: Thông báo thành công (Có thể từ Đăng ký) hoặc lỗi chung
        else if (hasStatus || hasError) {
            // Nếu có old data từ form Đăng ký (người dùng đã cố gắng đăng ký)
            if (registerForm.classList.contains('old-register')) {
                switchForm(registerForm, registerTab);
            } else {
                // Mặc định: form Đăng nhập
                switchForm(loginForm, loginTab);
            }
        }
        // 5. Mặc định: form Đăng nhập
        else {
            switchForm(loginForm, loginTab);
        }
    });
</script>
@endsection