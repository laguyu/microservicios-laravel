<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', strtolower(trim($email)))->first();
    }

    public function create(array $attributes): User
    {
        return User::query()->create([
            'name' => trim((string) $attributes['name']),
            'email' => strtolower(trim((string) $attributes['email'])),
            'password' => $attributes['password'],
        ]);
    }
}
