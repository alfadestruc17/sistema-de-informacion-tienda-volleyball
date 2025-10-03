<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string',
            'rol_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => $request->rol_id,
        ]);

        return response()->json(['user' => $user, 'message' => 'Usuario registrado'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return response()->json(['user' => Auth::user(), 'message' => 'Login exitoso'], 200);
        }

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }
}
