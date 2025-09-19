<x-guest-layout>
    <!-- Judul -->
    <h1 class="text-5xl md:text-6xl font-extrabold text-center mb-8 tracking-wide text-blue-600">
        {{ __('auth.register') }}
    </h1>
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('auth.name')" class="text-black" />
            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div id="nameError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('auth.email')" class="text-black" />
            <x-text-input id="email" type="email" name="email" 
                class="block mt-1 w-full" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div id="emailError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('auth.password')" class="text-black" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="passwordError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('auth.confirm password')" class="text-black" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div id="confirmPasswordError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Address -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('auth.address')" class="text-black" />
            <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address')" required />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
            <div id="addressError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('auth.phone number')" class="text-black" />
            <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            <div id="phoneError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Sudah punya akun -->
        <div class="mt-4 text-center">
            <a class="underline text-sm text-black hover:text-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                href="{{ route('login') }}">
                {{ __('auth.already have account') }}
            </a>
        </div>

        <!-- Register button -->
        <div class="mt-4 text-center">
            <x-primary-button class="w-full justify-center">
                {{ __('auth.register') }}
            </x-primary-button>
        </div>
    </form>
    {{-- JS Validation --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const name = document.getElementById("name");
            const email = document.getElementById("email");
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("password_confirmation");
            const address = document.getElementById("address");
            const phone = document.getElementById("phone");

            const nameError = document.getElementById("nameError");
            const emailError = document.getElementById("emailError");
            const passwordError = document.getElementById("passwordError");
            const confirmPasswordError = document.getElementById("confirmPasswordError");
            const addressError = document.getElementById("addressError");
            const phoneError = document.getElementById("phoneError");

            // Name
            name.addEventListener("input", function () {
                nameError.textContent = name.value.length < 3 ? "{{ __('auth.name too short') }}" : "";
            });

            // Email
            email.addEventListener("input", function () {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                emailError.textContent = !regex.test(email.value) ? "{{ __('auth.email_invalid') }}" : "";
            });

            // Password
            password.addEventListener("input", function () {
                if (password.value.length < 6) {
                    passwordError.textContent = "{{ __('auth.password_min') }}";
                } else if (password.value.length > 8) {
                    passwordError.textContent = "{{ __('auth.password_max') }}";
                } else {
                    passwordError.textContent = "";
                }
            });

            // Confirm Password
            confirmPassword.addEventListener("input", function () {
                confirmPasswordError.textContent =
                    confirmPassword.value !== password.value ? "{{ __('auth.password mismatch') }}" : "";
            });

            // Address
            address.addEventListener("input", function () {
                addressError.textContent = address.value.length < 5 ? "{{ __('auth.address too short') }}" : "";
            });

            // Phone
            phone.addEventListener("input", function () {
                const regex = /^[0-9]+$/;
                if (!regex.test(phone.value)) {
                    phoneError.textContent = "{{ __('auth.invalid phone') }}";
                } else if (phone.value.length < 11) {
                    phoneError.textContent = "{{ __('auth.phone too short') }}";
                } else if (phone.value.length > 13) {
                    phoneError.textContent = "{{ __('auth.phone too long') }}";
                } else {
                    phoneError.textContent = "";
                }
            });
        });
    </script>
</x-guest-layout>
