@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- EMAIL ADDRESS --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- PASSWORD BARU DENGAN IKON MATA DI DALAM INPUT --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <div class="position-relative">
                                    <input id="password" type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    <button type="button" id="togglePassword" class="btn btn-link text-muted position-absolute top-50 end-0 translate-middle-y me-2 text-decoration-none p-0 border-0 shadow-none">
                                        <i class="fas fa-eye" id="iconPassword"></i>
                                    </button>
                                </div>

                                @error('password')
                                    <span class="text-danger small mt-1 d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- CONFIRM PASSWORD DENGAN IKON MATA DI DALAM INPUT --}}
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <div class="position-relative">
                                    <input id="password-confirm" type="password" class="form-control pe-5" name="password_confirmation" required autocomplete="new-password">
                                    <button type="button" id="togglePasswordConfirm" class="btn btn-link text-muted position-absolute top-50 end-0 translate-middle-y me-2 text-decoration-none p-0 border-0 shadow-none">
                                        <i class="fas fa-eye" id="iconPasswordConfirm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT TOGGLE SHOW/HIDE PASSWORD --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupPasswordToggle(buttonId, inputId, iconId) {
        const toggleBtn = document.getElementById(buttonId);
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isPassword);
                toggleIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }
    }

    setupPasswordToggle('togglePassword', 'password', 'iconPassword');
    setupPasswordToggle('togglePasswordConfirm', 'password-confirm', 'iconPasswordConfirm');
});
</script>
@endsection