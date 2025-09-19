<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $newEmail;

    public function __construct($user, $token, $newEmail = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->newEmail = $newEmail;
    }

    public function build()
    {
        $verifyUrl = route('verification.verify.token', $this->token);

        if ($this->newEmail) {
            // 🔹 Proses ubah email → kirim ke EMAIL LAMA
            return $this->to($this->user->email) 
                        ->subject('Verifikasi Perubahan Email')
                        ->view('admin.settings.verify-new-email')
                        ->with([
                            'user' => $this->user,
                            'newEmail' => $this->newEmail,
                            'verificationUrl' => $verifyUrl
                        ]);
        } else {
            // 🔹 Proses registrasi → kirim ke EMAIL YANG DIDFTAR (baru)
            return $this->to($this->user->email)
                        ->subject('Verifikasi Email')
                        ->view('admin.settings.verify-register-email')
                        ->with([
                            'user' => $this->user,
                            'verificationUrl' => $verifyUrl
                        ]);
        }
    }
}