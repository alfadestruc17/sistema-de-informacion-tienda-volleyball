@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
{{-- Fondo azul-gris oscuro --}}
<div class="min-h-screen flex flex-col items-center justify-center p-4" style="background-color: #6D808A;">
    {{-- Contenedor del formulario: dos paneles --}}
    <div class="w-full max-w-2xl flex rounded-2xl overflow-hidden shadow-2xl flex-shrink-0">
        {{-- Panel izquierdo: formulario (fondo beige claro) --}}
        <div class="flex-[2] flex flex-col p-10" style="background-color: #EDEBE6;">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg text-sm" style="background-color: #d1fae5; color: #065f46;">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg text-sm" style="background-color: #fee2e2; color: #991b1b;">
                    @foreach($errors->all() as $e) {{ $e }} @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-6">
                @csrf
                {{-- Campo usuario/email con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="correo@ejemplo.com"
                           value="{{ old('email') }}">
                </div>
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                {{-- Campo contraseña con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="Contraseña">
                </div>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                {{-- ¿Olvidaste tu contraseña? y Recordarme --}}
                <div class="flex items-center justify-between text-sm">
                    <a href="#" class="hover:underline" style="color: #6B7280;">¿Olvidaste tu contraseña? </a>
                    <label class="flex items-center cursor-pointer" style="color: #6B7280;">
                        <input name="remember" type="checkbox" class="rounded border-gray-400 text-gray-600 focus:ring-gray-400"
                               style="background-color: #E0DDD4;">
                        <span class="ml-2">Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 font-medium rounded-lg text-white focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        style="background-color: #42474E;">
                    Entrar
                </button>
            </form>

            <p class="mt-6 text-sm text-center" style="color: #6B7280;">
                ¿No tienes cuenta? <a href="{{ route('register') }}" class="font-medium hover:underline" style="color: #42474E;">Crear cuenta</a>
            </p>
        </div>

        {{-- Panel derecho: LOGIN rotado + botón cerrar --}}
        <div class="flex-1 relative flex items-center justify-center py-12 px-6" style="background-color: #343A40;">
            <a href="{{ url('/') }}" class="absolute top-4 right-4 p-1 rounded hover:bg-white/10 transition" style="color: #AAAAAA;" aria-label="Cerrar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
            <span class="text-2xl font-semibold tracking-widest whitespace-nowrap select-none" style="color: #D3D3D3; transform: rotate(-90deg);"></span>
        </div>
    </div>

    {{-- Usuarios de prueba (debajo del card en móvil/escritorio) --}}
    <div class="mt-8 max-w-2xl w-full rounded-xl p-4 text-left text-sm" style="background-color: rgba(52,58,64,0.6); color: #D3D3D3;">
        <p class="font-medium mb-2">Usuarios de prueba</p>
        <ul class="space-y-1 opacity-90">
            <li><strong>Admin:</strong> admin@example.com / password</li>
            <li><strong>Cajero:</strong> cajero@example.com / password</li>
            <li><strong>Cliente:</strong> cliente@example.com / password</li>
        </ul>
    </div>
</div>
@endsection
