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

        // Fungsi mendapatkan string tanggal hari ini (YYYY-MM-DD)
        function getTodayString() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            return yyyy + '-' + mm + '-' + dd;
        }

        // 1. PEMBATASAN TANGGAL MAKSIMAL 7 HARI & KUNCI TANGGAL LALU
        function updateLimitTanggal() {
            var todayStr = getTodayString();
            var tglPinjamInput = $('#tgl_pinjam');
            var tglKembaliInput = $('#tgl_kembali');

            if (tglPinjamInput.length && tglKembaliInput.length) {
                // Kunci tanggal pinjam agar tidak bisa memilih tanggal yang sudah lewat
                tglPinjamInput.attr('min', todayStr);

                if (!tglPinjamInput.val() || tglPinjamInput.val() < todayStr) {
                    tglPinjamInput.val(todayStr);
                }

                var tglPinjamVal = tglPinjamInput.val();
                if (tglPinjamVal) {
                    var tglPinjam = new Date(tglPinjamVal);
                    var minKembaliStr = tglPinjamVal;

                    // Maksimal 7 hari dari tanggal pinjam
                    var maxKembali = new Date(tglPinjam);
                    maxKembali.setDate(maxKembali.getDate() + 7);
                    var maxKembaliStr = maxKembali.toISOString().split('T')[0];

                    tglKembaliInput.attr('min', minKembaliStr);
                    tglKembaliInput.attr('max', maxKembaliStr);

                    var tglKembaliVal = tglKembaliInput.val();
                    if (tglKembaliVal && (tglKembaliVal < minKembaliStr || tglKembaliVal > maxKembaliStr)) {
                        tglKembaliInput.val(maxKembaliStr);
                    }
                }
            }
        }

        // Jalankan saat awal dimuat
        updateLimitTanggal();

        // Validasi event saat tanggal pinjam diubah
        $('#tgl_pinjam').on('change', function() {
            var todayStr = getTodayString();
            if ($(this).val() < todayStr) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal pinjam tidak boleh memilih tanggal yang sudah lewat.',
                    confirmButtonColor: '#3085d6'
                });
                $(this).val(todayStr);
            }
            updateLimitTanggal();
        });

        // Validasi event saat tanggal kembali diubah
        $('#tgl_kembali').on('change', function() {
            var maxAllowed = $(this).attr('max');
            var minAllowed = $(this).attr('min');
            var selectedVal = $(this).val();

            if (selectedVal && maxAllowed && selectedVal > maxAllowed) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Peminjaman Terlampaui',
                    text: 'Peminjaman maksimal hanya diperbolehkan selama 1 minggu (7 hari) dari tanggal pinjam.',
                    confirmButtonColor: '#3085d6'
                });
                $(this).val(maxAllowed);
            } else if (selectedVal && minAllowed && selectedVal < minAllowed) {
                $(this).val(minAllowed);
            }
        });

        // 2. LOGIKA DETEKSI TAMPILAN INPUT SURAT IZIN
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

        // 3. HAPUS BARIS DINAMIS TABEL BARANG
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    }
});