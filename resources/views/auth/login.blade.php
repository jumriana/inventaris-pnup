@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">
            {{-- Menampilkan Alert jika ada error umum (seperti Session Expired) --}}
            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-5"> 
                    
                    <!-- Logo dan Judul -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 80px; width: auto;">
                        <h4 class="mt-3 font-weight-bold">Inventaris PNUP</h4>
                        <p class="text-muted small">Silakan login untuk mengakses sistem</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf {{-- Pelindung dari error 419 --}}

                        <!-- Input Email -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required autocomplete="email" autofocus 
                                   placeholder="admin01@gmail.com">
                            
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Input Password -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required autocomplete="current-password" 
                                   placeholder="..........">
                            
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Fitur Ingat Saya (Remember Me) -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">
                                Ingat Saya
                            </label>
                        </div>

                        <!-- Tombol Login -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2" style="border-radius: 10px; background-color: #007bff;">
                                Login
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link btn-sm text-decoration-none" href="{{ route('password.request') }}">
                                    Lupa Password?
                                </a>
                            </div>
                        @endif
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection