@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-5">

                    <!-- Logo dan Judul menyatu di dalam kotak putih -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 80px; width: auto;">
                        <h4 class="mt-3 font-weight-bold">Daftar Akun</h4>
                        <p class="text-muted small">Lengkapi data untuk pendaftaran</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama Anda">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin01@gmail.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="..........">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2" style="border-radius: 10px; background-color: #007bff;">
                                Daftar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection