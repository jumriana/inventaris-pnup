<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .header { text-align: center; line-height: 1.5; }
        hr { border: 0.5px solid #000; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN PEMINJAMAN BARANG & INVENTARIS</h2>
        <p style="margin:0;">Politeknik Negeri Ujung Pandang</p>
        <hr>
        <p>Periode: {{ $date_range['start'] }} s/d {{ $date_range['end'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Aset (Barang/Kendaraan/Ruangan)</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>
                    @if($item->barang) [Barang] {{ $item->barang->nama_barang }}
                    @elseif($item->kendaraan) [Kendaraan] {{ $item->kendaraan->nama_kendaraan }}
                    @elseif($item->ruangan) [Ruangan] {{ $item->ruangan->nama_ruangan }}
                    @else - @endif
                </td>
                <td class="text-center">{{ $item->tgl_pinjam ? $item->tgl_pinjam->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $item->tgl_kembali ? $item->tgl_kembali->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; padding-right: 20px;">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        <br><br><br>
        <p><strong>Admin Rumah Tangga</strong></p>
    </div>
</body>
</html>