<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function webLogin(LoginRequest $request): RedirectResponse
    {
        if ($this->authService->attemptLogin($request)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return redirect()->route($this->authService->getRedirectRouteForUser($user));
        }

        return back()
            ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'])
            ->onlyInput('email');
    }

    public function webRegister(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request);
        return redirect()->route('client.dashboard')->with('success', 'Cuenta creada exitosamente');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}
