@extends('adminlte::page')

@section('title', 'Inventaris Barang')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold text-dark"><i class="fas fa-boxes mr-2 text-primary"></i> Inventaris Barang PNUP</h1>
        
        {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
            </a>
        @endif
    </div>
@stop

@section('content')

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(Auth::user()->role == 'admin')
    {{-- TAMPILAN TABEL UNTUK ADMIN --}}
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px" class="text-center">No</th>
                            <th>Informasi Barang</th>
                            <th>Kelompok Kategori</th>
                            <th class="text-center">Stok</th>
                            <th>Lokasi</th>
                            <th class="text-center">Kondisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $b)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle">
                                <span class="font-weight-bold d-block text-primary">{{ $b->nama_barang }}</span>
                                <small class="text-muted"><i class="fas fa-tag mr-1"></i>{{ $b->kode_inventaris }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info shadow-sm px-2 py-1">
                                    <i class="fas fa-folder mr-1"></i> {{ $b->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="text-center align-middle font-weight-bold">
                                <span class="badge badge-pill {{ $b->jumlah_stok > 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $b->jumlah_stok }} Unit
                                </span>
                            </td>
                            <td class="align-middle small">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $b->ruangan_id }}
                            </td>
                            <td class="align-middle text-center">
                                @if($b->kondisi == 'Baik')
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Baik</span>
                                @elseif($b->kondisi == 'Rusak')
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rusak</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-tools"></i> {{ $b->kondisi }}</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-info shadow-sm mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-box-open fa-4x text-light mb-3"></i>
                                <h5 class="text-secondary">Belum ada barang di daftar inventaris.</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    {{-- TAMPILAN CARD UNTUK USER/PEMINJAM --}}
    <div class="row">
        @forelse($barangs as $b)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm border-0 inventory-card" style="border-radius: 15px;">
                <div class="card-body text-center d-flex flex-column">
                    <div class="mb-3">
                        <i class="fas fa-box fa-4x text-secondary mt-3"></i>
                    </div>
                    
                    <h5 class="font-weight-bold text-dark mb-1">{{ $b->nama_barang }}</h5>
                    <p class="text-muted small mb-3">Kode: {{ $b->kode_inventaris }}</p>
                    
                    <div class="bg-light rounded py-2 mb-3">
                        <small class="d-block text-muted">Stok Tersedia</small>
                        <span class="badge {{ $b->jumlah_stok > 0 ? 'badge-info' : 'badge-danger' }} px-3">
                            {{ $b->jumlah_stok }} Unit
                        </span>
                    </div>

                    <div class="mt-auto">
                        @if($b->jumlah_stok > 0)
                            <a href="{{ route('peminjaman.create', ['item_id' => $b->id, 'kategori' => 'barang']) }}" 
                               class="btn btn-success btn-block rounded-pill shadow-sm">
                                <i class="fas fa-hand-holding mr-1"></i> Pinjam Barang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-block rounded-pill disabled" disabled>
                                <i class="fas fa-ban mr-1"></i> Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-box-open fa-4x text-light mb-3"></i>
            <h4 class="text-secondary">Tidak ada barang yang tersedia saat ini.</h4>
        </div>
        @endforelse
    </div>
@endif

@stop

@section('css')
<style>
    /* Styling Tabel */
    .table thead th { border-top: none; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; color: #555; }
    .table-hover tbody tr:hover { background-color: rgba(0,123,255,.05); transition: 0.3s; }
    
    /* Styling Card Inventaris */
    .inventory-card { transition: all 0.3s ease; }
    .inventory-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; 
    }
    .badge-pill { font-weight: 500; min-width: 60px; }
    .rounded-pill { border-radius: 50px; }
</style>
@stop