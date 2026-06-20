@extends('adminlte::page')

@section('title', 'Inventaris Barang')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="font-weight-bold text-dark"><i class="fas fa-boxes mr-2 text-primary"></i> Inventaris Barang PNUP</h1>
    
    {{-- Tombol Tambah hanya muncul jika yang login adalah Admin --}}
    @if(Auth::user()->role == 'admin')
        <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Barang Baru
        </a>
    @endif
</div>
@stop

@section('content')

{{-- Notifikasi Sukses Bawaan (Akan Otomatis Ditangkap SweetAlert2 Juga) --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm d-none" role="alert">
        <i class="icon fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

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
                        {{-- TAMPILAN ADMIN: Menggunakan class="form-hapus" untuk SweetAlert2 --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="form-hapus">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger p-0"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                            
                            <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-info px-3 rounded-pill shadow-sm">
                                <i class="fas fa-edit mr-1"></i> Edit Data
                            </a>
                        </div>
                    @else
                        {{-- TAMPILAN USER: Filter Kondisi Rusak --}}
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

@section('css')
<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.1) !important;
    }
    .badge { font-size: 0.75rem; }
    .bg-light { background-color: #f8f9fa !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // 1. POPUP KONFIRMASI SEBELUM HAPUS BARANG
        $(document).on('submit', '.form-hapus', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data barang inventaris ini akan dihapus secara permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 2. POPUP NOTIFIKASI BERHASIL DIHAPUS / DISIMPAN
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
        @endif
    });
</script>
@stop