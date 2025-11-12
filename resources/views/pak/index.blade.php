@extends('layout.app')

@section('title', 'Daftar PAK')

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

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary font-weight-bold mb-0">Daftar PAK</h3>
            <a href="{{ route('pak.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah PAK
            </a>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="pakTable">
                        <thead class="thead-light">
                            <tr>
                                <th>No. Project</th>
                                <th>Employee</th>
                                <th>Project Name</th>
                                <th>Nilai Project</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paks as $index => $pak)
                                <tr>
                                    <td>{{ $pak->project_number }}</td>
                                    <td>{{ $pak->employee }}</td>
                                    <td>{{ $pak->project_name }}</td>
                                    <td>{{ $pak->location_project }}</td>
                                    <td>
                                        <small>
                                            Mulai: {{ \Carbon\Carbon::parse($pak->date)->format('d M Y') }}<br>
                                            Selesai: -
                                        </small>
                                    </td>
                                    <td>Rp {{ number_format($pak->project_value, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $totalItems = $pak->items->sum('total_cost');
                                            $hasInvoice = false; // You can add invoice logic later
                                        @endphp

                                        @if($hasInvoice)
                                            <span class="badge badge-success">Ada Invoice</span>
                                        @else
                                            <span class="badge badge-secondary">Belum Ada Invoice</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">0%</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- View Button -->
                                            <a href="{{ route('pak.show', $pak->pak_id) }}" class="btn btn-sm btn-info"
                                                title="Lihat Detail" data-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('pak.edit', $pak->pak_id) }}" class="btn btn-sm btn-secondary"
                                                title="Edit" data-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('pak.destroy', $pak->pak_id) }}" method="POST"
                                                class="d-inline" id="delete-form-{{ $pak->pak_id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-pak"
                                                    data-id="{{ $pak->pak_id }}" title="Hapus" data-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data PAK. Silakan tambah PAK baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .table th {
                font-weight: 600;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 2px solid #dee2e6;
            }

            .table td {
                vertical-align: middle;
                font-size: 0.875rem;
            }

            .btn-group .btn {
                margin: 0 2px;
            }

            .badge {
                padding: 0.4em 0.8em;
                font-weight: 500;
            }

            .card {
                border: none;
                border-radius: 8px;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Initialize DataTable
                var table = $('#pakTable').DataTable({
                    "language": {
                        "lengthMenu": "Show _MENU_ entries",
                        "zeroRecords": "Tidak ada data yang ditemukan",
                        "info": "Showing _START_ to _END_ of _ENTRIES_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "infoFiltered": "(filtered from _MAX_ total entries)",
                        "search": "Search:",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    },
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "columnDefs": [
                        { "orderable": false, "targets": 8 } // Disable sorting on action column
                    ]
                });

                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Delete confirmation
                $(document).on('click', '.delete-pak', function (e) {
                    e.preventDefault();
                    const pakId = $(this).data('id');
                    const form = $('#delete-form-' + pakId);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Data PAK dan semua item terkait akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus data PAK ini?')) {
                            form.submit();
                        }
                    }
                });

                // Auto dismiss alerts
                setTimeout(function () {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endpush
@endsection