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
    // B. KODE UNTUK HALAMAN CREATE (Form Input Peminjaman Baru)
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

        // 1. PEMBATASAN TANGGAL KEMBALI DINAMIS (BERDASARKAN MAX HARI BARANG)
        function updateLimitTanggal() {
            var todayStr = getTodayString();
            var tglPinjamInput = $('#tgl_pinjam');
            var tglKembaliInput = $('#tgl_kembali');
            var kategoriVal = $('#pilih-kategori').val();

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

                    // Cari max_hari terkecil dari seluruh barang yang dipilih
                    var minMaxHari = 7; // Default 7 Hari (1 Minggu)

                    if (kategoriVal === 'barang') {
                        $('.select-barang').each(function() {
                            var selectedOption = $(this).find(':selected');
                            var maxHariOption = parseInt(selectedOption.data('max-hari'));
                            
                            if (!isNaN(maxHariOption) && maxHariOption < minMaxHari) {
                                minMaxHari = maxHariOption;
                            }
                        });
                    }

                    // Update teks label judul form secara dinamis
                    if (minMaxHari < 7) {
                        $('#label-tgl-kembali').text('Rencana Kembali (Maks. ' + minMaxHari + ' Hari)');
                    } else {
                        $('#label-tgl-kembali').text('Rencana Kembali (Maks. 1 Minggu)');
                    }

                    // Hitung tanggal maksimal kembali
                    var maxKembali = new Date(tglPinjam);
                    maxKembali.setDate(maxKembali.getDate() + minMaxHari);
                    var maxKembaliStr = maxKembali.toISOString().split('T')[0];

                    tglKembaliInput.attr('min', minKembaliStr);
                    tglKembaliInput.attr('max', maxKembaliStr);

                    // Reset nilai jika tanggal yang sudah terisi melebihi batas baru
                    var tglKembaliVal = tglKembaliInput.val();
                    if (tglKembaliVal && (tglKembaliVal < minKembaliStr || tglKembaliVal > maxKembaliStr)) {
                        tglKembaliInput.val(maxKembaliStr);
                    }
                }
            }
        }

        // Jalankan saat pertama kali dimuat
        updateLimitTanggal();

        // Validasi event saat tanggal pinjam atau kategori diubah
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
                var currentLabel = $('#label-tgl-kembali').text();
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Peminjaman Terlampaui',
                    text: 'Peminjaman untuk barang pilihan Anda ' + currentLabel.toLowerCase() + '.',
                    confirmButtonColor: '#3085d6'
                });
                $(this).val(maxAllowed);
            } else if (selectedVal && minAllowed && selectedVal < minAllowed) {
                $(this).val(minAllowed);
            }
        });

        // 2. LOGIKA DETEKSI TAMPILAN INPUT SURAT IZIN & KATEGORI FORM
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
            updateLimitTanggal();
        });

        // 3. LOGIKA BARANG DINAMIS & CEK STOK
        // Logika tombol Tambah Baris Dinamis
        $('#addRow').click(function() {
            var newRow = `<tr>
                <td>
                    <select name="barang_id[]" class="form-control select-barang">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->id }}" data-stok="{{ $b->jumlah_stok }}" data-max-hari="{{ $b->max_hari ?? 7 }}">{{ $b->nama_barang }} (Stok: {{ $b->jumlah_stok }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="jumlah[]" class="form-control input-jumlah" value="1" min="1"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            $('#tableBarang tbody').append(newRow);
            updateLimitTanggal();
        });

        // Hapus baris dinamis
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateLimitTanggal();
        });

        // Mengubah nilai max atribut stok secara dinamis saat dropdown barang dipilih
        $(document).on('change', '.select-barang', function() {
            var stok = $(this).find(':selected').data('stok');
            var inputJumlah = $(this).closest('tr').find('.input-jumlah');
            
            if (stok !== undefined && stok !== '') {
                inputJumlah.attr('max', stok);
                
                if (parseInt(inputJumlah.val()) > parseInt(stok)) {
                    inputJumlah.val(stok);
                }
            } else {
                inputJumlah.removeAttr('max');
            }

            // Perbarui batas tanggal kembali sesuai max_hari barang yang baru dipilih
            updateLimitTanggal();
        });

        // Validasi saat user mengetik atau mengubah isi input jumlah secara manual
        $(document).on('input change', '.input-jumlah', function() {
            var maxStok = $(this).attr('max');
            var valueInput = $(this).val();

            if (maxStok && parseInt(valueInput) > parseInt(maxStok)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Mencukupi',
                    text: 'Anda tidak dapat meminjam melebihi sisa stok yang tersedia (' + maxStok + ' Unit).',
                    confirmButtonColor: '#3085d6'
                });
                $(this).val(maxStok);
            }
        });

        // Trigger perubahan awal saat form dimuat
        $('.select-barang').trigger('change');
    }
});