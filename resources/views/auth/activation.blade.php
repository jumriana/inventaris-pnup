@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">

            {{-- Menampilkan Alert jika ada error atau info --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3 small" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show mb-3 small" role="alert">
                    <i class="fas fa-info-circle me-1"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-5"> 
                    
                    {{-- HEADER LOGO & JUDUL --}}
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 80px; width: auto;">
                        <h4 class="mt-3 font-weight-bold">Ajukan Akses Akun</h4>
                        <p class="text-muted small">Masukkan nomor identitas civitas, email, dan WhatsApp untuk mendapatkan password bawaan.</p>
                    </div>

                    <form method="POST" action="{{ route('activation.request') }}" autocomplete="off">
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
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                            @error('identity_number')
                                <span class="text-danger d-block mt-1 small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- UPDATE BARU: KOLOM INPUT EMAIL CIVITAS --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Aktif</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" required
                                       placeholder="Contoh: nama@gmail.com"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                            @error('email')
                                <span class="text-danger d-block mt-1 small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- KOLOM INPUT NOMOR WHATSAPP --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid #e9ecef;">
                                    <i class="fab fa-whatsapp text-muted" style="font-size: 16px;"></i>
                                </span>
                                <input type="text" name="no_hp" 
                                       class="form-control @error('no_hp') is-invalid @enderror" 
                                       value="{{ old('no_hp') }}" required
                                       placeholder="Contoh: 08123456789"
                                       style="border-radius: 0 12px 12px 0 !important; border-left: none !important;">
                            </div>
                            <span class="text-muted d-block mt-2" style="font-size: 11px; line-height: 1.4;">
                                *Password bawaan akan otomatis dikirim oleh sistem via WhatsApp setelah diverifikasi oleh Admin.
                            </span>
                            @error('no_hp')
                                <span class="text-danger d-block mt-1 small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- TOMBOL SUBMIT KIRIM PENGJUAN --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm" style="border-radius: 12px; background-color: #007bff;">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan Aktivasi
                            </button>
                        </div>

                        {{-- Tombol Kembali yang membersihkan sesi/login tersangkut --}}
                        <div class="text-center mt-3">
                            @auth
                                <a href="{{ route('logout') }}" class="text-decoration-none text-muted small fw-bold"
                                   onclick="event.preventDefault(); document.getElementById('bypass-logout-form').submit();">
                                    <i class="fas fa-arrow-left me-1"></i> Keluar & Kembali ke Login
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Login
                                </a>
                            @endauth
                        </div>
                    </form>

                    {{-- Form tersembunyi pengeksekusi logout darurat --}}
                    <form id="bypass-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection