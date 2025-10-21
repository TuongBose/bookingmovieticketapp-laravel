<?php

namespace App\Services\User;

use App\Http\Requests\UserRequest;
use App\Models\User;

interface IUserService
{
    public function createUser(UserRequest $userRequest);
    public function loginCustomer(string $phoneNumber, string $password);
    public function loginAdmin(string $phoneNumber, string $password);
    public function getAllUserCustomer();
    public function getAllUserAdmin();
    public function updateUserStatus(int $userId, bool $isActive);
    public function getUserById(int $id);
    public function saveUser(User $user);
    public function updateUser(int $id, UserRequest $userRequest);
    public function checkExistsByphonenumber(string $phoneNumber);
    public function checkDoesNotExistsByphonenumber(string $phoneNumber);
    public function resetPassword(string $phoneNumber, string $password);
}
