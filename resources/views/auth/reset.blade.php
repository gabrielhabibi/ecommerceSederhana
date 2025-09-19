<x-guest-layout>
    <style>
        .reset-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .custom-input {
            border: 2px solid #0d6efd !important;
            border-radius: 8px;
            padding: 10px;
        }
        .custom-btn {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            padding: 12px 40px;
        }
        .custom-btn:hover {
            background-color: #084298;
        }
        .text-error { color: red; font-size: 0.9rem; }
        .text-success { color: green; font-size: 0.9rem; }
    </style>

    <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg rounded-4 p-4">
                <div class="card-header bg-white border-0 text-center mb-3">
                    <h3 class="reset-title">{{ __('auth.reset password') }}</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center mb-4">{{ __('auth.reset your password here') }}</p>

                    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                        @csrf
                        {{-- Token --}}
                        <x-text-input type="hidden" name="token" :value="$request->token" />
                        <x-text-input type="hidden" name="email" :value="$request->email" />

                        {{-- Password --}}
                        <div class="mt-3 text-start">
                            <x-input-label for="password" :value="__('auth.password')" class="d-block" />
                            <x-text-input id="password"
                                          type="password"
                                          name="password"
                                          class="custom-input block mt-1 w-full"
                                          required />
                            <div id="passwordError" class="text-error"></div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mt-3 text-start">
                            <x-input-label for="password_confirmation" :value="__('auth.confirm password')" class="d-block" />
                            <x-text-input id="password_confirmation"
                                          type="password"
                                          name="password_confirmation"
                                          class="custom-input block mt-1 w-full"
                                          required />
                            <div id="confirmError" class="text-error"></div>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-4 text-center">
                            <button type="submit" class="custom-btn">
                                {{ __('auth.reset password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const confirmInput = document.getElementById("password_confirmation");
        const passwordError = document.getElementById("passwordError");
        const confirmError = document.getElementById("confirmError");

        const messages = {
            emailInvalid: "{{ __('auth.email_invalid') }}",
            passwordMin: "{{ __('auth.password_min') }}",
            passwordMax: "{{ __('auth.password_max') }}",
            passwordMismatch: "{{ __('auth.password_mismatch') }}"
        };

        passwordInput.addEventListener("input", () => {
            const val = passwordInput.value;
            if (val.length < 6) {
                passwordError.textContent = messages.passwordMin;
            } else if (val.length > 8) {
                passwordError.textContent = messages.passwordMax;
            } else {
                passwordError.textContent = "";
            }
        });

        confirmInput.addEventListener("input", () => {
            if (confirmInput.value !== passwordInput.value) {
                confirmError.textContent = messages.passwordMismatch;
            } else {
                confirmError.textContent = "";
            }
        });
    </script>
</x-guest-layout>