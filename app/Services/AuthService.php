<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function attemptLogin(LoginRequest $request): bool
    {
        return Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );
    }

    public function getRedirectRouteForUser(Authenticatable $user): string
    {
        $roleName = $user->role->nombre ?? '';

        return match ($roleName) {
            'admin' => 'admin.dashboard',
            'cajero' => 'pos.index',
            default => 'client.dashboard',
        };
    }

    public function register(RegisterRequest $request): Authenticatable
    {
        $user = $this->userRepository->create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => 3, // cliente por defecto
        ]);

        Auth::login($user);

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * @throws ValidationException
     */
    public function loginError(): void
    {
        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.'],
        ]);
    }
}
