@extends('layout.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>{{ isset($project) ? 'Edit' : 'Tambah' }} Project</h3>
    </div>
    <div class="card-body">
        <form action="{{ isset($project) ? route('projects.update', $project->id) : route('projects.store') }}" method="POST">
            @csrf
            @if(isset($project))
                @method('PUT')
            @endif
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No. Project</label>
                        <input type="text" name="no_project" class="form-control" 
                               value="{{ old('no_project', $project->no_project ?? '') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Project</label>
                        <input type="text" name="nama_project" class="form-control" 
                               value="{{ old('nama_project', $project->nama_project ?? '') }}" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Client</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">Pilih Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" 
                                {{ (old('client_id', $project->client_id ?? '') == $client->id) ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Kerjaan</label>
                        <select name="kerjaan_id" class="form-control" required>
                            <option value="">Pilih Jenis Kerjaan</option>
                            @foreach($kerjaans as $kerjaan)
                            <option value="{{ $kerjaan->id }}" 
                                {{ (old('kerjaan_id', $project->kerjaan_id ?? '') == $kerjaan->id) ? 'selected' : '' }}>
                                {{ $kerjaan->nama_kerjaan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Deskripsi Project</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $project->deskripsi ?? '') }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start" class="form-control" 
                               value="{{ old('start', isset($project->start) ? $project->start->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="end" class="form-control" 
                               value="{{ old('end', isset($project->end) ? $project->end->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('projects.tampilan') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection