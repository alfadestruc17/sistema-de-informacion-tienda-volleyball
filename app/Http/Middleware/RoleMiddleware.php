<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!auth()->check()) {
            abort(401, 'No autenticado');
        }

        $user = auth()->user();
        if (!$user || !$user->role) {
            abort(403, 'Usuario sin rol asignado');
        }

        $allowedRoles = explode(',', $roles);
        $allowedRoles = array_map('trim', $allowedRoles);

        if (!in_array($user->role->nombre, $allowedRoles)) {
            abort(403, 'Acceso denegado - Rol insuficiente');
        }

        return $next($request);
    }
}
