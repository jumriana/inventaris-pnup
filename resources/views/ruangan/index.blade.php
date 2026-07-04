@extends('adminlte::page')

@section('title', 'Daftar Ruangan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="font-weight-bold"><i class="fas fa-building mr-2 text-primary"></i> Manajemen Ruangan</h1>
    
    {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
    @if(Auth::user()->role == 'admin')
        <a href="{{ route('ruangan.create') }}" class="btn btn-primary shadow-sm mb-2">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Ruangan Baru
        </a>
    @endif
</div>
@stop

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-md-end justify-content-start flex-wrap align-items-center">
        
        <div class="mb-2 mb-md-0 mr-md-2" style="width: 180px;">
            <select name="status" class="form-control shadow-sm style-select-kustom" onchange="filterStatus(this.value)" style="border-radius: 10px; height: calc(2.25rem + 6px);">
                <option value="" {{ request('status') == '' ? 'selected' : '' }}>📋 Semua Status</option>
                <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>✅ Tersedia</option>
                <option value="Dipakai" {{ request('status') == 'Dipakai' ? 'selected' : '' }}>🚪 Dipakai</option>
                <option value="Perbaikan" {{ request('status') == 'Perbaikan' ? 'selected' : '' }}>🛠️ Perbaikan</option>
            </select>
        </div>

        <div class="mb-2 mb-md-0" style="width: 320px; max-width: 100%;">
            <form action="{{ request()->url() }}" method="GET" class="d-flex w-100">
                {{-- Mengunci filter status yang sedang aktif saat melakukan pencarian kata kunci --}}
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="input-group shadow-sm border-0">
                    <input type="text" name="search" class="form-control form-search-kustom" 
                           placeholder="Cari nama/lokasi..." 
                           value="{{ request('search') }}"
                           style="border-radius: 10px 0 0 10px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary px-3" type="submit" style="border-radius: 0 10px 10px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ request()->url() . (request('status') ? '?status='.request('status') : '') }}" 
                       class="btn btn-secondary ml-2 d-flex align-items-center justify-content-center shadow-sm btn-clear-kustom"
                       style="border-radius: 10px;" title="Reset Pencarian">
                       Reset
                    </a>
                @endif
            </form>
        </div>

    </div>
</div>

<div class="row">
    @forelse($ruangans as $ruangan)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0 bg-white card-ruangan-kustom">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-uppercase text-muted font-weight-bold">{{ $ruangan->kode_ruangan }}</small>
                    {{-- Badge Status --}}
                    @if(strtolower($ruangan->status) == 'tersedia')
                        <span class="badge badge-success px-2 py-1">Tersedia</span>
                    @elseif(strtolower($ruangan->status) == 'dipakai')
                        <span class="badge badge-danger px-2 py-1">Dipakai</span>
                    @else
                        <span class="badge badge-warning px-2 py-1 text-white">Perbaikan</span>
                    @endif
                </div>

                <h4 class="font-weight-bold text-dark mb-1">{{ $ruangan->nama_ruangan }}</h4>
                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt mr-1 text-danger"></i> {{ $ruangan->lokasi }}</p>

                <div class="row text-center mb-3 py-2 bg-light rounded mx-0">
                    <div class="col-6 border-right">
                        <small class="d-block text-muted">Kapasitas</small>
                        <span class="font-weight-bold"><i class="fas fa-users text-info mr-1"></i> {{ $ruangan->kapasitas }}</span>
                    </div>
                    <div class="col-6">
                        <small class="d-block text-muted">Surat Izin</small>
                        @if($ruangan->butuh_surat)
                            <span class="text-warning font-weight-bold"><i class="fas fa-file-contract mr-1"></i> Wajib</span>
                        @else
                            <span class="text-muted font-weight-bold"><i class="fas fa-times-circle mr-1"></i> Tidak</span>
                        @endif
                    </div>
                </div>

                <p class="small text-secondary flex-grow-1 mb-2">
                    {{ $ruangan->keterangan ?? 'Tidak ada deskripsi tambahan.' }}
                </p>

                {{-- TRANSAKSI AKTIF JIKA STATUSNYA DIPAKAI --}}
                @if(strtolower($ruangan->status) == 'dipakai')
                    @php
                        $transaksiAktif = $ruangan->peminjamans ? $ruangan->peminjamans->where('status', 'disetujui')->last() : null;
                    @endphp
                    <div class="mt-1 mb-3 p-2 rounded text-danger border border-danger-soft animate-pulse-container">
                        <i class="fas fa-clock mr-1 animate-pulse"></i> 
                        <strong>Terpakai s.d:</strong> 
                        @if($transaksiAktif && $transaksiAktif->tgl_kembali)
                            {{ \Carbon\Carbon::parse($transaksiAktif->tgl_kembali)->translatedFormat('d M Y') }}
                        @else
                            <span class="text-muted font-italic">Sedang Berlangsung</span>
                        @endif
                    </div>
                @endif

                <hr class="my-3 border-light">

                {{-- AKSES AKSI UNTUK ADMIN --}}
                @if(Auth::user()->role == 'admin')
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" class="form-hapus-kustom">
                            @csrf 
                            @method('DELETE')
                            <button type="button" class="btn btn-sm text-danger p-0 btn-konfirmasi-hapus"><i class="fas fa-trash mr-1"></i> Hapus</button>
                        </form>
                        
                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Data
                        </a>
                    </div>
                @endif

                {{-- TOMBOL AJUKAN PEMINJAMAN UNTUK USER JIKA TERSEDIA --}}
                @if(strtolower($ruangan->status) == 'tersedia')
                    <div class="mt-3">
                        <button type="button" 
                                onclick="cekSuratIzin('{{ route('peminjaman.create', ['item_id' => $ruangan->id, 'kategori' => 'ruangan']) }}')"
                                class="btn btn-outline-primary btn-block rounded-pill shadow-sm font-weight-bold btn-pinjam-kustom">
                            <i class="fas fa-calendar-plus mr-1"></i> Ajukan Peminjaman
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="card shadow-none border-0 bg-transparent py-4">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-secondary font-weight-bold">Data tidak ditemukan</h4>
            <p class="text-muted mx-auto" style="max-width: 400px;">
                Ruangan dengan kata kunci atau status yang Anda cari tidak ditemukan atau belum terdaftar di sistem.
            </p>
            <div class="mt-2">
                <a href="{{ route('ruangan.index') }}" class="btn btn-sm btn-primary shadow-sm rounded-pill px-4">
                    <i class="fas fa-sync-alt mr-1"></i> Reset Pencarian
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/ruangan.css') }}">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/ruangan.js') }}"></script>

<script>
    function filterStatus(statusVal) {
        let currentUrl = new URL(window.location.href);
        
        if (statusVal) {
            currentUrl.searchParams.set('status', statusVal);
        } else {
            currentUrl.searchParams.delete('status');
        }
        
        window.location.href = currentUrl.toString();
    }
</script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#007bff',
        confirmButtonText: 'Oke'
    });
</script>
@endif
@stop