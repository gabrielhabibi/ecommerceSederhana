<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token verifikasi tidak valid.'], 404);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json(['message' => 'Email berhasil diverifikasi.']);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah diverifikasi.'], 400);
        }

        $token = Str::random(64);
        $user->email_verification_token = $token;
        $user->save();

        Mail::to($user->email)->send(new VerifyEmail($user, $token));

        return response()->json(['message' => 'Link verifikasi telah dikirim ulang.']);
    }
}