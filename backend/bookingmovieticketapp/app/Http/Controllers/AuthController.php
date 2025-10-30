<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon; // Thư viện bắt buộc để quản lý thời gian

class AuthController extends Controller
{
    /**
     * Hiển thị trang chứa form Đăng nhập/Đăng ký.
     */
    public function showAuthPage()
    {
        // Trả về view auth/index.blade.php
        return view('auth.index');
    }

    // --- 1. CHỨC NĂNG ĐĂNG NHẬP (KHÔNG HASH) ---
    public function login(Request $request)
    {
        // Xác thực SĐT và Mật khẩu
        $request->validate([
            'phone' => 'required|string|max:20',
            'password' => 'required',
        ]);

        // TÌM NGƯỜI DÙNG BẰNG SĐT VÀ MẬT KHẨU THÔ (Plain Text)
        // Đây là bước quan trọng để bỏ qua Hash::check
        $user = User::where('phonenumber', $request->phone)
            ->where('password', $request->password)
            ->first();

        if ($user) {

            // 1. Kiểm tra trạng thái kích hoạt
            if ($user->isactive == 0) {
                return back()->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
            }

            // 2. Đăng nhập thành công
            Auth::login($user);

            // 3. Chuyển hướng theo Role (1=Admin, 0=User)
            if ($user->rolename == 1) {
                return redirect()->intended('/admin');
            }

            // FIX LỖI: Chuyển hướng về Route có tên 'home' (URL '/')
            return redirect()->intended(route('home'));
        }

        // Đăng nhập thất bại
        throw ValidationException::withMessages([
            'phone' => ['Số điện thoại hoặc mật khẩu không đúng.'],
        ]);
    }

    // --- 2. CHỨC NĂNG ĐĂNG KÝ (KHÔNG HASH) ---
    public function register(Request $request)
    {
        // Xác định ngày người dùng phải đủ 16 tuổi (16 năm trước ngày hôm nay)
        $minBirthDate = \Carbon\Carbon::now()->subYears(16)->format('Y-m-d');
        // Xác thực toàn bộ trường từ form Đăng ký
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|regex:/.+@gmail\.com$/|unique:users',
            // FIX 1: Số điện thoại phải là 10 số, bắt đầu bằng 0
            'phone' => 'required|string|regex:/^0[0-9]{9}$/|unique:users,phonenumber',

            // FIX 2: Ngày sinh phải đủ 16 tuổi trở lên
            'dob' => 'required|date|before_or_equal:' . $minBirthDate,
            'password' => 'required|min:6|confirmed',
            'agree' => 'accepted',
        ], [
            // Thông báo lỗi tiếng Việt tùy chỉnh
            'phone.unique' => 'Số điện thoại này đã được đăng ký.',
            'phone.regex' => 'Số điện thoại phải có 10 chữ số và bắt đầu bằng 0.',
            'email.regex' => 'Email phải có định dạng @gmail.com.', 
            'email.unique' => 'Email này đã được đăng ký. Vui lòng sử dụng email khác.', 
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'agree.accepted' => 'Bạn phải đồng ý với điều khoản dịch vụ.',
            // Thông báo lỗi mới cho 16 tuổi
            'dob.before_or_equal' => 'Bạn phải đủ 16 tuổi để đăng ký tài khoản.',
        ]);

        // Tạo User trong DB
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phone,
            'dateofbirth' => $request->dob,
            'password' => $request->password, // LƯU MẬT KHẨU DẠNG THÔ (Plain Text)
            'createdat' => Carbon::now(),
            'isactive' => 1,
            'rolename' => 0,
        ]);

        // Chuyển hướng về trang form và hiển thị thông báo thành công
        return redirect()->route('auth')->with('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
    }

    // --- 3. CHỨC NĂNG QUÊN MẬT KHẨU (Khôi phục) ---
    public function sendResetLinkByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9,10}$/',
        ]);

        // Tìm kiếm người dùng bằng cột 'phonenumber'
        $user = User::where('phonenumber', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Số điện thoại này chưa được đăng ký.']);
        }

        // TODO: Logic Gửi OTP/SMS/Link Khôi phục

        return back()->with('status', 'Đã gửi hướng dẫn khôi phục mật khẩu đến số điện thoại của bạn!');
    }

    /**
     * Xử lý chức năng Đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home')); // Chuyển về trang chủ sau khi logout
    }
}
