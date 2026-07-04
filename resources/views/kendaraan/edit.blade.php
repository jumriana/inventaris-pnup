@extends('adminlte::page')

@section('title', 'Edit Kendaraan')

@section('content_header')
    <h1>Edit Kendaraan: {{ $kendaraan->nama_kendaraan }}</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-warning card-outline shadow">
            
            {{-- Blok Pesan Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible mx-3 mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal Memperbarui Data!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kendaraan.update', $kendaraan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    {{-- 1. Merk / Tipe Kendaraan --}}
                    <div class="form-group">
                        <label>Merk / Tipe Kendaraan</label>
                        <input type="text" name="nama_kendaraan" class="form-control" value="{{ old('nama_kendaraan', $kendaraan->nama_kendaraan) }}" required>
                    </div>

                    <div class="row">
                        {{-- 2. Nomor Plat --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor Plat</label>
                                <input type="text" name="plat_nomor" class="form-control" value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}" required>
                            </div>
                        </div>
                        
                        {{-- 3. Jenis Kendaraan --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" class="form-control" required>
                                    <option value="Mobil" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="Motor" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == 'Motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="Bus" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == 'Bus' ? 'selected' : '' }}>Bus / Elf</option>
                                    <option value="Mobil Tangki" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == 'Mobil Tangki' ? 'selected' : '' }}>Mobil Tangki</option>
                                    <option value="Gerobak Tarik" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == 'Gerobak Tarik' ? 'selected' : '' }}>Gerobak Tarik</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Kondisi --}}
                    <div class="form-group">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-control" required>
                            <option value="Baik" {{ old('kondisi', $kendaraan->kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Servis" {{ old('kondisi', $kendaraan->kondisi) == 'Servis' ? 'selected' : '' }}>Servis</option>
                            <option value="Rusak Ringan" {{ old('kondisi', $kendaraan->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            {{-- PERBAIKAN: Berhasil diubah dari Rusak menjadi Rusak Berat --}}
                            <option value="Rusak Berat" {{ old('kondisi', $kendaraan->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    {{-- 5. Keterangan --}}
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $kendaraan->keterangan) }}</textarea>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning font-weight-bold">Update Data</button>
                    <a href="{{ route('kendaraan.index') }}" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop