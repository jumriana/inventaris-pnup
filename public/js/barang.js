/* public/js/barang.js */

$(document).ready(function () {
    // 1. POPUP KONFIRMASI SEBELUM HAPUS BARANG
    $(document).on('submit', '.form-hapus', function(e) {
        e.preventDefault();
        var form = this;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data barang inventaris ini akan dihapus secara permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});