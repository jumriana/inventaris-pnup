<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman; 
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Load semua relasi aset agar tidak error saat pemanggilan di Blade
        $query = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan']);

        // 1. LOGIKA UTAMA: Cek jika ada request filter tanggal manual atau dari JavaScript otomatisasi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        } 
        // Cadangan jika JavaScript di-bypass, backend tetap mendeteksi parameter periode
        elseif ($request->filled('periode')) {
            $this->applyPeriodeFilter($query, $request->periode);
        }

        $reports = $query->orderBy('tgl_pinjam', 'desc')->get();

        return view('admin.report.index', compact('reports'));
    }

    public function exportPDF(Request $request)
    {
        $query = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan']);

        // 2. LOGIKA UTAMA: Pastikan data cetak PDF sinkron dengan rentang tanggal yang dipilih di layar
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        } 
        elseif ($request->filled('periode')) {
            $this->applyPeriodeFilter($query, $request->periode);
        }

        // Variabel harus bernama $data agar sinkron dengan template PDF HTML standar Anda
        $data = $query->orderBy('tgl_pinjam', 'desc')->get(); 
        
        // Memformat rentang tanggal agar bisa tampil cantik di KOP Surat berkas PDF laporan
        $date_range = [
            'start' => $request->start_date ? Carbon::parse($request->start_date)->format('d/m/Y') : '-',
            'end' => $request->end_date ? Carbon::parse($request->end_date)->format('d/m/Y') : '-'
        ];

        $pdf = Pdf::loadView('admin.report.pdf', compact('data', 'date_range'))
                  ->setPaper('a4', 'landscape'); 

        return $pdf->download('Laporan_Peminjaman_' . Carbon::now()->format('d-m-Y') . '.pdf');
    }

    /**
     * Helper Function untuk menangani query filter periode cepat secara dinamis di server
     */
    private function applyPeriodeFilter($query, $periode)
    {
        switch ($periode) {
            case 'minggu_ini':
                $query->whereBetween('tgl_pinjam', [Carbon::now()->subDays(7)->toDateString(), Carbon::now()->toDateString()]);
                break;
            case 'bulan_ini':
                $query->whereMonth('tgl_pinjam', Carbon::now()->month)
                      ->whereYear('tgl_pinjam', Carbon::now()->year);
                break;
            case 'tahun_ini':
                $query->whereYear('tgl_pinjam', Carbon::now()->year);
                break;
        }
    }
}