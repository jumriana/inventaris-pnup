@extends('adminlte::page') 

@section('title', 'Laporan Peminjaman')

@section('content_header')
    <h1>Laporan Peminjaman</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Data Peminjaman Barang & Inventaris</h6>
            <a href="{{ route('report.pdf', request()->all()) }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('report.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="small font-weight-bold">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="small font-weight-bold">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm px-3 mr-2 shadow-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('report.index') }}" class="btn btn-secondary btn-sm px-3 shadow-sm">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Peminjam</th>
                            <th>Aset (Barang/Kendaraan/Ruangan)</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $report->user->name ?? 'User terhapus' }}</td>
                            <td>
                                @if($report->barang)
                                    <small class="badge badge-info">Barang</small> {{ $report->barang->nama_barang }}
                                @elseif($report->kendaraan)
                                    <small class="badge badge-primary">Kendaraan</small> {{ $report->kendaraan->nama_kendaraan }}
                                @elseif($report->ruangan)
                                    <small class="badge badge-warning">Ruangan</small> {{ $report->ruangan->nama_ruangan }}
                                @else
                                    <span class="text-muted italic small">Aset tidak teridentifikasi</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $report->tgl_pinjam ? $report->tgl_pinjam->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">{{ $report->tgl_kembali ? $report->tgl_kembali->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                @php
                                    $badgeColor = 'bg-warning';
                                    if(in_array($report->status, ['disetujui', 'kembali', 'Selesai'])) $badgeColor = 'bg-success';
                                    if($report->status == 'ditolak') $badgeColor = 'bg-danger';
                                @endphp
                                <span class="badge {{ $badgeColor }} text-white shadow-sm">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center small py-3">Data tidak tersedia untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection