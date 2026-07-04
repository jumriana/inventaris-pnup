@extends('adminlte::page') 

@section('title', 'Data Peminjaman')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold text-dark"><i class="fas fa-handshake mr-2 text-success"></i> Daftar Transaksi Peminjaman</h1>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Buat Pinjaman Baru
        </a>
    </div>
@stop

@section('content')

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow-sm" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" style="width: 50px">No</th>
                        <th>Peminjam & WA</th>
                        <th>Aset / Barang</th>
                        <th>Waktu Pinjam</th>
                        <th class="text-center">Status</th>
                        @if(auth()->user()->role == 'admin')
                            <th class="text-center" style="width: 180px">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $key => $p)
                    <tr>
                        <td class="text-center align-middle">{{ $key + 1 }}</td>
                        <td class="align-middle">
                            <span class="font-weight-bold d-block">{{ $p->user->name ?? 'Civitas PNUP (User Terhapus)' }}</span>
                            @if($p->nomor_wa)
                                @php
                                    $nomorBersih = preg_replace('/[^0-9]/', '', $p->nomor_wa);
                                    if (str_starts_with($nomorBersih, '0')) {
                                        $nomorBersih = '62' . substr($nomorBersih, 1);
                                    }
                                @endphp
                                <a href="https://api.whatsapp.com/send?phone={{ $nomorBersih }}" target="_blank" class="badge badge-success shadow-sm">
                                    <i class="fab fa-whatsapp mr-1"></i> {{ $p->nomor_wa }}
                                </a>
                            @else
                                <small class="text-muted font-italic">No WA available</small>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($p->barang_id)
                                <span class="text-primary font-weight-bold"><i class="fas fa-box mr-1"></i> {{ $p->barang->nama_barang ?? 'Barang Terhapus' }}</span>
                                <br>
                                <small class="badge badge-info"><i class="fas fa-cubes mr-1"></i> {{ $p->jumlah_item }} Unit</small>
                            @elseif($p->kendaraan_id)
                                <span class="text-warning font-weight-bold"><i class="fas fa-car mr-1"></i> {{ $p->kendaraan->nama_kendaraan ?? 'Kendaraan Terhapus' }}</span>
                                <br>
                                <small class="text-muted font-weight-bold">{{ $p->kendaraan->plat_nomor ?? '-' }}</small>
                            @elseif($p->ruangan_id)
                                <span class="text-indigo font-weight-bold" style="color: #6610f2;"><i class="fas fa-door-open mr-1"></i> {{ $p->ruangan->nama_ruangan ?? 'Ruangan Terhapus' }}</span>
                                <br>
                                <small class="text-muted">Aula/Ruang Rapat</small>
                            @endif

                            @if($p->surat_izin)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $p->surat_izin) }}" target="_blank" class="btn btn-xs btn-outline-danger rounded-pill shadow-sm px-2">
                                        <i class="fas fa-eye mr-1"></i> Lihat Surat Izin
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle">
                            <div class="d-flex flex-column">
                                <span class="badge badge-light border text-left mb-1">
                                    <i class="fas fa-sign-out-alt text-danger mr-1"></i> {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}
                                </span>
                                <span class="badge badge-light border text-left">
                                    <i class="fas fa-sign-in-alt text-success mr-1"></i> {{ \Carbon\Carbon::parse($p->tgl_kembali)->format('d M Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            @php $status = strtolower($p->status); @endphp
                            @if($status == 'pending')
                                <span class="badge badge-warning px-3 py-2 shadow-sm"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                            @elseif($status == 'disetujui')
                                <span class="badge badge-primary px-3 py-2 shadow-sm"><i class="fas fa-hand-holding mr-1"></i> Dipinjam</span>
                            @elseif($status == 'dikembalikan')
                                <span class="badge badge-success px-3 py-2 shadow-sm"><i class="fas fa-check-circle mr-1"></i> Selesai</span>
                            @elseif($status == 'ditolak')
                                <span class="badge badge-danger px-3 py-2 shadow-sm"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                            @endif
                        </td>
                        
                        @if(auth()->user()->role == 'admin')
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                @if(strtolower($p->status) == 'pending')
                                    <form action="{{ route('peminjaman.setujui', $p->id) }}" method="POST" class="d-inline form-setujui">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success mr-1 shadow-sm" title="Setujui">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('peminjaman.tolak', $p->id) }}" method="POST" class="d-inline form-tolak">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(strtolower($p->status) == 'disetujui')
                                    <form action="{{ route('peminjaman.kembalikan', $p->id) }}" method="POST" class="form-kembalikan">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-undo mr-1"></i> Kembalikan
                                        </button>
                                    </form>
                                @endif

                                @if(strtolower($p->status) == 'dikembalikan' || strtolower($p->status) == 'ditolak')
                                    <form action="{{ route('peminjaman.destroy', $p->id) }}" method="POST" class="form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role == 'admin' ? 6 : 5 }}" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                            <p class="text-secondary font-weight-bold">Belum ada transaksi peminjaman.</p>
                            <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-outline-success">Mulai Pinjam Sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

{{-- Hubungkan File CSS Eksternal Modul Peminjaman --}}
@section('css')
<link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
@stop

{{-- Hubungkan File JS Eksternal Modul Peminjaman --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/peminjaman.js') }}"></script>
@stop