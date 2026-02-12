<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Arena Sport C.B')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center py-12 px-4">
    {{-- Acento sutil: sky en botones y enlaces --}}
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white border border-slate-200 text-2xl shadow-sm">🏐</span>
            <h1 class="mt-4 text-2xl font-bold text-slate-800">@yield('heading', 'Arena Sport C.B')</h1>
            <p class="mt-1 text-sm text-slate-500">@yield('subheading', 'Sistema de reservas')</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    @foreach($errors->all() as $e) {{ $e }} @endforeach
                </div>
            @endif
            @yield('content')
        </div>
        @hasSection('footer')
            <div class="mt-6 text-center text-sm text-slate-500">@yield('footer')</div>
        @endif
    </div>
</body>
</html>
