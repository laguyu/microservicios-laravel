<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function all(): Collection
    {
        return Client::query()->latest()->get();
    }

    public function findByEmail(string $email): ?Client
    {
        return Client::query()->where('email', strtolower(trim($email)))->first();
    }

    public function create(array $attributes): Client
    {
        return Client::query()->create([
            'name' => trim((string) ($attributes['name'] ?? '')),
            'email' => strtolower(trim((string) ($attributes['email'] ?? ''))),
            'company' => isset($attributes['company']) ? trim((string) $attributes['company']) : null,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    public function count(): int
    {
        return Client::query()->count();
    }
}
