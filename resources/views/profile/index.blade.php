@extends('adminlte::page')

@section('title', 'Pengaturan Profil')

@section('content_header')
    <h1><i class="fas fa-user-cog mr-2"></i> Pengaturan Profil</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- Tambahkan justify-content-center di sini --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title">Perbarui Informasi Akun</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Alamat Email</label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   placeholder="Masukkan alamat email" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">
                        
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info"></i> Keamanan Akun</h5>
                            <p>Kosongkan kolom password di bawah ini jika Anda tidak ingin mengubah password lama Anda.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password Baru</label>
                                    <input type="password" name="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           placeholder="Minimal 8 karakter">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent p-0 pt-3">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop