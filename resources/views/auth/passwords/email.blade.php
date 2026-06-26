@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('Reset Password'))

@section('auth_body')
<p class="auth-box-msg">Masukkan Gmail Anda yang terdaftar untuk menerima link reset password.</p>

{{-- Tampilkan Status Berhasil Dikirim --}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check-circle mr-1"></i> {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    {{-- Kolom Input Gmail Bergaya AdminLTE Input-Group --}}
    <div class="input-group mb-3">
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               placeholder="Contoh: user@gmail.com"
               required 
               autocomplete="email" 
               autofocus>
               
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope text-secondary"></span>
            </div>
        </div>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Tombol Kirim --}}
    <div class="row mb-0">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">
                <i class="fas fa-paper-plane mr-1"></i> {{ __('Send Password Reset Link') }}
            </button>
        </div>
    </div>
</form>

{{-- Navigasi Kembali --}}
<p class="mt-3 mb-1 text-center">
    <a href="{{ route('login') }}" class="text-primary small font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Login
    </a>
</p>
@stop