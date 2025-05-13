@extends('layout.app')

@section('content')
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1">Daftar Project</h3>
            <span class="text-muted">Manajemen seluruh project perusahaan</span>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Project
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">No. Project</th>
                        <th width="20%">Nama Project</th>
                        <th width="15%">Client</th>
                        <th width="15%">Jenis Kerjaan</th>
                        <th width="20%">Periode</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $project->no_project }}</td>
                        <td>{{ $project->nama_project }}</td>
                        <td>{{ $project->client->name }}</td>
                        <td>{{ $project->kerjaan->nama_kerjaan }}</td>
                        <td>
                            @if($project->start && $project->end)
                            <small class="d-block">Mulai: {{ $project->start->format('d M Y') }}</small>
                            <small class="d-block">Selesai: {{ $project->end->format('d M Y') }}</small>
                            @else
                            <span class="text-muted">Belum ditentukan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

