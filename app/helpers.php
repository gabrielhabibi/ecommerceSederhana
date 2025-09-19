<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasPermission')) {
    function hasPermission($permission)
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return false;
        }

        // super admin otomatis punya semua izin
        if ($user->role->role_name === 'super admin') {
            return true;
        }

        return in_array($permission, $user->role->permissions ?? []);
    }
}
