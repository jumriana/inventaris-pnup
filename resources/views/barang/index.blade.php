@extends('adminlte::page')

@section('title', 'Inventaris Barang')

@section('content_header')
<div class="row align-items-center mb-3">
    {{-- Sisi Kiri: Hanya Judul Utama --}}
    <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
        <h1 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-boxes mr-2 text-primary"></i> Inventaris Barang PNUP
        </h1>
    </div>
    
    {{-- Sisi Kanan Atas: Tombol Tambah Barang Baru (Sejajar ke Kanan) --}}
    <div class="col-md-6 col-sm-12 text-md-right text-left">
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm px-3" style="border-radius: 8px;">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Barang Baru
            </a>
        @endif
    </div>
</div>

{{-- Baris Baru di Bawahnya: Khusus untuk Filter, Pencarian, dan Reset --}}
<div class="d-flex justify-content-end align-items-center flex-wrap">
    <form action="{{ route('barang.index') }}" method="GET" class="d-flex align-items-center flex-wrap">
        {{-- Filter Dropdown Kategori --}}
        <div class="mr-2 mb-2 mb-md-0">
            <select name="kategori" class="form-control shadow-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">📦 Semua Kategori</option>
                <option value="pertukangan" {{ request('kategori') == 'pertukangan' ? 'selected' : '' }}>Alat Pertukangan & Perbaikan</option>
                <option value="elektronik" {{ request('kategori') == 'elektronik' ? 'selected' : '' }}>Elektronik & Multimedia</option>
                <option value="fasilitas" {{ request('kategori') == 'fasilitas' ? 'selected' : '' }}>Fasilitas Kelas & Kantor</option>
                <option value="kebersihan" {{ request('kategori') == 'kebersihan' ? 'selected' : '' }}>Alat Kebersihan & Perawatan</option>
                <option value="komunikasi" {{ request('kategori') == 'komunikasi' ? 'selected' : '' }}>Perangkat Komunikasi</option>
            </select>
        </div>

        {{-- Input Pencarian Cepat --}}
        <div class="input-group shadow-sm mr-2 mb-2 mb-md-0" style="width: 250px;">
            <input type="text" name="search" class="form-control" placeholder="Cari merek atau nama..." 
                   value="{{ request('search') }}" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        {{-- Tombol Reset Dinamis --}}
        @if(request()->filled('kategori') || request()->filled('search'))
            <div class="mb-2 mb-md-0">
                <a href="{{ route('barang.index') }}" class="btn btn-secondary shadow-sm" style="border-radius: 8px;">
                    Reset
                </a>
            </div>
        @endif
    </form>
</div>
@stop

@section('content')
<div class="row">
    @forelse($barangs as $b)
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 15px; transition: all 0.3s ease;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge badge-info px-2 py-1 shadow-sm">
                        <i class="fas fa-tag mr-1"></i> {{ $b->kode_inventaris }}
                    </span>
                    
                    {{-- Badge Status Kondisi dengan Warna Dinamis --}}
                    @php
                        $badgeColor = 'badge-success'; // Default: Baik
                        if($b->kondisi == 'Rusak Ringan') $badgeColor = 'badge-warning text-white';
                        if($b->kondisi == 'Rusak') $badgeColor = 'badge-danger';
                    @endphp
                    <span class="badge {{ $badgeColor }} px-2 py-1">
                        {{ $b->kondisi }}
                    </span>
                </div>

                {{-- Visual Box --}}
                <div class="text-center py-3 bg-light rounded mb-3">
                    <i class="fas fa-box fa-4x text-secondary opacity-50"></i>
                </div>

                {{-- Nama Barang menampilkan MERK BMN --}}
                <h4 class="font-weight-bold text-dark text-center mb-1 text-capitalize">{{ $b->nama_barang }}</h4>
                
                {{-- Keterangan Barang menampilkan teks inputan Keterangan --}}
                <p class="text-muted small text-center mb-3">
                    <i class="fas fa-info-circle text-info mr-1"></i> {{ Str::limit($b->ruangan_id, 40) }}
                </p>

                {{-- Tampilan statistik satu kolom penuh tanpa kolom NUP --}}
                <div class="text-center mb-3 py-2 bg-light rounded mx-0">
                    <small class="d-block text-muted">Jumlah</small>
                    <span class="font-weight-bold {{ $b->jumlah_stok > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                        {{ $b->jumlah_stok }} Unit
                    </span>
                </div>

                {{-- LOGIKA TOMBOL BERDASARKAN ROLE DAN KONDISI --}}
                <div class="mt-3 border-top pt-3">
                    @if(Auth::user()->role == 'admin')
                        {{-- TAMPILAN ADMIN --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="form-hapus">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger p-0"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                            
                            <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-info px-3 rounded-pill shadow-sm">
                                <i class="fas fa-edit mr-1"></i> Edit Data
                            </a>
                        </div>

                        {{-- FITUR TAMBAHAN: Tombol Pinjam Barang untuk Sisi Admin --}}
                        @if($b->kondisi == 'Rusak')
                            <button class="btn btn-secondary btn-block rounded-pill disabled" disabled>
                                <i class="fas fa-exclamation-triangle mr-1"></i> Rusak (Tidak Bisa Pinjam)
                            </button>
                        @elseif($b->jumlah_stok > 0)
                            <a href="{{ route('peminjaman.create', ['item_id' => $b->id, 'kategori' => 'barang']) }}" 
                               class="btn btn-success btn-block rounded-pill shadow-sm py-2">
                                <i class="fas fa-hand-holding mr-1"></i> Pinjam Barang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-block rounded-pill disabled" disabled>
                                <i class="fas fa-ban mr-1"></i> Stok Habis
                            </button>
                        @endif

                    @else
                        {{-- TAMPILAN USER --}}
                        @if($b->kondisi == 'Rusak')
                            <button class="btn btn-secondary btn-block rounded-pill disabled" disabled>
                                <i class="fas fa-exclamation-triangle mr-1"></i> Rusak (Tidak Bisa Pinjam)
                            </button>
                        @elseif($b->jumlah_stok > 0)
                            <a href="{{ route('peminjaman.create', ['item_id' => $b->id, 'kategori' => 'barang']) }}" 
                               class="btn btn-success btn-block rounded-pill shadow-sm py-2">
                                <i class="fas fa-hand-holding mr-1"></i> Pinjam Barang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-block rounded-pill disabled" disabled>
                                <i class="fas fa-ban mr-1"></i> Stok Habis
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-box-open fa-5x text-light mb-3"></i>
        <h4 class="text-secondary">Belum ada barang di daftar inventaris.</h4>
    </div>
    @endforelse
</div>
@stop

{{-- Menghubungkan File CSS Modular Barang --}}
@section('css')
<link class="stylesheet" href="{{ asset('css/barang.css') }}">
@stop

{{-- Menghubungkan File JS Modular Barang --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/barang.js') }}"></script>

{{-- Trigger SweetAlert jika ada session success --}}
@if(session('success'))
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Oke'
    });
</script>
@endif
@stop