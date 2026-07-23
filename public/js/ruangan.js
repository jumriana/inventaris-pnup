// public/js/ruangan.js

$(document).ready(function () {
    // =========================================================================
    // 1. KODE UNTUK HALAMAN INDEX (Daftar Ruangan - Konfirmasi Hapus Admin)
    // =========================================================================
    // Menangani klik pada tombol dengan class .btn-konfirmasi-hapus
    $(document).on('click', '.btn-konfirmasi-hapus', function (e) {
        e.preventDefault();
        
        var button = $(this);
        var form = button.closest('form');
        var namaRuangan = button.data('nama') || 'ruangan ini';

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data "' + namaRuangan + '" beserta riwayat peminjamannya akan dihapus secara permanen dari sistem!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Eksekusi submit form hapus
            }
        });
    });

    // Menangani submit langsung jika ada form dengan class .form-hapus atau .form-hapus-kustom
    $(document).on('submit', '.form-hapus, .form-hapus-kustom', function (e) {
        // Mencegah submit ganda jika event sudah ditangani oleh tombol di atas
        if ($(this).data('confirmed')) {
            return true;
        }
    });

    // =========================================================================
    // 2. KODE UNTUK HALAMAN CREATE / EDIT (Form Input File)
    // =========================================================================
    if ($('.custom-file-input').length > 0) {
        $(document).on('change', '.custom-file-input', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    }
});

// =========================================================================
// 3. FUNGSI GLOBAL (Dipanggil saat klik tombol "Ajukan Peminjaman" di Blade)
// =========================================================================
function cekSuratIzin(urlTujuan) {
    Swal.fire({
        title: 'Konfirmasi Surat Izin',
        text: 'Apakah Anda sudah memiliki surat izin resmi untuk penggunaan ruangan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Sudah Ada',
        cancelButtonText: 'Belum Ada',
        allowOutsideClick: false
    }).then((result) => {
        // JIKA MAHASISWA/DOSEN SUDAH MENYIAPKAN SURAT IZIN
        if (result.isConfirmed) {
            window.location.href = urlTujuan;
        } 
        // JIKA MAHASISWA/DOSEN BELUM MEMILIKI SURAT IZIN (TAMPILKAN ALUR BIROKRASI UPDATE BARU)
        else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: '<span class="font-weight-bold text-dark d-block mt-2">Informasi Persuratan Ruangan</span>',
                html: `
                    <p class="text-muted small mb-4" style="text-align: left; font-size: 14px; line-height: 1.6;">Sesuai prosedur operasional Divisi Rumah Tangga PNUP, peminjaman fasilitas ruangan/aula diwajibkan melampirkan berkas surat izin peminjaman resmi.</p>
                    
                    <div class="text-left bg-white p-2" style="font-size: 14px; line-height: 1.6; text-align: left; border-top: 1px solid #eee;">
                        <h6 class="text-warning font-weight-bold mb-3"><i class="fas fa-file-invoice mr-1"></i> Alur Pengurusan Surat Izin Ruangan PNUP:</h6>
                        
                        <div class="mb-3 pl-1" style="border-left: 3px solid #007bff; padding-left: 10px !important;">
                            <span class="text-primary font-weight-bold d-block mb-1"><i class="fas fa-graduation-cap mr-1"></i> KHUSUS MAHASISWA:</span>
                            <ol class="pl-3 text-secondary mb-0" style="line-height: 1.5; padding-left: 20px;">
                                <li>Wajib menyiapkan <strong>Surat Pengantar</strong> dari Himpunan Mahasiswa (HIMA) atau Organisasi Kampus terkait mengenai tujuan penggunaan.</li>
                                <li>Membawa surat pengantar tersebut ke <strong>Wakil Direktur II (Wadir 2)</strong> untuk proses pengesahan/persetujuan resmi.</li>
                                <li>Setelah surat izin resmi disahkan, silakan kembali ke website ini dan <strong>unggah (upload) berkas tersebut ke sistem</strong> agar langsung dicek oleh admin unit Rumah Tangga.</li>
                            </ol>
                        </div>

                        <div class="pl-1" style="border-left: 3px solid #28a745; padding-left: 10px !important;">
                            <span class="text-success font-weight-bold d-block mb-1"><i class="fas fa-user-tie mr-1"></i> STAF & DOSEN:</span>
                            <ol class="pl-3 text-secondary mb-0" style="line-height: 1.5; padding-left: 20px;">
                                <li>Dapat langsung membuat <strong>Surat Pengajuan</strong> keperluan penggunaan fasilitas yang ditujukan ke Divisi Rumah Tangga.</li>
                                <li>Surat tersebut bisa dibawa langsung secara fisik ke unit Rumah Tangga <strong>ATAU langsung diunggah (upload)</strong> ke dalam sistem website ini tanpa melalui Wakil Direktur.</li>
                            </ol>
                        </div>
                    </div>
                    <p class="text-muted text-center mt-3 mb-0" style="font-size: 12px; border-top: 1px dashed #ddd; padding-top: 8px;"><i class="fas fa-info-circle"></i> Setelah berkas surat izin resmi ber-nomor telah siap, silakan kembali lagi ke sistem ini untuk melanjutkan proses peminjaman.</p>
                `,
                icon: 'info',
                iconColor: '#007bff',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#007bff',
                width: '550px'
            });
        }
    });
}