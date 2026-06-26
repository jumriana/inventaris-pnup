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
    <div class="alert alert-success alert-dismissible fade show d-none" role="alert">
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

                {{-- UPDATE BARU: Grid Info Kondisi, Status, dan Surat Izin --}}
                <div class="row text-center mb-3 py-2 bg-light rounded mx-0">
                    <div class="col-4 border-right px-1">
                        <small class="d-block text-muted">Kondisi</small>
                        <span class="font-weight-bold small text-dark">{{ $k->kondisi }}</span>
                    </div>
                    <div class="col-4 border-right px-1">
                        <small class="d-block text-muted">Surat Izin</small>
                        {{-- Logika: Jika Motor tidak wajib surat, selain itu wajib --}}
                        @if($k->jenis_kendaraan == 'Motor')
                            <span class="text-muted font-weight-bold small"><i class="fas fa-times-circle mr-1"></i> Tidak</span>
                        @else
                            <span class="text-warning font-weight-bold small"><i class="fas fa-file-contract mr-1"></i> Wajib</span>
                        @endif
                    </div>
                    <div class="col-4 px-1">
                        <small class="d-block text-muted">Status</small>
                        @if($k->status == 'Tersedia')
                            <span class="badge badge-success px-2 py-0">Tersedia</span>
                        @else
                            <span class="badge badge-danger px-2 py-0">Dipinjam</span>
                        @endif
                    </div>
                </div>

                {{-- PENAMBAHAN: Estimasi Batas Waktu Pemakaian Kendaraan --}}
                @if($k->status == 'Dipinjam')
                    <div class="mt-1 mb-3 p-2 bg-light rounded text-danger text-center border border-danger-soft" style="font-size: 0.85rem; background-color: #fff5f5;">
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
                    {{-- AKSI ADMIN: HAPUS MODEREN MENGGUNAKAN INTERCEPTOR SWEETALERT2 --}}
                    @if(Auth::user()->role == 'admin')
                        <form action="{{ route('kendaraan.destroy', $k->id) }}" method="POST" class="form-hapus">
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

                        {{-- AKSI SEMUA USER: PINJAM --}}
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
    .text-secondary { line-height: 1.2; }
    .border-top { border-top: 1px solid #f4f4f4 !important; }
    
    /* Animasi berkedip pelan pada ikon jam */
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // 1. PEMBERITAHUAN KONFIRMASI SEBELUM HAPUS KENDARAAN
        $(document).on('submit', '.form-hapus', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data kendaraan ini akan dihapus secara permanen dari sistem!",
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

        // 2. PEMBERITAHUAN SETELAH BERHASIL DIHAPUS / DIUPDATE
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

    // 3. LOGIKA POPUP SURAT IZIN JALAN KENDARAAN (MENDUKUNG ALUR MAHASISWA & STAF/DOSEN)
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
                window.location.href = urlTujuan;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Informasi Persuratan Kendaraan',
                    html: `<div style="text-align: left; font-size: 14px; line-height: 1.6;">
                            <p>Sesuai prosedur operasional Divisi Rumah Tangga PNUP, peminjaman kendaraan operasional diwajibkan melampirkan berkas surat izin peminjaman resmi.</p>
                            
                            <b class="text-warning"><i class="fas fa-file-alt"></i> Alur Pengurusan Surat Izin Kendaraan PNUP:</b>
                            
                            <div class="mt-2" style="border-left: 3px solid #ffc107; padding-left: 10px; margin-bottom: 12px;">
                                <strong class="text-primary"><i class="fas fa-user-graduate"></i> KHUSUS MAHASISWA:</strong>
                                <ol style="margin-top: 5px; padding-left: 20px; margin-bottom: 5px;">
                                    <li>Membuat surat izin penggunaan kendaraan operasional kampus yang ditujukan kepada <b>Wakil Direktur III (Wadir 3)</b>.</li>
                                    <li>Membawa berkas surat tersebut untuk disahkan atau ditandatangani oleh <b>Wakil Direktur II (Wadir 2)</b>.</li>
                                </ol>
                            </div>

                            <div style="border-left: 3px solid #28a745; padding-left: 10px;">
                                <strong class="text-success"><i class="fas fa-user-tie"></i> STAF & DOSEN:</strong>
                                <ol style="margin-top: 5px; padding-left: 20px; margin-bottom: 5px;">
                                    <li>Dapat langsung bersurat resmi ke <b>Divisi Rumah Tangga PNUP</b> tanpa melalui Wakil Direktur.</li>
                                </ol>
                            </div>
                            
                            <p class="mt-3 small text-muted" style="border-top: 1px dashed #ddd; padding-top: 8px;"><i class="fas fa-info-circle"></i> Setelah surat resmi ber-nomor diterbitkan, silakan kembali lagi ke sistem ini untuk melanjutkan pengisian form dan mengunggah berkas PDF surat izin tersebut.</p>
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