<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRequest;
use App\Services\User\IUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    protected $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data['password'] !== $data['retypepassword']) {
                return response()->json(['error' => 'Xác nhận mật khẩu không khớp'], 400);
            }

            $newUser = $this->userService->createUser($request);
            return response()->json($newUser);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function loginCustomer(UserLoginRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userService->loginCustomer($data['phonenumber'], $data['password']);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function loginAdmin(UserLoginRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userService->loginAdmin($data['phonenumber'], $data['password']);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllUserAdmin()
    {
        return response()->json($this->userService->getAllUserAdmin());
    }

    public function getAllUserCustomer()
    {
        return response()->json($this->userService->getAllUserCustomer());
    }

    public function updateUserStatus($id, Request $request)
    {
        try {
            $isActive = $request->input('isActive');
            $this->userService->updateUserStatus($id, $isActive);
            return response()->json(['message' => 'Cập nhật trạng thái người dùng thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function uploadUserImage($id, Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json(['error' => 'Không có tệp hình ảnh được gửi.'], 400);
            }

            $file = $request->file('image');

            if (!$file->isValid() || !str_starts_with($file->getMimeType(), 'image/')) {
                return response()->json(['error' => 'Tệp phải là hình ảnh (jpg, png, v.v.).'], 400);
            }

            $user = $this->userService->getUserById($id);

            if (!$user) {
                return response()->json(['error' => 'Không tìm thấy người dùng.'], 404);
            }

            // Xóa file cũ
            if ($user->imagename && File::exists(public_path('images/users/' . $user->imagename))) {
                File::delete(public_path('images/users/' . $user->imagename));
            }

            // Tạo thư mục nếu chưa có
            $uploadPath = public_path('images/users/');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true);
            }

            // Lưu file mới
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = 'user_' . $id . '_' . time() . '.' . $extension;
            $file->move($uploadPath, $fileName);

            // Cập nhật vào DB
            $user->imagename = $fileName;
            $this->userService->saveUser($user);

            return response()->json(['message' => 'Tải lên hình ảnh thành công.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi khi lưu tệp: ' . $e->getMessage()], 400);
        }
    }

    public function getUserImage($id)
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user || !$user->imagename) {
                return response()->json(['error' => 'Không tìm thấy ảnh người dùng.'], 404);
            }

            $path = public_path('images/users/' . $user->imagename);
            if (!File::exists($path)) {
                return response()->json(['error' => 'Tệp không tồn tại.'], 404);
            }

            $mimeType = File::mimeType($path);
            return Response::file($path, ['Content-Type' => $mimeType]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Lỗi đường dẫn hình ảnh: ' . $e->getMessage()], 400);
        }
    }

    public function getUserById($id)
    {
        try {
            return response()->json($this->userService->getUserById($id));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateUser($id, UserRequest $request)
    {
        try {
            $updatedUser = $this->userService->updateUser($id, $request->validated());
            return response()->json($updatedUser);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkExistsByphonenumber($phonenumber)
    {
        if ($this->userService->checkExistsByphonenumber($phonenumber)) {
            return response()->json(['status' => 'exists', 'message' => 'Số điện thoại đã tồn tại']);
        }
        return response()->json(['status' => 'available', 'message' => 'Số điện thoại có thể sử dụng']);
    }

    public function checkDoesNotExistsByphonenumber($phonenumber)
    {
        if ($this->userService->checkDoesNotExistsByphonenumber($phonenumber)) {
            return response()->json(['status' => 'available', 'message' => 'Số điện thoại không tồn tại']);
        }
        return response()->json(['status' => 'exists', 'message' => 'Số điện thoại đã tồn tại']);
    }

    public function resetPassword(UserLoginRequest $request)
    {
        try {
            $data = $request->validated();
            return response()->json($this->userService->resetPassword($data['phonenumber'], $data['password']));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
