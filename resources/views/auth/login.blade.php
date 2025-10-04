<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservas de Voleibol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-white">
                <span class="text-2xl">🏐</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Iniciar Sesión
            </h2>
            <p class="mt-2 text-center text-sm text-gray-200">
                Sistema de Reservas de Canchas de Voleibol
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('email') border-red-300 @enderror"
                           placeholder="Correo electrónico"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Contraseña">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-200">
                        Recordarme
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('register') }}" class="font-medium text-indigo-200 hover:text-indigo-100">
                        ¿No tienes cuenta? Regístrate
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <!-- Usuarios de prueba -->
        <div class="mt-8 bg-white bg-opacity-10 rounded-lg p-4">
            <h3 class="text-lg font-medium text-white mb-3">Usuarios de Prueba:</h3>
            <div class="space-y-2 text-sm text-gray-200">
                <div><strong>Admin:</strong> admin@example.com / password</div>
                <div><strong>Cajero:</strong> cajero@example.com / password</div>
                <div><strong>Cliente:</strong> cliente@example.com / password</div>
            </div>
        </div>
    </div>
</body>
</html>