<!DOCTYPE html>
<html>
<head>
    <title>{{ __('setting.Verify New Email') }}</title>
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
            <h2>{{ __('setting.Verify Email Change') }}</h2>
        </div>
        
        <div class="email-body">
            <p>{{ __('setting.Hello') }} <strong>{{ $user->name }}</strong>,</p>
            <p>{{ __('setting.Email Change Request') }} <strong>{{ $newEmail }}</strong></p>
            <p>{{ __('setting.Confirm Email Change') }}</p>
            <a class="verify-button" href="{{ $verificationUrl }}">{{ __('setting.Verify Email') }}</a>
            <p>{{ __('setting.Ignore If Not Requested') }}</p>
        </div>
        
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>