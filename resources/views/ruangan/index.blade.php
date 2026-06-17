@extends('adminlte::page')

@section('title', 'Daftar Ruangan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="font-weight-bold"><i class="fas fa-building mr-2 text-primary"></i> Manajemen Ruangan</h1>
    
    {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
    @if(Auth::user()->role == 'admin')
        <a href="{{ route('ruangan.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Ruangan Baru
        </a>
    @endif
</div>
@stop

@section('content')

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    @forelse($ruangans as $ruangan)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 15px; transition: all 0.3s ease;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-uppercase text-muted font-weight-bold">{{ $ruangan->kode_ruangan }}</small>
                    {{-- Badge Status --}}
                    @if($ruangan->status == 'Tersedia')
                        <span class="badge badge-success px-2 py-1">Tersedia</span>
                    @elseif($ruangan->status == 'Dipakai')
                        <span class="badge badge-danger px-2 py-1">Dipakai</span>
                    @else
                        <span class="badge badge-warning px-2 py-1 text-white">Perbaikan</span>
                    @endif
                </div>

                <h4 class="font-weight-bold text-dark">{{ $ruangan->nama_ruangan }}</h4>
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

                <p class="small text-secondary flex-grow-1">
                    {{ Str::limit($ruangan->keterangan ?? 'Tidak ada deskripsi tambahan.', 80) }}
                </p>

                <hr class="my-3">

                {{-- AKSES AKSI UNTUK ADMIN --}}
                @if(Auth::user()->role == 'admin')
                    <div class="d-flex justify-content-between align-items-center">
                        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger p-0"><i class="fas fa-trash mr-1"></i> Hapus</button>
                        </form>
                        
                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Data
                        </a>
                    </div>
                @endif

                {{-- TOMBOL AJUKAN PEMINJAMAN (Dimodifikasi menggunakan fungsi SweetAlert2) --}}
                @if($ruangan->status == 'Tersedia')
                    <div class="mt-3">
                        <button type="button" 
                                onclick="cekSuratIzin('{{ route('peminjaman.create', ['item_id' => $ruangan->id, 'kategori' => 'ruangan']) }}')"
                                class="btn btn-outline-primary btn-block rounded-pill shadow-sm">
                            <i class="fas fa-calendar-plus mr-1"></i> Ajukan Peminjaman
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="card shadow-none border-0 bg-transparent">
            <i class="fas fa-door-closed fa-5x text-light mb-3"></i>
            <h4 class="text-secondary">Belum ada data ruangan.</h4>
            <p class="text-muted">
                @if(Auth::user()->role == 'admin')
                    Silakan tambah ruangan melalui tombol di pojok kanan atas.
                @else
                    Maaf, saat ini belum ada ruangan yang terdaftar di sistem.
                @endif
            </p>
        </div>
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
    .badge {
        font-size: 0.75rem;
        font-weight: 600;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }
</style>
@stop

{{-- TAMBAHAN SECTION JAVASCRIPT SWEETALERT2 --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function cekSuratIzin(urlTujuan) {
    Swal.fire({
        title: 'Konfirmasi Surat Izin',
        text: 'Apakah Anda sudah memiliki surat izin untuk penggunaan ruangan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Sudah Ada',
        cancelButtonText: 'Belum Ada',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // JIKA USER MEMILIH YA: Alihkan ke halaman form peminjaman
            window.location.href = urlTujuan;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // JIKA USER MEMILIH BELUM ADA: Munculkan modal info alur administrasi
            Swal.fire({
                title: 'Informasi Pembuatan Surat Izin',
                html: `<div style="text-align: left; font-size: 14px; line-height: 1.6;">
                        <p>Sesuai dengan kriteria wajib Divisi Rumah Tangga PNUP, Anda <b>diwajibkan</b> mengurus surat izin fisik terlebih dahulu sebelum menggunakan fasilitas ruangan kampus.</p>
                        <b class="text-primary"><i class="fas fa-info-circle"></i> Alur Pembuatan Surat Izin Ruangan:</b>
                        <ol style="margin-top: 5px; padding-left: 20px;">
                            <li>Unduh atau mintalah draft format surat permohonan peminjaman ruangan resmi.</li>
                            <li>Ajukan tanda tangan/persetujuan resmi kepada Ketua Jurusan atau Kepala Unit terkait Anda.</li>
                            <li>Bawa surat cetak fisik tersebut ke bagian Administrasi / Divisi Rumah Tangga di Gedung Direktorat PNUP untuk divalidasi dan mendapatkan nomor surat resmi.</li>
                            <li>Setelah nomor surat resmi diterbitkan, silakan kembali lagi ke sistem ini untuk melanjutkan proses peminjaman.</li>
                        </ol>
                       </div>`,
                icon: 'info',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#3085d6'
            });
        }
    });
}
</script>
@stop