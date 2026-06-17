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
            {{-- FORM FILTER TRANSAKSI --}}
            <form action="{{ route('report.index') }}" method="GET" class="row g-3 mb-4" id="form-filter">
                
                {{-- 1. DROPDOWN PERIODE CEPAT --}}
                <div class="col-md-4">
                    <label for="pilih-periode" class="small font-weight-bold"><i class="fas fa-clock mr-1 text-secondary"></i> Pilih Periode Cepat</label>
                    <select name="periode" id="pilih-periode" class="form-control form-control-sm">
                        <option value="">-- Pilih Periode Cepat --</option>
                        <option value="minggu_ini" {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>📅 Minggu Ini (7 Hari Terakhir)</option>
                        <option value="bulan_ini" {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>📅 Bulan Ini</option>
                        <option value="tahun_ini" {{ request('periode') == 'tahun_ini' ? 'selected' : '' }}>📅 Tahun Ini</option>
                    </select>
                </div>

                {{-- Tanggal Mulai --}}
                <div class="col-md-4">
                    <label class="small font-weight-bold">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>

                {{-- Tanggal Selesai --}}
                <div class="col-md-4">
                    <label class="small font-weight-bold">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                {{-- Tombol Submit Form diletakkan di bawah kontrol filter agar layout tetap rapi --}}
                <div class="col-12 d-flex justify-content-end mt-3">
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

{{-- LOGIKA AUTOMATION JAVASCRIPT JQUERY --}}
@section('js')
<script>
    $(document).ready(function() {
        // Mendeteksi perubahan pada dropdown periode cepat
        $('#pilih-periode').on('change', function() {
            var pilihan = $(this).val();
            var hariIni = new Date();
            
            var yyyy = hariIni.getFullYear();
            var mm = String(hariIni.getMonth() + 1).padStart(2, '0');
            var dd = String(hariIni.getDate()).padStart(2, '0');
            
            var formatHariIni = yyyy + '-' + mm + '-' + dd;

            if (pilihan === 'minggu_ini') {
                // Tarik tanggal mundur ke 7 hari yang lalu
                var tujuhHariLalu = new Date();
                tujuhHariLalu.setDate(hariIni.getDate() - 7);
                
                var startMM = String(tujuhHariLalu.getMonth() + 1).padStart(2, '0');
                var startDD = String(tujuhHariLalu.getDate()).padStart(2, '0');
                
                $('#start_date').val(tujuhHariLalu.getFullYear() + '-' + startMM + '-' + startDD);
                $('#end_date').val(formatHariIni);

            } else if (pilihan === 'bulan_ini') {
                // Rentang dari tanggal 01 sampai akhir bulan berjalan saat ini
                var tanggalAkhirBulan = new Date(yyyy, hariIni.getMonth() + 1, 0).getDate();
                
                $('#start_date').val(yyyy + '-' + mm + '-01');
                $('#end_date').val(yyyy + '-' + mm + '-' + String(tanggalAkhirBulan).padStart(2, '0'));

            } else if (pilihan === 'tahun_ini') {
                // Rentang dari 01 Januari sampai 31 Desember tahun berjalan
                $('#start_date').val(yyyy + '-01-01');
                $('#end_date').val(yyyy + '-12-31');
            } else {
                // Jika kembali memilih opsi default, kosongkan kolom tanggal
                $('#start_date').val('');
                $('#end_date').val('');
            }
        });
    });
</script>
@stop