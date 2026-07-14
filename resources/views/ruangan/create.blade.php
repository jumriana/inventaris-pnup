@extends('adminlte::page')

@section('title', 'Tambah Ruangan')

@section('content_header')
    <h1>Tambah Ruangan Baru</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline shadow">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-door-open mr-2"></i> Form Input Data Ruangan</h3>
            </div>
            
            <form action="{{ route('ruangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    
                    <div class="row">
                        {{-- 1. KODE RUANGAN --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_ruangan">Kode Ruangan</label>
                                <input type="text" name="kode_ruangan" class="form-control @error('kode_ruangan') is-invalid @enderror" 
                                       id="kode_ruangan" placeholder="Contoh: LAB-01, AULA-A" value="{{ old('kode_ruangan') }}" required>
                                @error('kode_ruangan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- 2. NAMA RUANGAN --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_ruangan">Nama Ruangan</label>
                                <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" 
                                       id="nama_ruangan" placeholder="Contoh: Aula Mini, Lab Jaringan" value="{{ old('nama_ruangan') }}" required>
                                <small class="text-muted text-xs">*Gunakan kata 'Aula' agar otomatis butuh surat izin.</small>
                                @error('nama_ruangan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 3. LOKASI --}}
                    <div class="form-group">
                        <label for="lokasi">Lokasi / Gedung</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                            </div>
                            <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" 
                                   id="lokasi" placeholder="Contoh: Gedung C Lantai 2" value="{{ old('lokasi') }}" required>
                        </div>
                        @error('lokasi')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 4. KAPASITAS & STATUS RUANGAN --}}
                    <div class="row">
                        {{-- KAPASITAS --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kapasitas">Kapasitas (Orang)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    </div>
                                    <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" 
                                           id="kapasitas" placeholder="Contoh: 50" value="{{ old('kapasitas') }}" required>
                                </div>
                                @error('kapasitas')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- STATUS RUANGAN (BARU) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status Ruangan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                    </div>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="Dipakai" {{ old('status') == 'Dipakai' ? 'selected' : '' }}>Dipakai</option>
                                        <option value="Perbaikan" {{ old('status') == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                    </select>
                                </div>
                                @error('status')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 5. KETERANGAN --}}
                    <div class="form-group">
                        <label for="keterangan">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" rows="3" placeholder="Fasilitas: AC, Proyektor, Sound System...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Data Ruangan
                    </button>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

{{-- Hubungkan file JS yang sama dengan index --}}
@section('js')
<script src="{{ asset('js/ruangan.js') }}"></script>
@stop