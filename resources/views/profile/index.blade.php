@extends('layout.app')

@section('title', 'Proposal Anggaran Kerja')

@section('content')

    <div class="container-fluid">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">PAK</h3>
            <a href="{{ route('pak.create') }}" class="btn btn-primary">+ PAK</a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="pakTable">
                            <thead>
                                <tr>
                                    <th>Project Number</th>
                                    <th>Project Name</th>
                                    <th>Project Value</th>
                                    <th>Employee</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paks as $pak)
                                    <tr>
                                        <td>{{ $pak->project_number }}</td>
                                        <td>{{ $pak->project_name }}</td>
                                        <td>Rp {{ number_format($pak->project_value, 0, ',', '.') }}</td>
                                        <td>{{ $pak->employee }}</td>
                                        <td>
                                            <a href="{{ route('pak.show', $pak->pak_id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pak.edit', $pak->pak_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pak.destroy', $pak->pak_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-pak" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data PAK</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#pakTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[0, "desc"]]
    });

    // Delete confirmation
    $(document).on('click', '.delete-pak', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data PAK akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if(confirm('Apakah Anda yakin ingin menghapus data PAK ini?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush
@endsection