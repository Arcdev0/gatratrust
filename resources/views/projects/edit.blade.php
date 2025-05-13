@extends('layout.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Detail Project</h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5>Informasi Project</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">No. Project</th>
                        <td>{{ $project->no_project }}</td>
                    </tr>
                    <tr>
                        <th>Nama Project</th>
                        <td>{{ $project->nama_project }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kerjaan</th>
                        <td>{{ $project->kerjaan->nama_kerjaan }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $project->creator->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Informasi Client</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama Client</th>
                        <td>{{ $project->client->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $project->client->email }}</td>
                    </tr>
                    <tr>
                        <th>Periode Project</th>
                        <td>
                            @if($project->start && $project->end)
                                {{ $project->start->format('d M Y') }} - {{ $project->end->format('d M Y') }}
                            @else
                                Belum ditentukan
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="mb-3">
            <h5>Deskripsi Project</h5>
            <div class="p-3 bg-light rounded">
                {!! nl2br(e($project->deskripsi)) !!}
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('projects.tampilan') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection