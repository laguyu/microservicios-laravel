<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class AuthService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function register(array $payload): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException('name, email y password son obligatorios.');
        }

        if ($this->userRepository->findByEmail($email) !== null) {
            throw new InvalidArgumentException('Ya existe un usuario con ese correo.');
        }

        $user = $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => hash('sha256', $email.':'.$user->id.':'.now()->timestamp),
        ];
    }

    public function login(array $payload): array
    {
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException('email y password son obligatorios.');
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw new InvalidArgumentException('Credenciales inválidas.');
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => hash('sha256', $email.':'.$user->id.':'.now()->timestamp),
        ];
    }
}
