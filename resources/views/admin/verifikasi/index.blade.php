@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            {{-- Alert Notifikasi Berhasil --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Alert Notifikasi Gagal --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-secondary">
                        <i class="fas fa-user-check text-primary me-2"></i> Antrean Verifikasi Aktivasi Akun Civitas
                    </h5>
                    <span class="badge bg-primary rounded-pill">{{ count($users) }} Antrean</span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">No</th>
                                    <th>NIM / NIP Civitas</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. WhatsApp</th>
                                    <th>Status Antrean</th>
                                    <th class="text-center" style="width: 250px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge bg-secondary px-2 py-1.5 font-monospace">{{ $user->identity_number }}</span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $user->name }}</td>
                                        <td>
                                            <a href="https://wa.me/{{ $user->no_hp }}" target="_blank" class="text-decoration-none text-success fw-medium">
                                                <i class="fab fa-whatsapp me-1"></i> +{{ $user->no_hp }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Tombol Setujui --}}
                                                <form action="{{ route('admin.verifikasi.approve', $user->id) }}" method="POST" id="form-approve-{{ $user->id }}" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm" onclick="konfirmasiSetuju('{{ $user->id }}', '{{ $user->name }}')">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                </form>

                                                {{-- Tombol Tolak --}}
                                                <form action="{{ route('admin.verifikasi.tolak', $user->id) }}" method="POST" id="form-tolak-{{ $user->id }}" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-pill shadow-sm" onclick="konfirmasiTolak('{{ $user->id }}', '{{ $user->name }}')">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-secondary">
                                            <i class="fas fa-user-shield fa-3x mb-3 text-muted"></i>
                                            <p class="mb-0 fw-medium">Bersih! Tidak ada antrean pengajuan aktivasi akun baru saat ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT SWEETALERT2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function konfirmasiSetuju(id, nama) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Menyetujui akun " + nama + " dan mengirim password bawaan via WhatsApp.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754', // Warna hijau Bootstrap
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'px-4 rounded-pill',
                cancelButton: 'px-4 rounded-pill'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-approve-' + id).submit();
            }
        });
    }

    function konfirmasiTolak(id, nama) {
        Swal.fire({
            title: 'Tolak Pengajuan?',
            text: "Apakah Anda yakin ingin menolak aktivasi akun " + nama + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Warna merah Bootstrap
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'px-4 rounded-pill',
                cancelButton: 'px-4 rounded-pill'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-tolak-' + id).submit();
            }
        });
    }
</script>
@endsection