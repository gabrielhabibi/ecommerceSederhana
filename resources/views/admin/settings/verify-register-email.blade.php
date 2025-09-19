<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email</title>
    <style>
        .email-container { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; }
        .email-header { background: #f8f9fa; padding: 20px; text-align: center; }
        .email-body { padding: 20px; }
        .verify-button {
            display: inline-block;
            background: #3490dc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .email-footer { background: #f8f9fa; padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Verifikasi Email Anda</h2>
        </div>
        
        <div class="email-body">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Terima kasih telah mendaftar di <strong>{{ config('app.name') }}</strong>.</p>
            <p>Untuk mengaktifkan akun Anda, silakan klik tombol di bawah ini:</p>
            <a href="{{ $verificationUrl }}">Verifikasi Email</a>
            <p>Jika Anda tidak melakukan pendaftaran, abaikan email ini.</p>
        </div>
        
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
