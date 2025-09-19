<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    
    public function __construct($token) // ✅ perbaikan, terima $token
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Reset Password')
            ->view('admin.settings.password-reset')
            ->with(['token' => $this->token]);
    }
}