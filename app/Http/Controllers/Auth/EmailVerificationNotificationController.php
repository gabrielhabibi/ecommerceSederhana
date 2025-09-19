<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard')->with('status', 'already-verified');
        }

        // Pastikan ini dijalankan
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Email berhasil diverifikasi!');
    }
}
