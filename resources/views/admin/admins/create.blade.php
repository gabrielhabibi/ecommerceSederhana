@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('admin.add_admin') }}</h2>

    {{-- Pesan error dari backend --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admins.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name">{{ __('admin.name') }}</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email">{{ __('admin.email') }}</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password">{{ __('admin.password') }}</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation">{{ __('admin.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="address">{{ __('admin.address') }}</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone_number">{{ __('admin.phone_number') }}</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>

        <!-- Role otomatis admin -->
        <div class="mb-3">
            <label for="role">{{ __('admin.role') }}</label>
            <input type="text" value="admin" class="form-control" disabled>
            <!-- Supaya tetap terkirim ke controller -->
            <input type="hidden" name="role" value="admin">
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
    </form>

    {{-- ✅ Realtime Validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone_number');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const address = document.getElementById('address');
            const role = document.getElementById('role_id');

            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');
            const phoneError = document.getElementById('phoneError');
            const passwordLengthError = document.getElementById('passwordLengthError');
            const passwordMatchError = document.getElementById('passwordMatchError');
            const addressError = document.getElementById('addressError');
            const roleError = document.getElementById('roleError');

            // Name
            name.addEventListener('input', () => {
                const regex = /^[A-Za-z\s]+$/;
                if (!regex.test(name.value)) {
                    name.classList.add('is-invalid'); nameError.classList.remove('d-none');
                } else {
                    name.classList.remove('is-invalid'); nameError.classList.add('d-none');
                }
            });

            // Email
            email.addEventListener('input', () => {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regex.test(email.value)) {
                    email.classList.add('is-invalid'); emailError.classList.remove('d-none');
                } else {
                    email.classList.remove('is-invalid'); emailError.classList.add('d-none');
                }
            });

            // Phone
            phone.addEventListener('input', () => {
                const regex = /^\d{11,13}$/;
                if (!regex.test(phone.value)) {
                    phone.classList.add('is-invalid'); phoneError.classList.remove('d-none');
                } else {
                    phone.classList.remove('is-invalid'); phoneError.classList.add('d-none');
                }
            });

            // Password
            function validatePassword() {
                const passVal = password.value;
                const confirmVal = confirmPassword.value;

                if (passVal.length < 6 || passVal.length > 8) {
                    password.classList.add('is-invalid'); passwordLengthError.classList.remove('d-none');
                } else {
                    password.classList.remove('is-invalid'); passwordLengthError.classList.add('d-none');
                }

                if (passVal !== confirmVal) {
                    confirmPassword.classList.add('is-invalid'); passwordMatchError.classList.remove('d-none');
                } else {
                    confirmPassword.classList.remove('is-invalid'); passwordMatchError.classList.add('d-none');
                }
            }

            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);

            // Address
            address.addEventListener('input', () => {
                if (address.value.length < 5) {
                    address.classList.add('is-invalid'); addressError.classList.remove('d-none');
                } else {
                    address.classList.remove('is-invalid'); addressError.classList.add('d-none');
                }
            });

            // Role
            role.addEventListener('change', () => {
                if (!role.value) {
                    role.classList.add('is-invalid'); roleError.classList.remove('d-none');
                } else {
                    role.classList.remove('is-invalid'); roleError.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
