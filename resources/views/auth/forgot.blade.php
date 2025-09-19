<x-guest-layout>
    <style>
        .forgot-title {
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
        .error-text {
            color: red;
            font-size: 0.875rem;
            margin-top: 4px;
        }
    </style>

    <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg rounded-4 p-4">
                <div class="card-header bg-white border-0 text-center mb-3">
                    <h3 class="forgot-title">{{ __('auth.forgot password') }}</h3>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-4">{{ __('auth.we will send reset link') }}</p>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        {{-- Email Field --}}
                        <div class="mt-3 text-start">
                            <x-input-label for="email" :value="__('auth.email')" class="d-block" />
                            <x-text-input id="email" 
                                        class="custom-input block mt-1 w-full"
                                        type="email" 
                                        name="email" 
                                        :value="old('email')" 
                                        required autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <div id="emailError" class="error-text text-start"></div>
                        </div>


                        {{-- Submit Button --}}
                        <div class="mt-4">
                            <x-primary-button class="w-100 py-3">
                                {{ __('auth.send reset link') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Realtime Validation Script --}}
    <script>
        const emailInput = document.getElementById("email");
        const emailError = document.getElementById("emailError");

        const messages = {
            emailInvalid: "{{ __('auth.email_invalid') }}"
        };

        emailInput.addEventListener("input", () => {
            const val = emailInput.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regex.test(val)) {
                emailError.textContent = messages.emailInvalid;
            } else {
                emailError.textContent = "";
            }
        });
    </script>
</x-guest-layout>
