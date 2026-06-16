@extends('adminlte::page')

@section('title', 'Tambah Kategori')

@section('content_header')
    <h1>Tambah Jenis Kategori Baru</h1>
@stop

@section('content')
<div class="card card-info shadow">
    <div class="card-header">
        <h3 class="card-title">Form Input Kategori Aset</h3>
    </div>
    
    <form action="{{ route('kategori.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">

            {{-- --- 1. PILIH KATEGORI INDUK (Folder Utama) --- --}}
            <div class="form-group">
                <label for="kategori_induk">Pilih Kategori Induk (Folder Utama)</label>
                <select name="kategori_induk" class="form-control @error('kategori_induk') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Furnitur">Furnitur</option>
                    <option value="Peralatan Kantor">Peralatan Kantor</option>
                    <option value="Peralatan IT / Jaringan">Peralatan IT / Jaringan</option>
                    <option value="Multimedia / Acara">Multimedia / Acara</option>
                    <option value="Laboratorium">Laboratorium</option>
                    <option value="Kendaraan">Kendaraan</option>
                    <option value="Peralatan Kebersihan">Peralatan Kebersihan</option>
                    <option value="Peralatan Olahraga">Peralatan Olahraga</option>
                </select>
                @error('kategori_induk')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 2. Input Kode Jenis --}}
            <div class="form-group">
                <label for="kode_jenis">Kode Jenis / ID Unik</label>
                <input type="text" name="kode_jenis" class="form-control @error('kode_jenis') is-invalid @enderror" 
                       id="kode_jenis" placeholder="Contoh: ELK, FRN, KDR" value="{{ old('kode_jenis') }}" required>
                @error('kode_jenis')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 3. Input Nama Barang (SUDAH DIUBAH) --}}
            <div class="form-group">
                <label for="nama_jenis">Nama Barang / Item</label>
                <input type="text" name="nama_jenis" class="form-control @error('nama_jenis') is-invalid @enderror" 
                       id="nama_jenis" placeholder="Contoh: Meja Kerja, Kursi Putar, Jam Dinding" value="{{ old('nama_jenis') }}" required>
                @error('nama_jenis')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 4. Input Keterangan (SUDAH DIUBAH) --}}
            <div class="form-group">
                <label for="keterangan">Deskripsi Singkat Barang</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                          id="keterangan" rows="3" placeholder="Contoh: Meja kayu jati warna cokelat, ukuran 120x60cm...">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 5. Input Gambar --}}
            <div class="form-group">
                <label for="gambar">Gambar / Ikon Kategori</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar">
                        <label class="custom-file-label" for="gambar">Pilih file gambar...</label>
                    </div>
                </div>
                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                @error('gambar')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info">
                <i class="fas fa-save"></i> Simpan Kategori
            </button>
            <a href="{{ route('kategori.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
    // Script agar nama file muncul di label AdminLTE
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@stop