@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">

            {{-- 1. NOTIFIKASI ERROR JIKA STATUS AKUN BELUM AKTIF / KREDENSIAL SALAH --}}
            @if($errors->has('identity_number') || $errors->has('password'))
                <div class="alert alert-danger alert-dismissible fade show mb-3 small shadow-sm border-0" role="alert" style="border-radius: 12px; background-color: #f8d7da; color: #721c24;">
                    <i class="fas fa-exclamation-circle me-2" style="font-size: 16px;"></i>
                    @if($errors->has('identity_number'))
                        {{ $errors->first('identity_number') }}
                    @else
                        {{ $errors->first('password') }}
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Menampilkan Alert umum jika ada status session kustom --}}
            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show mb-3 small shadow-sm border-0" role="alert" style="border-radius: 12px;">
                    <i class="fas fa-info-circle me-2"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Menampilkan Alert sukses setelah civitas selesai mengajukan aktivasi --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3 small shadow-sm border-0" role="alert" style="border-radius: 12px; background-color: #d4edda; color: #155724;">
                    <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-5"> 
                    
                    {{-- HEADER LOGIN --}}
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 80px; width: auto;">
                        <h4 class="mt-3 font-weight-bold">Inventaris PNUP</h4>
                        <p class="text-muted small">Silakan login menggunakan nomor identitas civitas</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" autocomplete="off">
                        @csrf

                        {{-- KOLOM INPUT NIM / NIP CIVITAS --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NIM / NIP Civitas</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fas fa-id-card text-muted"></i>
                                </span>
                                <input type="text" name="identity_number" 
                                       class="form-control @error('identity_number') is-invalid @enderror" 
                                       value="{{ old('identity_number') }}" required autofocus 
                                       placeholder="Masukkan NIM atau NIP Anda"
                                       autocomplete="off"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                        </div>

                        {{-- KOLOM INPUT PASSWORD --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required autocomplete="current-password" 
                                       placeholder="Masukkan password Anda"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-secondary" for="remember">
                                Ingat Saya di Perangkat Ini
                            </label>
                        </div>

                        {{-- TOMBOL SIGN IN --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm" style="border-radius: 12px; background-color: #007bff;">
                                <i class="fas fa-sign-in-alt me-1"></i> Masuk ke Sistem
                            </button>
                        </div>

                        {{-- LINK KE HALAMAN PENGAJUAN AKTIVASI WHATSAPP --}}
                        <div class="text-center mt-3">
                            <p class="mb-0 small text-muted">
                                Belum memiliki akses login atau akun belum aktif? <br>
                                <a href="{{ route('activation.form') }}" class="text-primary fw-bold text-decoration-none">
                                    Ajukan Aktivasi Akun di Sini
                                </a>
                            </p>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-2">
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