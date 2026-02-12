@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('heading', 'Crear cuenta')
@section('subheading', 'Regístrate para reservar canchas')

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf
    <div>
        <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1">Nombre completo</label>
        <input id="nombre" name="nombre" type="text" autocomplete="name" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800 @error('nombre') border-red-500 @enderror"
               placeholder="Tu nombre"
               value="{{ old('nombre') }}">
        @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="email" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800 @error('email') border-red-500 @enderror"
               placeholder="correo@ejemplo.com"
               value="{{ old('email') }}">
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="telefono" class="block text-sm font-medium text-slate-700 mb-1">Teléfono (opcional)</label>
        <input id="telefono" name="telefono" type="tel" autocomplete="tel"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800"
               placeholder="Teléfono"
               value="{{ old('telefono') }}">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800 @error('password') border-red-500 @enderror"
               placeholder="Mínimo 8 caracteres">
        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800"
               placeholder="Repite la contraseña">
    </div>
    <p class="text-sm text-slate-600 text-center">
        <a href="{{ route('login') }}" class="text-slate-700 hover:text-slate-900 font-medium">¿Ya tienes cuenta? Inicia sesión</a>
    </p>
    <button type="submit" class="w-full py-2.5 px-4 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 focus:ring-2 focus:ring-offset-2 focus:ring-sky-400">
        Crear cuenta
    </button>
</form>
@endsection
