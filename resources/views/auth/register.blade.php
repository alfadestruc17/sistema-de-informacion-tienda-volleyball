@extends('layouts.auth')

@section('title', 'Crear cuenta')

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

            <form method="POST" action="{{ route('register.post') }}" class="flex flex-col gap-4">
                @csrf
                {{-- Nombre con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input id="nombre" name="nombre" type="text" autocomplete="name" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="Nombre completo"
                           value="{{ old('nombre') }}">
                </div>
                @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                {{-- Email con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="correo@ejemplo.com"
                           value="{{ old('email') }}">
                </div>
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                {{-- Teléfono (opcional) con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input id="telefono" name="telefono" type="tel" autocomplete="tel"
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="Teléfono (opcional)"
                           value="{{ old('telefono') }}">
                </div>

                {{-- Contraseña con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="Contraseña (mín. 8 caracteres)">
                </div>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                {{-- Confirmar contraseña con icono --}}
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="w-full pl-11 pr-4 py-3 rounded-lg border-0 text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-gray-400"
                           style="background-color: #E0DDD4;"
                           placeholder="Confirmar contraseña">
                </div>

                <button type="submit" class="w-full py-3 px-4 font-medium rounded-lg text-white focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mt-1"
                        style="background-color: #42474E;">
                    Crear cuenta
                </button>
            </form>

            <p class="mt-6 text-sm text-center" style="color: #6B7280;">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #42474E;">Inicia sesión</a>
            </p>
        </div>

        {{-- Panel derecho: REGISTRO rotado + botón cerrar --}}
        <div class="flex-1 relative flex items-center justify-center py-12 px-6" style="background-color: #343A40;">
            <a href="{{ url('/') }}" class="absolute top-4 right-4 p-1 rounded hover:bg-white/10 transition" style="color: #AAAAAA;" aria-label="Cerrar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
            <span class="text-2xl font-semibold tracking-widest whitespace-nowrap select-none" style="color: #D3D3D3; transform: rotate(-90deg);"></span>
        </div>
    </div>
</div>
@endsection
