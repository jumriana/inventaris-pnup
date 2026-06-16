@extends('adminlte::page')

@section('title', 'Edit Kendaraan')

@section('content_header')
    <h1>Edit Kendaraan: {{ $kendaraan->nama_kendaraan }}</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-warning card-outline shadow">
            <form action="{{ route('kendaraan.update', $kendaraan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Merk / Tipe Kendaraan</label>
                        <input type="text" name="nama_kendaraan" class="form-control" value="{{ $kendaraan->nama_kendaraan }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor Plat</label>
                                <input type="text" name="plat_nomor" class="form-control" value="{{ $kendaraan->plat_nomor }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" class="form-control" required>
                                    <option value="Mobil" {{ $kendaraan->jenis_kendaraan == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="Motor" {{ $kendaraan->jenis_kendaraan == 'Motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="Bus" {{ $kendaraan->jenis_kendaraan == 'Bus' ? 'selected' : '' }}>Bus / Elf</option>
                                    <option value="Mobil Tangki" {{ $kendaraan->jenis_kendaraan == 'Mobil Tangki' ? 'selected' : '' }}>Mobil Tangki</option>
                                    <option value="Gerobak Tarik" {{ $kendaraan->jenis_kendaraan == 'Gerobak Tarik' ? 'selected' : '' }}>Gerobak Tarik</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-control">
                            <option value="Baik" {{ $kendaraan->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Servis" {{ $kendaraan->kondisi == 'Servis' ? 'selected' : '' }}>Servis</option>
                            <option value="Rusak Ringan" {{ $kendaraan->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak" {{ $kendaraan->kondisi == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $kendaraan->keterangan }}</textarea>
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