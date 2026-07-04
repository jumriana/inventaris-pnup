/* public/js/peminjaman.js */

$(document).ready(function () {
    
    // =========================================================================
    // A. KODE UNTUK HALAMAN INDEX (Daftar Riwayat Transaksi)
    // =========================================================================
    if ($('.form-setujui').length > 0 || $('.form-tolak').length > 0 || $('.form-kembalikan').length > 0 || $('.form-hapus').length > 0) {
        
        // 1. POPUP AKSI PERSETUJUAN
        $(document).on('submit', '.form-setujui', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Setujui peminjaman ini?',
                text: "Stok atau status aset akan otomatis terpotong oleh sistem.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });

        // 2. POPUP AKSI PENOLAKAN
        $(document).on('submit', '.form-tolak', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Tolak peminjaman ini?',
                text: "Permohonan transaksi ini akan ditandai sebagai ditolak.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });

        // 3. POPUP PROSES PENGEMBALIAN ASET
        $(document).on('submit', '.form-kembalikan', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Proses pengembalian aset?',
                text: "Status barang atau fasilitas akan dikembalikan menjadi 'Tersedia'.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kembalikan!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });

        // 4. POPUP HAPUS PERMANEN RIWAYAT
        $(document).on('submit', '.form-hapus', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data histori peminjaman ini akan dihapus secara permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    }

    // =========================================================================
    // B. KODE UNTUK HALAMAN CREATE (Form Input Baru)
    // =========================================================================
    if ($('#pilih-kategori').length > 0) {

        // 1. Validasi Tanggal Dinamis
        $('#tgl_pinjam').on('change', function() {
            var selectedDate = $(this).val();
            $('#tgl_kembali').attr('min', selectedDate);
            if ($('#tgl_kembali').val() < selectedDate) {
                $('#tgl_kembali').val(selectedDate);
            }
        });

        // 2. Logika Deteksi Tampilan Input Surat Izin (Ruangan & Kendaraan)
        function handleSuratIzinVisibility(kategori) {
            if (kategori === 'ruangan' || kategori === 'kendaraan') {
                $('#container-surat-izin').slideDown();
                $('#surat_izin').attr('required', true);
            } else {
                $('#container-surat-izin').slideUp();
                $('#surat_izin').attr('required', false);
                $('#surat_izin').val('');
            }
        }

        // Cek kondisi saat awal halaman dimuat
        var kategoriAwal = $('#pilih-kategori').val();
        handleSuratIzinVisibility(kategoriAwal);

        // Aksi ketika dropdown kategori diganti oleh user
        $('#pilih-kategori').on('change', function() {
            var kategori = $(this).val();
            $('.form-kategori').hide();
            $('#form-' + kategori).show();
            handleSuratIzinVisibility(kategori);
        });

        // 3. Hapus Baris Dinamis Tabel Barang
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    }
});