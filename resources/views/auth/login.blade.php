@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('heading', 'Iniciar sesión')
@section('subheading', 'Sistema de reservas de canchas de voleibol')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="email" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800 @error('email') border-red-500 @enderror"
               placeholder="correo@ejemplo.com"
               value="{{ old('email') }}">
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-400 focus:border-slate-400 text-slate-800"
               placeholder="Contraseña">
    </div>
    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center text-slate-600">
            <input name="remember" type="checkbox" class="rounded border-slate-300 text-slate-600 focus:ring-slate-400">
            <span class="ml-2">Recordarme</span>
        </label>
        <a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-800">Crear cuenta</a>
    </div>
    <button type="submit" class="w-full py-2.5 px-4 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 focus:ring-2 focus:ring-offset-2 focus:ring-sky-400">
        Entrar
    </button>
</form>
@endsection

@section('footer')
<div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-left">
    <p class="font-medium text-slate-700 mb-2">Usuarios de prueba</p>
    <ul class="text-slate-600 text-sm space-y-1">
        <li><strong>Admin:</strong> admin@example.com / password</li>
        <li><strong>Cajero:</strong> cajero@example.com / password</li>
        <li><strong>Cliente:</strong> cliente@example.com / password</li>
    </ul>
</div>
@endsection
