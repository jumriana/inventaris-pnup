@extends('adminlte::page')

@section('title', 'Edit Ruangan')

@section('content_header')
    <h1>Edit Ruangan: {{ $ruangan->nama_ruangan }}</h1>
@stop

@section('content')
<div class="card card-warning card-outline col-md-8 mx-auto shadow">
    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Nama Ruangan</label>
                <input type="text" name="nama_ruangan" class="form-control" value="{{ $ruangan->nama_ruangan }}" required>
            </div>
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control" value="{{ $ruangan->lokasi }}" required>
            </div>
            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="form-control" value="{{ $ruangan->kapasitas }}" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ $ruangan->keterangan }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning px-4 font-weight-bold">Update Data</button>
            <a href="{{ route('ruangan.index') }}" class="btn btn-secondary px-4">Batal</a>
        </div>
    </form>
</div>
@stop