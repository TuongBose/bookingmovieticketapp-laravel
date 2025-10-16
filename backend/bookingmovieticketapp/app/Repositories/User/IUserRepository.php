<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Collection;

interface IUserRepository
{
    public function existsByPhonenumber(string $phoneNumber): bool;

    public function findByPhonenumber(string $phoneNumber): ?User;

    public function findByRolenameTrue(): Collection;

    public function findByRolenameFalse(): Collection;
}
