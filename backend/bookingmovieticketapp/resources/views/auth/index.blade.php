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

                <div class="forgot-link">
                    <a href="#" id="forgotLink">Quên mật khẩu?</a>
                </div>

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
        .auth-page-wrapper {
            /* Giả định layout chính (layouts.app) có header/footer, nên wrapper này chỉ cần căn giữa nội dung */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh; /* Chiếm phần lớn chiều cao viewport */
            margin: 50px 0;
            background: #f4f4f4; /* Nền nhẹ nhàng hơn để nội dung nổi bật */
        }

        .container {
            width: 400px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .tabs {
            display: flex;
            background: #3b0066;
        }

        .tab {
            flex: 1;
            text-align: center;
            color: white;
            padding: 12px;
            cursor: pointer;
            transition: 0.3s;
        }

        .tab.active {
            background: #8000ff;
        }

        .form {
            padding: 25px;
            display: none;
        }

        .form.active {
            display: block;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 0;
        }

        .error-message {
            color: #ff3333;
            font-size: 13px;
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #8000ff;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5c00cc;
        }

        .msg {
            text-align: center;
            padding: 10px 25px;
            color: red;
            background-color: #ffebeb;
            border-bottom: 1px solid #ff0000;
        }

        .msg.success {
            color: green;
            background-color: #ebfff1;
            border-bottom: 1px solid #00aa00;
        }

        .gender-group {
            margin-bottom: 16px;
        }

        .gender-group .gender-options {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .gender-options label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .terms-group {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            margin-bottom: 16px;
        }

        .terms-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #333;
            line-height: 1.4;
        }

        .terms-label a {
            color: #8000ff;
            text-decoration: none;
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
