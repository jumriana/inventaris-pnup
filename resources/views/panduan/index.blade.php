@extends('adminlte::page')

@section('title', 'Panduan Penggunaan')

@section('content_header')
    <h1><i class="fas fa-question-circle mr-2 text-primary"></i> Panduan Penggunaan Sistem</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        {{-- BLOK PANDUAN UNTUK ADMIN --}}
        @if(Auth::user()->role == 'admin')
            <div class="card card-outline card-primary shadow-sm" style="border-radius: 12px;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-shield mr-2 text-primary"></i> Hak Akses: Administrator (Divisi Rumah Tangga)</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-info shadow-xs" style="border-radius: 8px;">
                        <h5>Selamat Datang di Panel Panduan Admin</h5>
                        <p>Sebagai Administrator Divisi Rumah Tangga PNUP, Anda memiliki kendali penuh terhadap pengelolaan aset logistik dan validasi sirkulasi peminjaman sarana prasarana kampus.</p>
                    </div>

                    <div class="timeline mt-4">
                        {{-- Langkah 1 --}}
                        <div>
                            <i class="fas fa-boxes bg-blue"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-primary">1. Manajemen Informasi Barang</h3>
                                <div class="timeline-body">
                                    Menu <strong>Informasi Barang</strong> digunakan untuk mendata seluruh aset BMN (Barang Milik Negara). Anda dapat menambah barang baru, memperbarui jumlah stok operasional, atau mengubah kondisi barang (Baik/Rusak). Pastikan nomor kode aset terinput secara unik dan valid untuk menghindari redudansi data.
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 2 --}}
                        <div>
                            <i class="fas fa-door-open bg-indigo" style="background-color: #6610f2 !important;"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-indigo" style="color: #6610f2;">2. Pengelolaan Fasilitas Ruangan & Kendaraan</h3>
                                <div class="timeline-body">
                                    Melalui menu <strong>Informasi Ruangan</strong> and <strong>Informasi Kendaraan</strong>, admin dapat mengontrol ketersediaan prasarana rapat/aula serta kendaraan operasional dinas PNUP. Anda berhak memperbarui detail kapasitas ruangan atau plat nomor kendaraan secara berkala sesuai kondisi nyata di lapangan.
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 3 --}}
                        <div>
                            <i class="fas fa-clipboard-check bg-green"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-success">3. Validasi, Persetujuan & Komunikasi Peminjaman</h3>
                                <div class="timeline-body">
                                    Setiap permohonan baru dari staf atau mahasiswa akan masuk ke menu <strong>Peminjaman</strong> dengan status <em>Menunggu</em>. Periksa berkas lampiran berkas surat izin PDF yang diunggah. Gunakan tombol **Setujui** (stok barang otomatis terpotong) atau tombol **Tolak**. Anda dapat menekan tombol pintas **WhatsApp** kontak civitas untuk melakukan koordinasi langsung via aplikasi WhatsApp Desktop laptop Anda.
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 4 --}}
                        <div>
                            <i class="fas fa-undo bg-info"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-info">4. Pencatatan Pengembalian Aset & Pembersihan Riwayat</h3>
                                <div class="timeline-body">
                                    Apabila peminjam telah selesai menggunakan fasilitas, klik opsi **Kembalikan** pada data transaksi terkait. Sistem akan mengembalikan jumlah kuantitas stok barang atau mengubah status ruangan/kendaraan menjadi <em>Tersedia</em> kembali. Riwayat transaksi yang telah berstatus selesai atau ditolak dapat dihapus permanen guna menjaga kebersihan pangkalan data sistem.
                                </div>
                            </div>
                        </div>

                        {{-- Garis Akhir Timeline --}}
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>

        {{-- BLOK PANDUAN UNTUK USER / MAHASISWA / STAF --}}
        @else
            <div class="card card-outline card-success shadow-sm" style="border-radius: 12px;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2 text-success"></i> Hak Akses: Pengguna / Peminjam (Civitas PNUP)</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-success shadow-xs" style="border-radius: 8px;">
                        <h5>Selamat Datang di Panduan Peminjam</h5>
                        <p>Ikuti langkah-langkah terstruktur di bawah ini untuk mengajukan permohonan peminjaman fasilitas logistik barang, ruang rapat/aula, atau kendaraan operasional di lingkungan Politeknik Negeri Ujung Pandang.</p>
                    </div>

                    <div class="timeline mt-4">
                        {{-- Langkah 1 --}}
                        <div>
                            <i class="fas fa-search bg-info"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-info">1. Cek Ketersediaan Logistik & Prasarana</h3>
                                <div class="timeline-body">
                                    Silakan jelajahi menu <strong>Informasi Barang</strong>, <strong>Informasi Ruangan</strong>, atau <strong>Informasi Kendaraan</strong> pada sidebar. Periksa daftar fasilitas untuk memastikan status unit berada dalam kondisi <em>Tersedia</em> atau memiliki sisa volume stok yang cukup sebelum Anda menekan tombol pengajuan.
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 2 --}}
                        <div>
                            <i class="fas fa-file-export bg-warning"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-warning" style="color: #ffc107 !important;">2. Pengisian Formulir Pengajuan & Unggah Surat Izin</h3>
                                <div class="timeline-body">
                                    Klik tombol **Buat Peminjaman Baru**, tentukan item yang ingin dipinjam, tentukan tanggal mulai pemakaian dan tanggal pengembalian secara akurat, serta isi deskripsi alasan peminjaman. Anda **diwajibkan mengunggah dokumen surat izin resmi** (hasil pindai/scan berformat PDF atau Gambar) sebagai berkas prasyarat validasi oleh divisi rumah tangga.
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 3 --}}
                        <div>
                            <i class="fas fa-clock bg-secondary"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-secondary">3. Pemantauan Status Riwayat Permohonan</h3>
                                <div class="timeline-body">
                                    Setelah formulir dikirim, pantau status sirkulasi peminjaman Anda secara berkala pada menu <strong>Peminjaman</strong>. Transaksi Anda akan melewati proses peninjauan oleh admin dengan indikator status berupa:
                                    <span class="badge badge-warning mx-1">Menunggu</span> (belum divalidasi),
                                    <span class="badge badge-primary mx-1">Dipinjam</span> (disetujui & hak pakai aktif), atau
                                    <span class="badge badge-danger mx-1">Ditolak</span> (permohonan ditolak admin).
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 4 --}}
                        <div>
                            <i class="fas fa-history bg-green"></i>
                            <div class="timeline-item shadow-xs">
                                <h3 class="timeline-header font-weight-bold text-success">4. Pengembalian Fasilitas Tepat Waktu</h3>
                                <div class="timeline-body">
                                    Peminjam wajib mengembalikan sarana prasarana logistik kepada pihak Divisi Rumah Tangga PNUP sesuai dengan batas tanggal kembali yang telah ditentukan di dalam sistem. Setelah diverifikasi secara fisik oleh petugas, status Anda akan berubah menjadi <span class="badge badge-success">Selesai</span> dan sirkulasi peminjaman dinyatakan tertutup.
                                </div>
                            </div>
                        </div>

                        {{-- Garis Akhir Timeline --}}
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection