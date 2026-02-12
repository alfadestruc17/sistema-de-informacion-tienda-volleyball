<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Reservas') - Arena Sport C.B</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    {{-- Paleta: fondo slate-50, cards blanco con borde slate-200, botón primario slate-700, éxito emerald --}}
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    {{-- Acento sutil: sky/azul claro en navbar, bordes y hovers --}}
    @auth
        @include('partials.navbar')
    @endauth

    <main class="@auth max-w-6xl mx-auto px-4 sm:px-6 py-8 @endauth">
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
