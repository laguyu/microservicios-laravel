<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    #[Endpoint(operationId: 'auth.register', title: 'Registrar usuario', description: 'Registra un nuevo usuario y devuelve un token de acceso.')]
    #[BodyParameter('name', 'Nombre completo del usuario.', required: true, type: 'string', example: 'Ana García')]
    #[BodyParameter('email', 'Correo electrónico único del usuario.', required: true, type: 'string', format: 'email', example: 'ana@ejemplo.com')]
    #[BodyParameter('password', 'Contraseña segura con al menos 8 caracteres.', required: true, type: 'string', format: 'password', example: 'Secret123')]
    #[Response(status: 201, description: 'Usuario registrado correctamente.')]
    #[Response(status: 422, description: 'Los datos enviados no cumplen la validación.')]
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => hash('sha256', $user->email.':'.$user->id.':'.now()->timestamp),
            ],
        ], 201);
    }

    #[Endpoint(operationId: 'auth.login', title: 'Iniciar sesión', description: 'Autentica a un usuario y devuelve un token de acceso.')]
    #[BodyParameter('email', 'Correo electrónico registrado.', required: true, type: 'string', format: 'email', example: 'ana@ejemplo.com')]
    #[BodyParameter('password', 'Contraseña del usuario.', required: true, type: 'string', format: 'password', example: 'Secret123')]
    #[Response(status: 200, description: 'Sesión iniciada correctamente.')]
    #[Response(status: 422, description: 'Credenciales inválidas.')]
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => hash('sha256', $user->email.':'.$user->id.':'.now()->timestamp),
            ],
        ]);
    }
}
