@extends('adminlte::page')

@section('title', 'Daftar Kendaraan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold text-dark"><i class="fas fa-car-side mr-2"></i> Manajemen Kendaraan PNUP</h1>
        
        {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('kendaraan.create') }}" class="btn btn-warning shadow-sm">
                <i class="fas fa-plus-circle"></i> Tambah Kendaraan
            </a>
        @endif
    </div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon fas fa-check"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    @forelse($kendaraans as $k)
    <div class="col-md-4 mb-4">
        <div class="card card-outline card-warning shadow-sm h-100" style="border-radius: 15px;">
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

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item border-top-0">
                        <b>Kondisi</b> <a class="float-right text-dark">{{ $k->kondisi }}</a>
                    </li>
                    <li class="list-group-item border-bottom-0">
                        <b>Status</b> 
                        <a class="float-right">
                            @if($k->status == 'Tersedia')
                                <span class="badge badge-success px-3">Tersedia</span>
                            @else
                                <span class="badge badge-danger px-3">Dipinjam</span>
                            @endif
                        </a>
                    </li>
                </ul>

                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                    {{-- AKSI ADMIN: HAPUS --}}
                    @if(Auth::user()->role == 'admin')
                        <form action="{{ route('kendaraan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kendaraan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none">
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

                        {{-- AKSI SEMUA USER: PINJAM (DIMODIFIKASI MENGGUNAKAN POPUP SWEETALERT2) --}}
                        @if($k->status == 'Tersedia')
                            <button type="button" 
                                    onclick="cekSuratIzinKendaraan('{{ route('peminjaman.create', ['item_id' => $k->id, 'kategori' => 'kendaraan']) }}')"
                                    class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                <i class="fas fa-key mr-1"></i> Pinjam
                            </button>
                        @else
                            <button class="btn btn-secondary btn-sm rounded-pill px-3 disabled" disabled>
                                <i class="fas fa-lock mr-1"></i> Terpakai
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-car-crash fa-5x text-light mb-3"></i>
        <h4 class="text-secondary">Belum ada data kendaraan operasional.</h4>
    </div>
    @endforelse
</div>
@stop

@section('css')
<style>
    .card { transition: transform .2s; }
    .card:hover { transform: scale(1.02); }
    .profile-username { font-size: 1.25rem; color: #343a40; }
    .list-group-item { font-size: 0.9rem; }
    .text-secondary { line-height: 1.2; }
    .border-top { border-top: 1px solid #f4f4f4 !important; }
</style>
@stop

{{-- TAMBAHAN SECTION JAVASCRIPT SWEETALERT2 UNTUK KENDARAAN --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function cekSuratIzinKendaraan(urlTujuan) {
    Swal.fire({
        title: 'Konfirmasi Surat Izin Jalan',
        text: 'Apakah Anda sudah memiliki surat izin resmi untuk penggunaan kendaraan operasional ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Sudah Ada',
        cancelButtonText: 'Belum Ada',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // JIKA USER MEMILIH YA: Alihkan langsung ke form peminjaman baru
            window.location.href = urlTujuan;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // JIKA USER MEMILIH BELUM ADA: Munculkan modal info alur persuratan jalan
            Swal.fire({
                title: 'Informasi Persuratan Kendaraan',
                html: `<div style="text-align: left; font-size: 14px; line-height: 1.6;">
                        <p>Sesuai prosedur operasional Divisi Rumah Tangga PNUP, peminjaman kendaraan (bus/mobil dinas) <b>diwajibkan</b> melampirkan berkas surat izin peminjaman resmi.</p>
                        <b class="text-warning"><i class="fas fa-file-alt"></i> Alur Pengurusan Surat Izin Kendaraan:</b>
                        <ol style="margin-top: 5px; padding-left: 20px;">
                            <li>Buat surat permohonan peminjaman resmi yang ditandatangani oleh Ketua Lembaga/Organisasi atau Ketua Jurusan Anda.</li>
                            <li>Bawa surat cetak fisik tersebut ke <b>Sub Bagian Umum & BMN / Divisi Rumah Tangga di Gedung Direktorat PNUP</b> untuk divalidasi dan mendapatkan nomor surat jalan resmi.</li>
                            <li>Pastikan Anda juga sudah berkoordinasi dengan pihak driver/supir internal kampus mengenai kesiapan jadwal perjalanan.</li>
                            <li>Setelah surat resmi ber-nomor diterbitkan, silakan kembali lagi ke sistem ini untuk melanjutkan pengisian form dan mengunggah berkas PDF surat izin tersebut.</li>
                        </ol>
                       </div>`,
                icon: 'info',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#007bff'
            });
        }
    });
}
</script>
@stop