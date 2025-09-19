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
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }
    </style>

    <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg rounded-4 p-4">
                <div class="card-header bg-white border-0 text-center mb-3">
                    <h3 class="forgot-title">{{ __('Forgot Password') }}</h3>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-4">{{ __('We will send a link to reset your password') }}</p>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="text-start">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" 
                                   class="form-control custom-input @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn custom-btn btn-lg">
                                {{ __('Send Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>