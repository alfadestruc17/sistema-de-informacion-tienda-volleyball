<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // API Methods (para compatibilidad)
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string',
        ]);

        // Asignar rol de cliente por defecto si no se especifica
        $rolId = $request->rol_id ?? 3; // 3 = cliente

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => $rolId,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'user' => $user->load('role'),
            'token' => $token,
            'message' => 'Usuario registrado'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'user' => $user->load('role'),
                'token' => $token,
                'message' => 'Login exitoso'
            ], 200);
        }

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    // Web Methods (sesiones tradicionales)
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirigir según rol
            $user = Auth::user();
            if ($user->role->nombre === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role->nombre === 'cajero') {
                return redirect()->route('pos.index');
            } else {
                return redirect()->route('reservations.index');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function webRegister(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => 3, // cliente por defecto
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Cuenta creada exitosamente');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
