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

        // Filter berdasarkan tgl_pinjam
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        }

        $reports = $query->orderBy('tgl_pinjam', 'desc')->get();

        return view('admin.report.index', compact('reports'));
    }

    public function exportPDF(Request $request)
    {
        $query = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        }

        // Variabel harus bernama $data agar sinkron dengan template PDF HTML standar
        $data = $query->get(); 
        
        $date_range = [
            'start' => $request->start_date ?? '-',
            'end' => $request->end_date ?? '-'
        ];

        $pdf = Pdf::loadView('admin.report.pdf', compact('data', 'date_range'))
                  ->setPaper('a4', 'landscape'); 

        return $pdf->download('Laporan_Peminjaman_' . Carbon::now()->format('d-m-Y') . '.pdf');
    }
}