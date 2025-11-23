<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $user->formatted_dob = $user->dateofbirth
            ? Carbon::parse($user->dateofbirth)->format('d/m/Y')
            : '';

        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phonenumber' => 'required|regex:/^0[0-9]{9}$/|unique:users,phonenumber,' . $user->id,
            'dateofbirth' => 'required|date|before:' . Carbon::now()->subYears(16)->format('Y-m-d'),
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only(['name', 'email', 'phonenumber', 'dateofbirth', 'address']));

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->imagename && File::exists(public_path('images/users/' . $user->imagename))) {
            File::delete(public_path('images/users/' . $user->imagename));
        }

        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->extension();
        $file->move(public_path('images/users'), $filename);

        $user->update(['imagename' => $filename]);

        return back()->with('success', 'Thay đổi ảnh đại diện thành công!');
    }


    public function changePassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
{
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // So sánh mật khẩu dạng plain text
        if ($user->password !== $request->current_password) {
            return back()->withErrors(['current_password' => 'Mật khẩu cũ không đúng!']);
        }

        // Lưu mật khẩu mới dạng plain text
        $user->password = $request->password;
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

}