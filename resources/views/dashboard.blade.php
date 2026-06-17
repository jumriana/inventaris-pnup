@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Ringkasan Informasi</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $totalInventaris }}</h3>
                    <p>Total Seluruh Inventaris</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $totalRiwayat }}</h3>
                    <p>{{ auth()->user()->role == 'admin' ? 'Total Riwayat Peminjaman' : 'Peminjaman Saya' }}</p>
                </div>
                <div class="icon"><i class="fas fa-hand-holding"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary shadow-sm">
                <div class="inner">
                    <h3>{{ $barangTersedia }}</h3>
                    <p>Barang Tersedia</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $barangDipinjam ?? 0 }}</h3>
                    <p>Barang Dipinjam</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-white shadow-sm">
                <span class="info-box-icon bg-info"><i class="fas fa-tools"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Barang Umum</span>
                    <span class="info-box-number">{{ $totalBarang }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-white shadow-sm">
                <span class="info-box-icon bg-warning text-white"><i class="fas fa-car"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kendaraan</span>
                    <span class="info-box-number">{{ $totalKendaraan }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-white shadow-sm">
                <span class="info-box-icon bg-success"><i class="fas fa-door-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ruangan</span>
                    <span class="info-box-number">{{ $totalRuangan }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mt-3 shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-1"></i> 
                {{ auth()->user()->role == 'admin' ? 'Aktivitas Peminjaman Terbaru (Seluruh User)' : 'Riwayat Peminjaman Saya' }}
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Peminjam</th>
                            <th>Aset / Barang</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifikasiPeminjaman as $notif)
                            <tr>
                                {{-- PERBAIKAN: Menggunakan null-safe fallback agar tidak crash jika data user kosong --}}
                                <td class="align-middle font-weight-bold">{{ $notif->user->name ?? 'Civitas PNUP (User Terhapus)' }}</td>
                                <td class="align-middle">
                                    @if($notif->barang_id)
                                        <i class="fas fa-box text-muted mr-1"></i> {{ $notif->barang->nama_barang ?? 'Barang' }}
                                    @elseif($notif->kendaraan_id)
                                        <i class="fas fa-car text-muted mr-1"></i> {{ $notif->kendaraan->nama_kendaraan ?? 'Kendaraan' }}
                                    @elseif($notif->ruangan_id)
                                        <i class="fas fa-door-open text-muted mr-1"></i> {{ $notif->ruangan->nama_ruangan ?? 'Ruangan' }}
                                    @else
                                        <span class="text-muted small italic">Aset Terhapus</span>
                                    @endif
                                </td>
                                <td class="align-middle small text-muted">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '-' }}</td>
                                <td class="align-middle">
                                    @php $status = strtolower($notif->status); @endphp
                                    @if($status == 'pending')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif($status == 'disetujui')
                                        <span class="badge badge-primary">Dipinjam</span>
                                    @elseif($status == 'dikembalikan')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($status == 'ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- PERBAIKAN: Kolom colspan diubah menjadi 4 agar sesuai dengan header tabel --}}
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> Belum ada aktivitas peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Footer Card --}}
        <div class="card-footer text-center bg-white">
            <a href="{{ route('peminjaman.index') }}" class="small-box-footer text-primary small">
                Lihat Semua Riwayat <i class="fas fa-arrow-circle-right ml-1"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box h3 { font-size: 2.2rem; }
        .info-box-number { font-size: 1.4rem; }
        .table thead th { border-top: 0; }
    </style>
@stop