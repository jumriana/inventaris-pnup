@extends('adminlte::page')

@section('title', 'Tambah Barang')

@section('content_header')
    <h1><i class="fas fa-plus-circle mr-2"></i>Tambah Barang Inventaris Baru</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-primary card-outline shadow">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Form Input Barang</h3>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible mx-3 mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Kesalahan Input!</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        {{-- 1. KODE BARANG --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" id="kode_barang" placeholder="Contoh: 3.02.01.01.002" value="{{ old('kode_barang') }}" required>
                            </div>
                        </div>
                        
                        {{-- 2. NUP / JUMLAH --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nup">NUP / Jumlah</label>
                                <input type="number" name="nup" class="form-control" id="nup" placeholder="Contoh: 29" value="{{ old('nup') }}" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- 3. NAMA BARANG --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_barang">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" id="nama_barang" placeholder="Contoh: Laptop, Kamera, Mesin Bor" value="{{ old('nama_barang') }}" required>
                            </div>
                        </div>

                        {{-- 4. MERK --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="merk">Merk</label>
                                <input type="text" name="merk" class="form-control" id="merk" placeholder="Contoh: Honda, Asus, Epson" value="{{ old('merk') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- 5. KONDISI --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kondisi">Kondisi Barang</label>
                                <select name="kondisi" class="form-control" id="kondisi" required>
                                    <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak" {{ old('kondisi') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                </select>
                            </div>
                        </div>

                        {{-- UPDATE BARU: KATEGORI BARANG --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kategori">Kategori Barang</label>
                                <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" id="kategori" required>
                                    <option value="">-- Pilih Kategori Barang --</option>
                                    <option value="pertukangan" {{ old('kategori') == 'pertukangan' ? 'selected' : '' }}>Alat Pertukangan & Perbaikan</option>
                                    <option value="elektronik" {{ old('kategori') == 'elektronik' ? 'selected' : '' }}>Elektronik & Multimedia</option>
                                    <option value="fasilitas" {{ old('kategori') == 'fasilitas' ? 'selected' : '' }}>Fasilitas Kelas & Kantor</option>
                                    <option value="kebersihan" {{ old('kategori') == 'kebersihan' ? 'selected' : '' }}>Alat Kebersihan & Perawatan</option>
                                    <option value="komunikasi" {{ old('kategori') == 'komunikasi' ? 'selected' : '' }}>Perangkat Komunikasi</option>
                                </select>
                                @error('kategori')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 6. KETERANGAN TAMBAHAN --}}
                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" id="keterangan" rows="3" placeholder="Contoh: Lokasi di Lab Komputer Gedung B Lantai 2...">{{ old('keterangan') }}</textarea>
                    </div>

                </div>

                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Barang</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-default px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop