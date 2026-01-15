@extends('layout.app')

@section('title', 'Journal')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-primary">Journal</h3>
            <a href="{{ route('journals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Journal
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="journalTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Journal No</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(function() {

            let table = $('#journalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('journals.datatable') }}",
                order: [
                    [2, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'journal_no',
                        name: 'journal_no'
                    },
                    {
                        data: 'journal_date',
                        name: 'journal_date'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'reference_no',
                        name: 'reference_no'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Delete
            $(document).on('click', '.btnDeleteJournal', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus jurnal?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/journals/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Success', 'Jurnal berhasil dihapus', 'success');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ??
                                'Gagal menghapus', 'error');
                        }
                    });
                });
            });

        });
    </script>
@endsection
