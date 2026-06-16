@extends('adminlte::page')

@section('title', 'Edit Barang')

@section('content_header')
    <h1 class="font-weight-bold text-dark"><i class="fas fa-edit mr-2"></i> Edit Inventaris Barang</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white">
                <h3 class="card-title text-secondary">Form Perubahan Data BMN</h3>
            </div>
            
            <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="row">
                        {{-- 1. KODE BARANG --}}
                        <div class="col-md-6 form-group">
                            <label for="kode_barang">Kode Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang" 
                                   class="form-control @error('kode_barang') is-invalid @enderror" 
                                   value="{{ old('kode_barang', $barang->kode_inventaris) }}" required>
                            @error('kode_barang')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 2. NUP / JUMLAH --}}
                        <div class="col-md-6 form-group">
                            <label for="nup">NUP / Jumlah</label>
                            <input type="number" name="nup" id="nup" 
                                   class="form-control @error('nup') is-invalid @enderror" 
                                   value="{{ old('nup', $barang->jumlah_stok) }}" min="1" required>
                            @error('nup')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- 3. NAMA BARANG (Tambahan Baru) --}}
                        <div class="col-md-6 form-group">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" 
                                   class="form-control @error('nama_barang') is-invalid @enderror" 
                                   value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                            @error('nama_barang')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 4. MERK (Memisahkan Teks Merk dari ruangan_id) --}}
                        <div class="col-md-6 form-group">
                            <label for="merk">Merk</label>
                            @php
                                $merkValue = '';
                                // Jika formatnya "Merk: BOSCH | Keterangan..."
                                if (strpos($barang->ruangan_id, 'Merk: ') !== false) {
                                    $part1 = explode('Merk: ', $barang->ruangan_id)[1];
                                    $merkValue = explode(' | ', $part1)[0];
                                } else {
                                    // Antisipasi jika data lama belum menggunakan format gabungan
                                    $merkValue = $barang->nama_barang; 
                                }
                            @endphp
                            <input type="text" name="merk" id="merk" 
                                   class="form-control @error('merk') is-invalid @enderror" 
                                   value="{{ old('merk', $merkValue) }}" required>
                            @error('merk')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- 5. KONDISI BARANG --}}
                        <div class="col-md-12 form-group">
                            <label for="kondisi">Kondisi Barang</label>
                            <select name="kondisi" id="kondisi" class="form-control @error('kondisi') is-invalid @enderror" required>
                                <option value="Baik" {{ old('kondisi', $barang->kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ old('kondisi', $barang->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak" {{ old('kondisi', $barang->kondisi) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                            @error('kondisi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- 6. KETERANGAN TAMBAHAN (Memisahkan Teks Keterangan dari ruangan_id) --}}
                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan (Opsional)</label>
                        @php
                            $ketValue = '';
                            // Jika format menggunakan pemisah " | "
                            if (strpos($barang->ruangan_id, ' | ') !== false) {
                                $ketValue = explode(' | ', $barang->ruangan_id)[1];
                                if($ketValue == 'Tanpa Keterangan') {
                                    $ketValue = '';
                                }
                            } else {
                                // Jika data lama tidak mengandung " | ", ambil langsung isinya
                                $ketValue = $barang->ruangan_id;
                            }
                        @endphp
                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="3" placeholder="Contoh: Lokasi penempatan, riwayat serah terima...">{{ old('keterangan', $ketValue) }}</textarea>
                        @error('keterangan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer bg-white text-right">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary border-0 px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .form-control { border-radius: 8px; border: 1px solid #ddd; }
    .form-control:focus { box-shadow: none; border-color: #007bff; }
</style>
@stop