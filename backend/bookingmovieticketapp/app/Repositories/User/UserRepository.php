<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements IUserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function existsByPhonenumber(string $phoneNumber): bool
    {
        return User::where('phonenumber', $phoneNumber)->exists();
    }

    public function findByPhonenumber(string $phoneNumber): ?User
    {
        return User::where('phonenumber', $phoneNumber)->first();
    }

    public function findByRolenameTrue(): Collection
    {
        return User::where('rolename', true)->get();
    }

    public function findByRolenameFalse(): Collection
    {
        return User::where('rolename', false)->get();
    }
}
