@extends('adminlte::page')

@section('title', 'Edit Ruangan')

@section('content_header')
    <h1>Edit Ruangan: {{ $ruangan->nama_ruangan }}</h1>
@stop

@section('content')
<div class="card card-warning card-outline col-md-8 mx-auto shadow">
    
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

    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- TAMBAHAN BARU: Mengirimkan kode_ruangan asli secara tersembunyi agar lolos validasi controller --}}
        <input type="hidden" name="kode_ruangan" value="{{ $ruangan->kode_ruangan }}">

        <div class="card-body">
            <div class="form-group">
                <label>Nama Ruangan</label>
                <input type="text" name="nama_ruangan" class="form-control" value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}" required>
            </div>
            
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $ruangan->lokasi) }}" required>
            </div>
            
            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', $ruangan->kapasitas) }}" required>
            </div>

            {{-- FIELD STATUS RUANGAN (BARU) --}}
            <div class="form-group">
                <label for="status">Status Ruangan</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="Tersedia" {{ old('status', $ruangan->status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipakai" {{ old('status', $ruangan->status) == 'Dipakai' ? 'selected' : '' }}>Dipakai</option>
                    <option value="Perbaikan" {{ old('status', $ruangan->status) == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
                @error('status')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $ruangan->keterangan) }}</textarea>
            </div>
        </div>
        
        <div class="card-footer">
            <button type="submit" class="btn btn-warning px-4 font-weight-bold">Update Data</button>
            <a href="{{ route('ruangan.index') }}" class="btn btn-secondary px-4">Batal</a>
        </div>
    </form>
</div>
@stop