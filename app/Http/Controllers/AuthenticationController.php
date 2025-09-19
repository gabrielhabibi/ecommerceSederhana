<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthenticationController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => 'required',
            'address' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{11,13}$/',
        ]);

        // Ambil role 'admin' dari tabel roles
        $userRole = Role::where('role_name', 'admin')->first();

        if (!$userRole) {
            return back()->withErrors(['role' => 'Role admin tidak ditemukan.']);
        }

        $token = Str::random(64);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'address' => $request->address,
            'phone_number' => $request->phone,
            'email_verification_token' => $token,
        ]);

        Mail::to($user->email)->send(new VerifyEmail($user, $token));
        Auth::logout();
        return redirect('/login')->with('success', 'Registrasi berhasil! Cek email untuk verifikasi.');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (!$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'Verifikasi email diperlukan. Cek email Anda atau <a href="'.route('verification.send').'">kirim ulang</a> link verifikasi.'
            ])->withInput();
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard'); // ✅ Semua user langsung ke dashboard
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
    public function logout(Request $request)
    {
        session()->forget('plain_password'); // hapus kalau masih ada

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('admin.welcome');
    }

    public function settingView()
    {
        $user = Auth::user();
        return view('admin.settings.index', compact('user'));
    }

    public function requestEmailChange(Request $request)
    {
        Log::info('Step 1: Masuk ke requestEmailChange');

        $validated = $request->validate([
            'old_email' => ['required', 'email', 'in:'.Auth::user()->email],
            'new_email' => ['required', 'email', 'unique:users,email'],
            'current_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $token = Str::random(64);

        // Simpan email baru sementara dan token verifikasi
        $user->new_email = $validated['new_email'];
        $user->email_verification_token = $token;
        $user->save();

        // Simpan password ke session buat logoutOtherDevices nanti
        session(['email_change_password' => $validated['current_password']]);

        Log::info('Step 2: Data user diperbarui', ['token' => $token]);

        // Kirim link verifikasi ke EMAIL LAMA
        Mail::to($validated['old_email'])->send(new VerifyEmail(
            $user,
            $token,
            $validated['new_email']
        ));

        Log::info('Step 3: Email verifikasi terkirim ke '.$validated['old_email']);

        return back()->with('success', __('setting.success_email_change'));
    }

    // Tampilkan form forgot password
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Proses kirim email reset password
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Tampilkan form reset password
    public function resetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Proses simpan password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
