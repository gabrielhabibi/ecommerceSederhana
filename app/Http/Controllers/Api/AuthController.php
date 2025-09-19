<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewUserRegistered;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^[0-9]{11,13}$/',
        ]);

        $role = Role::where('role_name', 'user')->first();

        $token = Str::random(64);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role?->id,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'email_verification_token' => $token,
        ]);

        Mail::to($user->email)->send(new VerifyEmail($user, $token));

        // 🔥 kirim notifikasi ke semua admin & super admin
        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['admin', 'super admin']);
        })->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegistered($user));
            \Log::info('Recipients for NewUserRegistered:', $admins->pluck('id','name')->toArray());
        } else {
            \Log::warning('No admin/super admin found for NewUserRegistered notification!');
        }

        return response()->json([
            'message' => 'Registrasi berhasil, silakan cek email untuk verifikasi.'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Email belum diverifikasi.'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}