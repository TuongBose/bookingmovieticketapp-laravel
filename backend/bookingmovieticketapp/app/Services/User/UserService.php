<?php

namespace App\Services\User;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\User\IUserRepository;
use Exception;

class UserService implements IUserService
{
    protected $userRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(
        IUserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function createUser(UserRequest $userRequest)
    {
        $phoneNumber = $userRequest->phonenumber;

        if ($this->userRepository->existsByPhonenumber($phoneNumber)) {
            throw new Exception("Số điện thoại này đã tồn tại");
        }

        $user = User::create([
            'name' => $userRequest->name,
            'email' => $userRequest->email,
            'password' => $userRequest->password, // có thể dùng Hash::make()
            'phonenumber' => $userRequest->phonenumber,
            'dateofbirth' => $userRequest->dateofbirth ?? null,
            'isactive' => true,
            'rolename' => false, // false = customer, true = admin
        ]);

        return $user;
    }

    public function loginCustomer(string $phoneNumber, string $password)
    {
        $user = $this->userRepository->findByPhonenumber($phoneNumber);

        if (!$user->isactive || !$user->rolename || $user->password !== $password) {
            throw new Exception("Sai số điện thoại hoặc mật khẩu");
        }

        return $user;
    }

    public function loginAdmin(string $phoneNumber, string $password)
    {
        $user = $this->userRepository->findByPhonenumber($phoneNumber);
        if (!$user) {
            throw new Exception("Sai số điện thoại hoặc mật khẩu");
        }

        if (!$user->isactive || !$user->rolename || $user->password !== $password) {
            throw new Exception("Sai số điện thoại hoặc mật khẩu");
        }

        return $user;
    }

    public function getAllUserCustomer()
    {
        return $this->userRepository->findByRolenameFalse();
    }

    public function getAllUserAdmin()
    {
        return $this->userRepository->findByRolenameTrue();
    }

    public function updateUserStatus(int $userId, bool $isActive)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'isactive' => $isActive
        ]);
    }

    public function getUserById(int $id)
    {
        return User::findOrFail($id);
    }

    public function saveUser(User $user)
    {
        $user->save();
    }

    public function updateUser(int $id, UserRequest $userRequest)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $userRequest->name,
            'email' => $userRequest->email,
            'password' => $userRequest->password,
            'phonenumber' => $userRequest->phonenumber,
            'address' => $userRequest->address,
            'dateofbirth' => $userRequest->dateofbirth
        ]);

        return $user;
    }

    public function checkExistsByphonenumber(string $phoneNumber)
    {
        $user = $this->userRepository->findByPhonenumber($phoneNumber);
        if (!$user) {
            return $this->userRepository->existsByPhonenumber($phoneNumber);
        }

        if ($user->rolename)
            return true;
        return $user->isactive;
    }

    public function checkDoesNotExistsByphonenumber(string $phoneNumber)
    {
        $user = $this->userRepository->findByPhonenumber($phoneNumber);
        if (!$user)
            return true;

        if ($user->rolename)
            return true;
        return !$user->isactive;
    }

    public function resetPassword(string $phoneNumber, string $password)
    {
        $user = $this->userRepository->findByPhonenumber($phoneNumber);
        if (!$user) {
            throw new Exception("Số điện thoại này không tồn tại");
        }

        $user->update([
            'password' => $password,
        ]);

        return $user;
    }
}
