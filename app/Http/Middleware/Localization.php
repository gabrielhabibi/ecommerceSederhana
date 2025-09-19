<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // ✅ kalau user sudah login → ambil dari session
            if (session()->has('locale')) {
                app()->setLocale(session('locale'));
            } else {
                app()->setLocale(config('app.locale')); // fallback
            }
        } else {
            // ✅ kalau masih guest → juga ambil dari session
            if (session()->has('locale')) {
                app()->setLocale(session('locale'));
            } else {
                app()->setLocale(config('app.locale')); // fallback
            }
        }

        return $next($request);
    }
}