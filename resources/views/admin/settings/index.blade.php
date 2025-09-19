@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('setting.Change Email') }}</h2>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('settings.email.change') }}" method="POST" id="emailChangeForm">
        @csrf
        <div class="form-group">
            <label for="old_email">{{ __('setting.Old Email') }}</label>
            <input type="email" name="old_email" id="old_email" class="form-control" required>
            <small id="oldEmailError" class="text-danger d-none">{{ __('setting.Old Email Error') }}</small>
        </div>

        <div class="form-group mt-3">
            <label for="new_email">{{ __('setting.New Email') }}</label>
            <input type="email" name="new_email" id="new_email" class="form-control" required>
            <small id="newEmailError" class="text-danger d-none">{{ __('setting.New Email Error') }}</small>
        </div>

        <div class="form-group mt-3">
            <label for="current_password">{{ __('setting.Current Password') }}</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">{{ __('setting.Update Email') }}</button>
    </form>

    {{-- Form logout tersembunyi --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const oldEmailInput = document.getElementById('old_email');
            const newEmailInput = document.getElementById('new_email');
            const oldEmailError = document.getElementById('oldEmailError');
            const newEmailError = document.getElementById('newEmailError');
            const currentUserEmail = @json($user->email);

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            oldEmailInput.addEventListener('input', function () {
                if (oldEmailInput.value.trim() !== currentUserEmail) {
                    oldEmailError.classList.remove('d-none');
                } else {
                    oldEmailError.classList.add('d-none');
                }
            });

            newEmailInput.addEventListener('input', function () {
                if (!validateEmail(newEmailInput.value) || newEmailInput.value.trim() === currentUserEmail) {
                    newEmailError.classList.remove('d-none');
                } else {
                    newEmailError.classList.add('d-none');
                }
            });

            const form = document.getElementById('emailChangeForm');
            form.addEventListener('submit', function (e) {
                const confirmed = confirm("{{ __('setting.Email Change Confirmation') }}");
                if (!confirmed) {
                    e.preventDefault();
                }
            });

            setTimeout(() => {
                let alertBox = document.getElementById('success-alert');
                if (alertBox) {
                    alertBox.style.display = 'none';
                    document.getElementById('logout-form').submit();
                }
            }, 3000);
        });
    </script>
@endsection