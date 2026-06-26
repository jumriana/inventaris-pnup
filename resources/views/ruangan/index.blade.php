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
    <div class="alert alert-success alert-dismissible fade show shadow-sm d-none" role="alert">
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
                    @if(strtolower($ruangan->status) == 'tersedia')
                        <span class="badge badge-success px-2 py-1">Tersedia</span>
                    @elseif(strtolower($ruangan->status) == 'dipakai')
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

                <p class="small text-secondary flex-grow-1 mb-2">
                    {{ $ruangan->keterangan ?? 'Tidak ada deskripsi tambahan.' }}
                </p>

                {{-- PENYEMPURNAAN LOGIKA: Melacak transaksi aktif berstatus disetujui --}}
                @if(strtolower($ruangan->status) == 'dipakai')
                    @php
                        // Mencari data transaksi terakhir dari ruangan ini yang berstatus disetujui
                        $transaksiAktif = $ruangan->peminjamans ? $ruangan->peminjamans->where('status', 'disetujui')->last() : null;
                    @endphp
                    <div class="mt-1 mb-3 p-2 bg-light rounded text-danger border border-danger-soft" style="font-size: 0.85rem; background-color: #fff5f5;">
                        <i class="fas fa-clock mr-1 animate-pulse"></i> 
                        <strong>Terpakai s.d:</strong> 
                        @if($transaksiAktif && $transaksiAktif->tgl_kembali)
                            {{ \Carbon\Carbon::parse($transaksiAktif->tgl_kembali)->translatedFormat('d M Y') }}
                        @else
                            <span class="text-muted font-italic">Sedang Berlangsung</span>
                        @endif
                    </div>
                @endif

                <hr class="my-3">

                {{-- AKSES AKSI UNTUK ADMIN --}}
                @if(Auth::user()->role == 'admin')
                    <div class="d-flex justify-content-between align-items-center">
                        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" class="form-hapus">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger p-0"><i class="fas fa-trash mr-1"></i> Hapus</button>
                        </form>
                        
                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Data
                        </a>
                    </div>
                @endif

                {{-- TOMBOL AJUKAN PEMINJAMAN --}}
                @if(strtolower($ruangan->status) == 'tersedia')
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
        // 1. PEMBERITAHUAN KONFIRMASI SEBELUM HAPUS RUANGAN
        $(document).on('submit', '.form-hapus', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ruangan ini akan dihapus secara permanen dari sistem!",
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

        // 2. PEMBERITAHUAN SETELAH BERHASIL DIHAPUS / DITAMBAH
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

    // 3. LOGIKA POPUP INFORMASI PENGAJUAN RUANGAN
    function cekSuratIzin(urlTujuan) {
        Swal.fire({
            title: 'Konfirmasi Surat Izin',
            text: 'Apakah Anda sudah memiliki surat izin resmi untuk penggunaan ruangan ini?',
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
                    title: 'Informasi Persuratan Ruangan',
                    html: `<div style="text-align: left; font-size: 14px; line-height: 1.6;">
                            <p>Sesuai prosedur operasional Divisi Rumah Tangga PNUP, peminjaman fasilitas ruangan/aula diwajibkan melampirkan berkas surat izin peminjaman resmi.</p>
                            
                            <b class="text-warning"><i class="fas fa-file-alt"></i> Alur Pengurusan Surat Izin Ruangan PNUP:</b>
                            
                            <div class="mt-2" style="border-left: 3px solid #ffc107; padding-left: 10px; margin-bottom: 12px;">
                                <strong class="text-primary"><i class="fas fa-user-graduate"></i> KHUSUS MAHASISWA:</strong>
                                <ol style="margin-top: 5px; padding-left: 20px; margin-bottom: 5px;">
                                    <li>Membuat surat izin penggunaan fasilitas ruangan yang ditujukan kepada <b>Wakil Direktur III (Wadir 3)</b>.</li>
                                    <li>Membawa berkas surat tersebut untuk disahkan atau ditandatangani oleh <b>Wakil Direktur II (Wadir 2)</b>.</li>
                                </ol>
                            </div>

                            <div style="border-left: 3px solid #28a745; padding-left: 10px;">
                                <strong class="text-success"><i class="fas fa-user-tie"></i> STAF & DOSEN:</strong>
                                <ol style="margin-top: 5px; padding-left: 20px; margin-bottom: 5px;">
                                    <li>Dapat langsung bersurat resmi mengajukan permohonan ke <b>Divisi Rumah Tangga PNUP</b> tanpa melalui Wakil Direktur.</li>
                                </ol>
                            </div>
                            
                            <p class="mt-3 small text-muted" style="border-top: 1px dashed #ddd; padding-top: 8px;"><i class="fas fa-info-circle"></i> Setelah surat resmi ber-nomor diterbitkan, silakan kembali lagi ke sistem ini untuk melanjutkan proses peminjaman.</p>
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