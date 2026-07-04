@extends('adminlte::page')

@section('title', 'Daftar Kendaraan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="font-weight-bold text-dark"><i class="fas fa-car-side mr-2 text-primary"></i> Manajemen Kendaraan PNUP</h1>
        
        {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('kendaraan.create') }}" class="btn btn-warning font-weight-bold shadow-sm mb-2">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Kendaraan
            </a>
        @endif
    </div>
@stop

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-md-end justify-content-start flex-wrap align-items-center">
        
        <div class="mb-2 mb-md-0 mr-md-2" style="width: 180px;">
            <select name="jenis_kendaraan" class="form-control shadow-sm style-select-kustom" onchange="filterAsetKendaraan()" id="filterJenis" style="border-radius: 10px; height: calc(2.25rem + 6px);">
                <option value="">🚗 Semua Jenis</option>
                <option value="Mobil" {{ request('jenis_kendaraan') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                <option value="Motor" {{ request('jenis_kendaraan') == 'Motor' ? 'selected' : '' }}>Motor</option>
                {{-- VALUE DIUBAH MENJADI "Bus" AGAR COCOK DENGAN DATA DI DATABASE --}}
                <option value="Bus" {{ request('jenis_kendaraan') == 'Bus' ? 'selected' : '' }}>Bus / Elf</option>
                <option value="Mobil Tangki" {{ request('jenis_kendaraan') == 'Mobil Tangki' ? 'selected' : '' }}>Mobil Tangki</option>
                <option value="Gerobak Tarik" {{ request('jenis_kendaraan') == 'Gerobak Tarik' ? 'selected' : '' }}>Gerobak Tarik</option>
            </select>
        </div>

        <div class="mb-2 mb-md-0" style="width: 280px; max-width: 100%;">
            <form action="{{ request()->url() }}" method="GET" id="formSearchKendaraan" class="d-flex w-100">
                {{-- Mengunci filter jenis yang sedang aktif saat mencari kata kunci --}}
                @if(request('jenis_kendaraan')) 
                    <input type="hidden" name="jenis_kendaraan" value="{{ request('jenis_kendaraan') }}"> 
                @endif
                
                <div class="input-group shadow-sm border-0">
                    <input type="text" name="search" class="form-control form-search-kustom" 
                           placeholder="Cari merek atau plat nomor..." 
                           value="{{ request('search') }}"
                           style="border-radius: 10px 0 0 10px; height: calc(2.25rem + 6px);">
                    <div class="input-group-append">
                        <button class="btn btn-primary px-3" type="submit" style="border-radius: 0 10px 10px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ request()->url() . (request('jenis_kendaraan') ? '?jenis_kendaraan='.request('jenis_kendaraan') : '') }}" 
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
    @forelse($kendaraans as $k)
    <div class="col-md-4 mb-4">
        <div class="card card-outline card-warning shadow-sm h-100 card-ruangan-kustom" style="border-radius: 15px;">
            <div class="card-body box-profile d-flex flex-column">
                
                {{-- Logika Ikon Otomatis --}}
                <div class="py-4 bg-light mb-3 text-center" style="border-radius: 10px;">
                    @if($k->jenis_kendaraan == 'Mobil')
                        <i class="fas fa-car fa-5x text-primary"></i>
                    @elseif($k->jenis_kendaraan == 'Motor')
                        <i class="fas fa-motorcycle fa-5x text-success"></i>
                    @elseif($k->jenis_kendaraan == 'Mobil Tangki')
                        <i class="fas fa-truck fa-5x text-danger"></i> 
                    @elseif($k->jenis_kendaraan == 'Gerobak Tarik')
                        <i class="fas fa-trailer fa-5x text-info"></i>
                    @else
                        <i class="fas fa-bus fa-5x text-warning"></i>
                    @endif
                </div>

                <h3 class="profile-username text-center font-weight-bold mb-0 text-capitalize">{{ $k->nama_kendaraan }}</h3>
                <p class="text-muted text-center mb-2 font-weight-bold">{{ $k->plat_nomor }}</p>

                {{-- KOLOM KETERANGAN TAMBAHAN --}}
                <div class="text-center mb-3 px-2">
                    <p class="small text-secondary mb-0" style="font-style: italic;">
                        <i class="fas fa-info-circle mr-1"></i> 
                        {{ $k->keterangan ? Str::limit($k->keterangan, 60) : 'Tidak ada keterangan tambahan' }}
                    </p>
                </div>

                {{-- Grid Info Kondisi, Status, dan Surat Izin --}}
                <div class="row text-center mb-3 py-2 bg-light rounded mx-0">
                    <div class="col-4 border-right px-1">
                        <small class="d-block text-muted">Kondisi</small>
                        <span class="font-weight-bold small text-dark">{{ $k->kondisi }}</span>
                    </div>
                    <div class="col-4 border-right px-1">
                        <small class="d-block text-muted">Surat Izin</small>
                        @if($k->jenis_kendaraan == 'Motor')
                            <span class="text-muted font-weight-bold small"><i class="fas fa-times-circle mr-1"></i> Tidak</span>
                        @else
                            <span class="text-warning font-weight-bold small"><i class="fas fa-file-contract mr-1"></i> Wajib</span>
                        @endif
                    </div>
                    <div class="col-4 px-1">
                        <small class="d-block text-muted">Status</small>
                        @if($k->kondisi == 'Servis' || $k->kondisi == 'Rusak Berat')
                            <span class="badge badge-secondary px-2 py-0">Mogok</span>
                        @elseif($k->status == 'Tersedia')
                            <span class="badge badge-success px-2 py-0">Tersedia</span>
                        @else
                            <span class="badge badge-danger px-2 py-0">Dipinjam</span>
                        @endif
                    </div>
                </div>

                {{-- Estimasi Batas Waktu Pemakaian Kendaraan --}}
                @if($k->status == 'Dipinjam' && $k->kondisi != 'Servis' && $k->kondisi != 'Rusak Berat')
                    <div class="mt-1 mb-3 p-2 rounded text-danger text-center border border-danger-soft animate-pulse-container" style="font-size: 0.85rem; background-color: #fff5f5;">
                        <i class="fas fa-clock mr-1 animate-pulse"></i> 
                        <strong>Dipinjam s.d:</strong> <br>
                        @if($k->peminjamanAktif && $k->peminjamanAktif->tgl_kembali)
                            {{ \Carbon\Carbon::parse($k->peminjamanAktif->tgl_kembali)->translatedFormat('d M Y') }}
                        @else
                            <span class="text-muted font-italic">Sedang Berlangsung</span>
                        @endif
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                    {{-- AKSI ADMIN: HAPUS MENGGUNAKAN INTERCEPTOR SWEETALERT2 --}}
                    @if(Auth::user()->role == 'admin')
                        <form action="{{ route('kendaraan.destroy', $k->id) }}" method="POST" class="form-hapus-kustom m-0">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-link text-danger p-0 text-decoration-none btn-konfirmasi-hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    @endif

                    <div class="ml-auto">
                        {{-- AKSI ADMIN: EDIT --}}
                        @if(Auth::user()->role == 'admin')
                            <a href="{{ route('kendaraan.edit', $k->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill mr-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif

                        {{-- PERBAIKAN LOGIKA PINJAM --}}
                        @if($k->status == 'Tersedia' && ($k->kondisi == 'Baik' || $k->kondisi == 'Rusak Ringan'))
                            <button type="button" 
                                    onclick="cekSuratIzinKendaraan('{{ route('peminjaman.create', ['item_id' => $k->id, 'kategori' => 'kendaraan']) }}')"
                                    class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold btn-pinjam-kustom">
                                <i class="fas fa-key mr-1"></i> Pinjam
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" disabled>
                                @if($k->status == 'Dipinjam' && ($k->kondisi == 'Baik' || $k->kondisi == 'Rusak Ringan'))
                                    <i class="fas fa-ban mr-1"></i> Dipinjam
                                @else
                                    <i class="fas fa-tools mr-1"></i> Perbaikan
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="card shadow-none border-0 bg-transparent py-4">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-secondary font-weight-bold">Data tidak ditemukan</h4>
            <p class="text-muted mx-auto" style="max-width: 400px;">
                Aset kendaraan dinas operasional dengan filter atau kata kunci yang Anda cari tidak terdaftar dalam sistem Rumah Tangga PNUP.
            </p>
            <div class="mt-2">
                <a href="{{ route('kendaraan.index') }}" class="btn btn-sm btn-primary shadow-sm rounded-pill px-4">
                    <i class="fas fa-sync-alt mr-1"></i> Reset Pencarian
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@stop

@section('css')
<link class="css-kustom" rel="stylesheet" href="{{ asset('css/kendaraan.css') }}">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kendaraan.js') }}?v={{ time() }}"></script>

<script>
    function filterAsetKendaraan() {
        let jenis = document.getElementById('filterJenis').value;
        let currentUrl = new URL(window.location.href);
        
        if (jenis) {
            currentUrl.searchParams.set('jenis_kendaraan', jenis);
        } else {
            currentUrl.searchParams.delete('jenis_kendaraan');
        }
        
        // Bersihkan filter status bawaan agar tidak bentrok
        currentUrl.searchParams.delete('status');
        
        window.location.href = currentUrl.toString();
    }
</script>

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