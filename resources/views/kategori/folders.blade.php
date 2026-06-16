@extends('adminlte::page')

@section('title', 'Kategori Utama')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="font-weight-bold" style="color: #333;">Pilih Kategori Utama</h1>
    <a href="{{ route('kategori.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus"></i> Tambah Jenis Baru
    </a>
</div>
@stop

@section('content')
<div class="row">
    @php
        // Array untuk menentukan ikon dan warna setiap kategori
        $ikon_list = [
            'Elektronik' => ['icon' => 'fas fa-plug', 'color' => '#f8d7da'],
            'Furnitur' => ['icon' => 'fas fa-chair', 'color' => '#d1ecf1'],
            'Peralatan Kantor' => ['icon' => 'fas fa-print', 'color' => '#fff3cd'],
            'Peralatan IT / Jaringan' => ['icon' => 'fas fa-network-wired', 'color' => '#d4edda'],
            'Multimedia / Acara' => ['icon' => 'fas fa-camera-retro', 'color' => '#e2e3e5'],
            'Laboratorium' => ['icon' => 'fas fa-flask', 'color' => '#cce5ff'],
            'Kendaraan' => ['icon' => 'fas fa-car', 'color' => '#f8d7da'],
            'Peralatan Kebersihan' => ['icon' => 'fas fa-broom', 'color' => '#d1ecf1'],
            'Peralatan Olahraga' => ['icon' => 'fas fa-volleyball-ball', 'color' => '#fff3cd'],
        ];
    @endphp

    @foreach($daftar_induk as $induk)
    <div class="col-md-4 mb-4">
        <a href="{{ route('kategori.index', ['view' => $induk]) }}" class="text-decoration-none h-100">
            {{-- Kartu Putih dengan Border Tipis --}}
            <div class="card h-100 shadow-sm border" style="border-radius: 10px; transition: 0.3s; background: #fff;">
                <div class="card-body text-center py-5">
                    
                    {{-- Ikon Lingkaran Berwarna (Sesuai contoh gambar kamu) --}}
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-4" 
                         style="width: 80px; height: 80px; background-color: {{ $ikon_list[$induk]['color'] ?? '#eee' }}; border-radius: 50%;">
                        <i class="{{ $ikon_list[$induk]['icon'] ?? 'fas fa-folder' }} fa-2x" style="color: #333;"></i>
                    </div>

                    <h5 class="font-weight-bold text-uppercase mb-1" style="color: #333; letter-spacing: 1px;">
                        {{ $induk }}
                    </h5>
                    <p class="small text-muted text-uppercase mb-0" style="font-size: 0.7rem;">
                        Klik untuk melihat isi
                    </p>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- CSS Tambahan untuk Efek Hover --}}
<style>
    .card:hover {
        transform: translateY(-5px);
        border-color: #007bff !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@stop