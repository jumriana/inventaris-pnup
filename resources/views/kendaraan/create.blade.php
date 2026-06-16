@extends('adminlte::page')

@section('title', 'Tambah Kendaraan')

@section('content_header')
    <h1>Input Kendaraan Baru</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-warning card-outline shadow">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-car mr-2"></i> Form Informasi Kendaraan</h3>
            </div>
            
            {{-- Bagian untuk menampilkan error jika validasi gagal --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible mx-3 mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Ada Kesalahan!</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kendaraan.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    {{-- 1. NAMA KENDARAAN --}}
                    <div class="form-group">
                        <label for="nama_kendaraan">Merk / Tipe Kendaraan</label>
                        <input type="text" name="nama_kendaraan" class="form-control @error('nama_kendaraan') is-invalid @enderror" 
                               id="nama_kendaraan" placeholder="Contoh: Toyota Avanza, Honda Vario" value="{{ old('nama_kendaraan') }}" required>
                    </div>

                    <div class="row">
                        {{-- 2. PLAT NOMOR (Sesuai nama di database) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plat_nomor">Nomor Plat (Nopol)</label>
                                <input type="text" name="plat_nomor" class="form-control @error('plat_nomor') is-invalid @enderror" 
                                       id="plat_nomor" placeholder="Contoh: DD 1234 AB" value="{{ old('plat_nomor') }}" required>
                            </div>
                        </div>

                        {{-- 3. JENIS KENDARAAN (Penting untuk Ikon) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_kendaraan">Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" class="form-control" id="jenis_kendaraan" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Mobil" {{ old('jenis_kendaraan') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="Motor" {{ old('jenis_kendaraan') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="Bus" {{ old('jenis_kendaraan') == 'Bus' ? 'selected' : '' }}>Bus / Elf</option>
                                    <option value="Mobil Tangki" {{ old('jenis_kendaraan') == 'Mobil Tangki' ? 'selected' : '' }}>Mobil Tangki</option>
                                    <option value="Gerobak Tarik" {{ old('jenis_kendaraan') == 'Gerobak Tarik' ? 'selected' : '' }}>Gerobak Tarik</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. KONDISI --}}
                    <div class="form-group">
                        <label for="kondisi">Kondisi Kendaraan</label>
                        <select name="kondisi" class="form-control" id="kondisi">
                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik (Siap Pakai)</option>
                            <option value="Servis" {{ old('kondisi') == 'Servis' ? 'selected' : '' }}>Sedang Servis</option>
                            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak" {{ old('kondisi') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    {{-- 5. KETERANGAN --}}
                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" id="keterangan" rows="3" placeholder="Contoh: BPKB ada, bensin penuh...">{{ old('keterangan') }}</textarea>
                    </div>

                </div>

                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-warning font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Data Kendaraan
                    </button>
                    <a href="{{ route('kendaraan.index') }}" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop