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
                    
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 80px; width: auto;">
                        <h4 class="mt-3 font-weight-bold">Inventaris PNUP</h4>
                        <p class="text-muted small">Silakan login menggunakan nomor identitas civitas</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" autocomplete="off">
                        @csrf {{-- Pelindung dari error 419 --}}

                        {{-- KOLOM INPUT NIM / NIP CIVITAS --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NIM / NIP Civitas</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fas fa-id-card text-muted"></i>
                                </span>
                                {{-- Menghapus class is-invalid dari identity_number agar border input tidak ikut memerah --}}
                                <input type="text" name="identity_number" 
                                       class="form-control" 
                                       value="{{ old('identity_number') }}" required autofocus 
                                       placeholder="Masukkan NIM atau NIP Anda"
                                       autocomplete="off"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                        </div>

                        {{-- KOLOM INPUT PASSWORD & TEMPAT NOTIFIKASI ERROR KUSTOM --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required autocomplete="new-password" 
                                       placeholder="Masukkan password Anda"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                            
                            {{-- Memunculkan Pesan Error Tepat Di Bawah Input Group Password --}}
                            @error('password')
                                <span class="text-danger font-weight-bold d-block mt-2 small" role="alert">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-secondary" for="remember">
                                Ingat Saya di Perangkat Ini
                            </label>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm" style="border-radius: 12px; background-color: #007bff;">
                                <i class="fas fa-sign-in-alt mr-1"></i> Masuk ke Sistem
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link btn-sm text-decoration-none text-muted small" href="{{ route('password.request') }}">
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