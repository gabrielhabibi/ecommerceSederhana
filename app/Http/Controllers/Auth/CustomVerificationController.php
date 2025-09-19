<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CustomVerificationController extends Controller
{
    public function verify(Request $request, $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Token verifikasi tidak valid.']);
        }

        // Kalau user belum pernah verifikasi sebelumnya
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        // Kalau ini verifikasi ganti email
        if ($user->new_email) {
            $user->email = $user->new_email;
            $user->new_email = null;

            // Logout dari semua device lain
            if (session('plain_password')) {
                Auth::logoutOtherDevices(session('plain_password'));
            }
        }

        // Hapus token
        $user->email_verification_token = null;
        $user->save();

        // Logout supaya harus login ulang dengan email baru
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Email berhasil diverifikasi. Silakan login kembali.');
    }
}
