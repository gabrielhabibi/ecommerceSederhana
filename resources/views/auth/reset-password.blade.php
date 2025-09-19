<x-guest-layout>
    <!-- Judul -->
    <h1 class="text-5xl md:text-6xl font-extrabold text-center mb-8 tracking-wide text-blue-600">
        {{ __('auth.reset password') }}
    </h1>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-black" />
            <x-text-input id="email"
                          class="block mt-1 w-full bg-white border border-blue-500 text-black rounded-lg focus:border-blue-600 focus:ring focus:ring-blue-200"
                          type="email"
                          name="email"
                          :value="old('email', $request->email)"
                          required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div class="mt-3 text-start">
            <x-input-label for="password" :value="__('auth.password')" class="d-block" />
            <x-text-input id="password"
                        type="password"
                        name="password"
                        class="custom-input block mt-1 w-full"
                        required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="passwordError" class="error-text text-start"></div>
        </div>

        {{-- Confirm Password --}}
        <div class="mt-3 text-start">
            <x-input-label for="password_confirmation" :value="__('auth.confirm password')" class="d-block" />
            <x-text-input id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="custom-input block mt-1 w-full"
                        required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div id="confirmPasswordError" class="error-text text-start"></div>
        </div>

        <!-- Button -->
        <div class="mt-4 text-center">
            <x-primary-button class="w-full justify-center">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>