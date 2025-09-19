<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- Judul -->
    <h1 class="text-5xl md:text-6xl font-extrabold text-center mb-8 tracking-wide text-blue-600">
        {{ __('auth.login') }}
    </h1>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-black" />
            <x-text-input id="email"
                class="block mt-1 w-full bg-white border border-blue-500 text-black rounded-lg focus:border-blue-600 focus:ring focus:ring-blue-200"
                type="email"
                name="email"
                :value="is_string(old('email')) ? old('email') : ''"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div id="emailError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-black" />
            <x-text-input id="password"
                class="block mt-1 w-full bg-white border border-blue-500 text-black rounded-lg focus:border-blue-600 focus:ring focus:ring-blue-200"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="passwordError" class="text-red-600 text-sm mt-1"></div>
        </div>

        <!-- Remember me -->
        <div class="flex items-center mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">
                <span class="ms-2 text-sm text-black">{{ __('auth.remember') }}</span>
            </label>
        </div>

        <!-- Login button -->
        <div class="mt-4 text-center">
            <x-primary-button class="w-full justify-center">
                {{ __('auth.login') }}
            </x-primary-button>
        </div>

        <!-- Forgot password -->
        <div class="mt-2 text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="underline text-sm text-black hover:text-gray-600">
                   Forgot your password?
                </a>
            @endif
        </div>

        <!-- Register -->
        <div class="mt-2 text-center">
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="underline text-sm text-black hover:text-gray-600">
                   Belum punya akun? Daftar sekarang
                </a>
            @endif
        </div>
    </form>
    {{-- JS Validation --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const email = document.getElementById("email");
            const password = document.getElementById("password");
            const emailError = document.getElementById("emailError");
            const passwordError = document.getElementById("passwordError");

            // Email validation
            email.addEventListener("input", function () {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regex.test(email.value)) {
                    emailError.textContent = "{{ __('auth.invalid email') }}";
                } else {
                    emailError.textContent = "";
                }
            });

            // Password validation
            password.addEventListener("input", function () {
                if (password.value.length < 6) {
                    passwordError.textContent = "{{ __('auth.password_min') }}";
                } else if (password.value.length > 8) {
                    passwordError.textContent = "{{ __('auth.password_max') }}";
                } else {
                    passwordError.textContent = "";
                }
            });
        });
    </script>
</x-guest-layout>