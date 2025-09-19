<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();

        // Kalau belum login
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized');
        }

        // Ambil role dan permission dari DB
        $role = $user->role;
        $permissions = $role->permissions ?? [];

        // Super admin → selalu lolos
        if ($role->role_name === 'super admin') {
            return $next($request);
        }

        // Kalau butuh permission spesifik → cek
        if ($permission && !in_array($permission, $permissions)) {
            abort(403, 'Unauthorized: no access to ' . $permission);
        }

        return $next($request);
    }
}