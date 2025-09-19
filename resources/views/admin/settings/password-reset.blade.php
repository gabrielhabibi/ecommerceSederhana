<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('setting.Reset Password') }}</title>
</head>
<body>
    <h2>{{ __('setting.Hello') }}</h2>
    <p>{{ __('setting.Password Reset Request') }}</p>
    <p>{{ __('setting.Your Reset Code') }}</p>
    <h3 style="color: red;">{{ $token }}</h3>
    <p>{{ __('setting.Enter This Code') }}</p>
    <br>
    <p>{{ __('setting.Ignore If Not Requested') }}</p>
</body>
</html>