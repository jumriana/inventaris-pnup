@extends('adminlte::page')

@section('title', 'Buat Peminjaman')

@section('content_header')
    <h1>Form Peminjaman Baru</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Input Detail Peminjaman</h3>
    </div>
    
    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <!-- BARIS 1: Informasi Dasar -->
            <div class="row">
                {{-- Nama Peminjam (Readonly) --}}
                <div class="col-md-4 form-group">
                    <label>Nama Peminjam</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>

                {{-- Tanggal Pinjam --}}
                <div class="col-md-4 form-group">
                    <label>Tanggal Pinjam</label>
                    <input type="date" 
                           name="tgl_pinjam" 
                           id="tgl_pinjam"
                           class="form-control @error('tgl_pinjam') is-invalid @enderror" 
                           value="{{ date('Y-m-d') }}" 
                           min="{{ date('Y-m-d') }}" 
                           required>
                </div>

                {{-- Rencana Kembali --}}
                <div class="col-md-4 form-group">
                    <label>Rencana Kembali</label>
                    <input type="date" 
                           name="tgl_kembali" 
                           id="tgl_kembali"
                           class="form-control @error('tgl_kembali') is-invalid @enderror" 
                           min="{{ date('Y-m-d') }}" 
                           required>
                </div>
            </div>

            <!-- BARIS 2: Kontak & Kategori -->
            <div class="row">
                {{-- Nomor WA --}}
                <div class="col-md-4 form-group">
                    <label>Nomor WA yang Bisa Dihubungi</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                        </div>
                        <input type="text" name="nomor_wa" class="form-control" placeholder="Contoh: 0821..." required>
                    </div>
                </div>

                {{-- Keperluan --}}
                <div class="col-md-4 form-group">
                    <label>Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="1" placeholder="Tujuan peminjaman..." required></textarea>
                </div>

                {{-- Pilih Kategori Aset --}}
                <div class="col-md-4 form-group">
                    <label>Pilih Kategori Aset</label>
                    <select name="kategori" id="pilih-kategori" class="form-control" required>
                        <option value="barang" {{ $kategori_pilihan == 'barang' ? 'selected' : '' }}>📦 Barang / Alat Inventaris</option>
                        <option value="kendaraan" {{ $kategori_pilihan == 'kendaraan' ? 'selected' : '' }}>🚛 Kendaraan Operasional</option>
                        <option value="ruangan" {{ $kategori_pilihan == 'ruangan' ? 'selected' : '' }}>🏢 Ruangan / Aula</option>
                    </select>
                </div>
            </div>

            <hr>

            {{-- SEKSI FORM RUANGAN --}}
            <div id="form-ruangan" class="form-kategori" style="{{ $kategori_pilihan == 'ruangan' ? '' : 'display:none;' }}">
                <h5><i class="fas fa-door-open mr-2"></i> Pilih Ruangan</h5>
                <div class="form-group">
                    <select name="ruangan_id" class="form-control">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($ruangans as $r)
                            <option value="{{ $r->id }}" {{ ($kategori_pilihan == 'ruangan' && $selected_item_id == $r->id) ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} Orang)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SEKSI FORM KENDARAAN --}}
            <div id="form-kendaraan" class="form-kategori" style="{{ $kategori_pilihan == 'kendaraan' ? '' : 'display:none;' }}">
                <h5><i class="fas fa-car mr-2"></i> Pilih Kendaraan</h5>
                <div class="form-group">
                    <select name="kendaraan_id" class="form-control">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($kendaraans as $k)
                            <option value="{{ $k->id }}" {{ ($kategori_pilihan == 'kendaraan' && $selected_item_id == $k->id) ? 'selected' : '' }}>
                                {{ $k->nama_kendaraan }} - {{ $k->plat_nomor }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SEKSI FORM BARANG (INVENTARIS) --}}
            <div id="form-barang" class="form-kategori" style="{{ ($kategori_pilihan == 'barang' || !$kategori_pilihan) ? '' : 'display:none;' }}">
                <h5><i class="fas fa-boxes mr-2"></i> Daftar Barang yang Dipinjam</h5>
                <table class="table table-bordered" id="tableBarang">
                    <thead>
                        <tr>
                            <th>Pilih Barang</th>
                            <th style="width: 150px;">Jumlah</th>
                            <th style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="barang_id[]" class="form-control">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}" 
                                            {{ ($kategori_pilihan == 'barang' && $selected_item_id == $b->id) ? 'selected' : '' }}>
                                            {{ $b->nama_barang }} (Stok: {{ $b->jumlah_stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="jumlah[]" class="form-control" value="1" min="1"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-sm mt-2" id="addRow"><i class="fas fa-plus"></i> Tambah Item Lain</button>
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('peminjaman.index') }}" class="btn btn-default">Batal</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane mr-1"></i> Proses Peminjaman</button>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // 1. Validasi Tanggal Dinamis
        $('#tgl_pinjam').on('change', function() {
            var selectedDate = $(this).val();
            // Set minimal tanggal kembali mengikuti tanggal pinjam yang dipilih
            $('#tgl_kembali').attr('min', selectedDate);
            
            // Jika tanggal kembali sudah terisi dan lebih kecil dari tanggal pinjam baru, reset value-nya
            if ($('#tgl_kembali').val() < selectedDate) {
                $('#tgl_kembali').val(selectedDate);
            }
        });

        // 2. Logika Toggle Form Berdasarkan Kategori
        $('#pilih-kategori').on('change', function() {
            var kategori = $(this).val();
            $('.form-kategori').hide();
            $('#form-' + kategori).show();
        });

        // 3. Tambah Baris Barang
        $('#addRow').click(function() {
            var newRow = `<tr>
                <td>
                    <select name="barang_id[]" class="form-control">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="jumlah[]" class="form-control" value="1" min="1"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            $('#tableBarang tbody').append(newRow);
        });

        // 4. Hapus Baris Barang
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@stop