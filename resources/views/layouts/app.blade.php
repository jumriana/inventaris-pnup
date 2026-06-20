<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventaris PNUP') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5 !important;
            color: #333;
        }
        .navbar {
            background-color: #ffffff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        }
        .form-control {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
        }
        .btn-primary {
            background-color: #007bff !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 10px 20px !important;
            font-weight: 600 !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo-pnup.png') }}" alt="Logo PNUP" style="height: 40px; width: auto;">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('peminjaman*') ? 'active fw-bold' : '' }}" href="{{ route('peminjaman.index') }}">Peminjaman</a>
                            </li>
                            @if(Auth::user()->role == 'admin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="masterData" role="button" data-bs-toggle="dropdown">Master Data</a>
                                    <ul class="dropdown-menu border-0 shadow-sm">
                                        <li><a class="dropdown-item" href="{{ route('ruangan.index') }}">Ruangan</a></li>
                                        <li><a class="dropdown-item" href="{{ route('kendaraan.index') }}">Kendaraan</a></li>
                                        <li><a class="dropdown-item" href="{{ route('barang.index') }}">Inventaris</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('report*') ? 'active fw-bold' : '' }}" href="{{ route('report.index') }}">Laporan</a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('login') }}">Login</a></li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">Profil Saya</a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-5">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // 1. INTERSEPTOR TOMBOL HAPUS GLOBAL (Berlaku untuk semua form dengan class 'form-hapus')
            $(document).on('submit', '.form-hapus', function (e) {
                e.preventDefault(); // Menahan form agar tidak langsung terhapus
                
                var form = this;

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data aset yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit form jika user memilih tombol "Ya, Hapus!"
                    }
                });
            });

            // 2. POPUP NOTIFIKASI BERHASIL DIHAPUS (Muncul otomatis jika ada session success)
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</body>
</html>