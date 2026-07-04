<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Inventaris PNUP</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        
        /* --- KOP SURAT STRUKTUR TABEL --- */
        .tabel-kop {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            margin-bottom: 5px;
        }
        .tabel-kop td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        .logo-wadah {
            width: 110px; /* PERBAIKAN: Diperlebar untuk memberikan ruang bagi logo yang lebih besar */
            text-align: left;
        }
        .logo-wadah img {
            width: 95px; /* PERBAIKAN: Ukuran logo PNUP diperbesar dari semula 70px */
            height: auto;
        }
        .teks-kop {
            text-align: center;
            /* PERBAIKAN: Padding kanan disamakan dengan lebar wadah logo baru agar teks lurus di tengah */
            padding-right: 110px !important; 
        }
        .teks-kop h3 {
            font-size: 14px;
            margin: 0 0 4px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .teks-kop h4 {
            font-size: 13px;
            margin: 0 0 3px 0;
            font-weight: normal;
            letter-spacing: 0.3px;
        }

        /* --- GARIS DOUBLE PEMBATAS KOP --- */
        .garis-pembatus {
            border-bottom: 4px double #000;
            margin-bottom: 15px;
            margin-top: 5px;
            width: 100%;
        }
        
        /* --- AREA JUDUL DI BAWAH GARIS --- */
        .judul-container {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .nama-kampus {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 8px 0;
            letter-spacing: 0.8px;
        }
        .nama-laporan {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .periode {
            font-size: 11px;
            color: #444;
            font-style: italic;
        }

        /* --- STYLING TABEL DATA LAPORAN --- */
        .tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .tabel-data th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #000;
            font-size: 10.5px;
            text-transform: uppercase;
        }
        .tabel-data td {
            padding: 7px 6px;
            border: 1px solid #000;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        
        /* --- TANDA TANGAN KANAN BAWAH --- */
        .ttd-wadah {
            float: right;
            width: 230px;
            text-align: center;
            margin-top: 20px;
        }
        .ttd-wadah .tgl-cetak {
            font-size: 10.5px;
            margin-bottom: 50px;
        }
        .ttd-wadah .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <table class="tabel-kop">
        <tr>
            <td class="logo-wadah">
                <img src="{{ public_path('img/logo-pnup.png') }}" alt="Logo PNUP">
            </td>
            <td class="teks-kop">
                <h3>BIDANG PERENCANAAN, KEUANGAN, DAN UMUM</h3>
                <h4>SUBBAGIAN UMUM</h4>
                <h3 style="margin-top: 2px;">RUMAH TANGGA DAN PERLENGKAPAN</h3>
            </td>
        </tr>
    </table>

    <div class="garis-pembatus"></div>

    <div class="judul-container">
        <div class="nama-kampus">POLITEKNIK NEGERI UJUNG PANDANG</div>
        <div class="nama-laporan">LAPORAN PEMINJAMAN INVENTARIS</div>
        <div class="periode">
            Periode: 
            @if(isset($date_range['start']) && isset($date_range['end']) && $date_range['start'] && $date_range['end'])
                {{ strpos($date_range['start'], '/') !== false ? \Carbon\Carbon::createFromFormat('d/m/Y', $date_range['start'])->translatedFormat('d M Y') : \Carbon\Carbon::parse($date_range['start'])->translatedFormat('d M Y') }} 
                s/d 
                {{ strpos($date_range['end'], '/') !== false ? \Carbon\Carbon::createFromFormat('d/m/Y', $date_range['end'])->translatedFormat('d M Y') : \Carbon\Carbon::parse($date_range['end'])->translatedFormat('d M Y') }}
            @else
                Semua Periode
            @endif
        </div>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Peminjam</th>
                <th style="width: 40%;">Aset (Barang/Kendaraan/Ruangan)</th>
                <th style="width: 12%;">Tgl Pinjam</th>
                <th style="width: 12%;">Tgl Kembali</th>
                <th style="width: 11%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>
                    @if($item->barang) 
                        [Barang] {{ $item->barang->nama_barang }}
                    @elseif($item->kendaraan) 
                        [Kendaraan] {{ $item->kendaraan->nama_kendaraan }}
                    @elseif($item->ruangan) 
                        [Ruangan] {{ $item->ruangan->nama_ruangan }}
                    @else 
                        - 
                    @endif
                </td>
                <td class="text-center">
                    {{ $item->tgl_pinjam ? ($item->tgl_pinjam instanceof \Carbon\Carbon ? $item->tgl_pinjam->format('d/m/Y') : \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y')) : '-' }}
                </td>
                <td class="text-center">
                    {{ $item->tgl_kembali ? ($item->tgl_kembali instanceof \Carbon\Carbon ? $item->tgl_kembali->format('d/m/Y') : \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y')) : '-' }}
                </td>
                <td class="text-center">{{ ucfirst($item->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #666; font-style: italic;">Tidak ada data peminjaman aset pada rentang periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-wadah">
        <div class="tgl-cetak">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
        <div class="nama-pejabat">Admin Rumah Tangga</div>
        <div>NIP. ........................................</div>
    </div>

</body>
</html>