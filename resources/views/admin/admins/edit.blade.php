@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('admin.edit_admin') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container mt-4">
        <form action="{{ route('admins.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('admin.name') }}</label>
                <input type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name" name="name"
                    value="{{ old('name', $user->name) }}" required>
                <div class="invalid-feedback d-none" id="nameError">{{ __('admin.error_name') }}</div>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('admin.email') }}</label>
                <input type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email" name="email"
                    value="{{ old('email', $user->email) }}" required>
                <div class="invalid-feedback d-none" id="emailError">{{ __('admin.error_email') }}</div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role --}}
            <div class="mb-3">
                <label for="role_id" class="form-label">{{ __('admin.role') }}</label>
                <select class="form-select @error('role_id') is-invalid @enderror"
                        id="role_id" name="role_id" required>
                    <option value="">{{ __('admin.error_role_required') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-none" id="roleError">{{ __('admin.error_role_required') }}</div>
                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <label for="address" class="form-label">{{ __('admin.address') }}</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                        id="address" name="address"
                        rows="3" required>{{ old('address', $user->address) }}</textarea>
                <div class="invalid-feedback d-none" id="addressError">{{ __('admin.error_address_length') }}</div>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Phone Number --}}
            <div class="mb-3">
                <label for="phone_number" class="form-label">{{ __('admin.phone_number') }}</label>
                <input type="text"
                    class="form-control @error('phone_number') is-invalid @enderror"
                    id="phone_number" name="phone_number"
                    value="{{ old('phone_number', $user->phone_number) }}" required>
                <div class="invalid-feedback d-none" id="phoneError">{{ __('admin.error_phone') }}</div>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
                <a href="{{ route('admins.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </div>
        </form>
    </div>


    {{-- Frontend validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone_number');
            const address = document.getElementById('address');
            const role = document.getElementById('role_id');

            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');
            const phoneError = document.getElementById('phoneError');
            const addressError = document.getElementById('addressError');
            const roleError = document.getElementById('roleError');

            name.addEventListener('input', () => {
                const regex = /^[A-Za-z\s]+$/;
                if (!regex.test(name.value)) {
                    name.classList.add('is-invalid'); nameError.classList.remove('d-none');
                } else {
                    name.classList.remove('is-invalid'); nameError.classList.add('d-none');
                }
            });

            email.addEventListener('input', () => {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regex.test(email.value)) {
                    email.classList.add('is-invalid'); emailError.classList.remove('d-none');
                } else {
                    email.classList.remove('is-invalid'); emailError.classList.add('d-none');
                }
            });

            phone.addEventListener('input', () => {
                const regex = /^\d{11,13}$/;
                if (!regex.test(phone.value)) {
                    phone.classList.add('is-invalid'); phoneError.classList.remove('d-none');
                } else {
                    phone.classList.remove('is-invalid'); phoneError.classList.add('d-none');
                }
            });

            address.addEventListener('input', () => {
                if (address.value.length < 5) {
                    address.classList.add('is-invalid'); addressError.classList.remove('d-none');
                } else {
                    address.classList.remove('is-invalid'); addressError.classList.add('d-none');
                }
            });

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