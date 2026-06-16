@extends('adminlte::page')

@section('title', 'Isi Kategori: ' . ($kategori_aktif ?? 'Semua'))

@section('content_header')
<div class="d-flex justify-content-between">
    {{-- Menampilkan nama Folder Utama yang sedang dibuka --}}
    <h1>Kategori: <span class="text-primary">{{ $kategori_aktif ?? 'Semua' }}</span></h1>
    
    <div>
        {{-- Tombol kembali ke tampilan 9 folder --}}
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Folder Utama
        </a>
    </div>
</div>
@stop

@section('content')

{{-- Pemberitahuan jika berhasil menghapus data --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon fas fa-check"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
@forelse($kategoris as $item)
    <div class="col-md-3 mb-4">
        <div class="card card-outline card-info shadow-sm h-100">
            <div class="card-body box-profile d-flex flex-column">

                <div class="text-center">
                    {{-- Menampilkan Gambar Barang --}}
                    @if($item->gambar)
                        <img src="{{ asset('img/kategori/'.$item->gambar) }}"
                             class="img-fluid mb-3 rounded shadow-sm"
                             style="height:150px; width:100%; object-fit:cover; border: 1px solid #eee;">
                    @else
                        <div class="py-4 bg-light mb-3 rounded border">
                            <i class="fas fa-box fa-3x text-info"></i>
                            <p class="small text-muted mt-2">Tidak ada gambar</p>
                        </div>
                    @endif
                </div>

                {{-- NAMA BARANG / ITEM (Meja, Kursi, Jam, dll) --}}
                <h3 class="profile-username text-center font-weight-bold" style="font-size: 1.2rem; color: #333;">
                    {{ $item->nama_jenis }}
                </h3>

                {{-- KODE BARANG --}}
                <p class="text-muted text-center mb-2">
                    Kode: <span class="badge badge-dark px-2">{{ $item->kode_jenis }}</span>
                </p>

                {{-- DESKRIPSI SINGKAT BARANG --}}
                <div class="text-center flex-grow-1">
                    <p class="small text-secondary mb-0">
                        {{ $item->keterangan ?? 'Tidak ada deskripsi untuk barang ini.' }}
                    </p>
                </div>

                <hr class="my-3">

                {{-- TOMBOL HAPUS --}}
                <div class="d-flex justify-content-center mt-auto">
                    <form action="{{ route('kategori.destroy', $item->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ({{ $item->nama_jenis }}) ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                            <i class="fas fa-trash"></i> Hapus Barang
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@empty
    {{-- TAMPILAN JIKA FOLDER KOSONG --}}
    <div class="col-12">
        <div class="card py-5 text-center shadow-sm border-0">
            <div class="card-body">
                <i class="fas fa-folder-open fa-5x text-light mb-3" style="color: #dee2e6 !important;"></i>
                <h4 class="text-secondary">Folder <b>{{ $kategori_aktif }}</b> Masih Kosong</h4>
                <p class="text-muted">Belum ada barang yang didaftarkan dalam kategori ini.</p>
                <a href="{{ route('kategori.index') }}" class="btn btn-info btn-sm mt-2">
                    <i class="fas fa-arrow-left"></i> Pilih Kategori Lain
                </a>
            </div>
        </div>
    </div>
@endforelse
</div>
@stop