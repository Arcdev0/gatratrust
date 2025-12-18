@extends('layout.app')

@section('title', 'Prosedur List')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Prosedur</h3>
            <a href="{{ route('procedures.create') }}" class="btn btn-primary">
                + Create Prosedur
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table table-bordered table-striped" id="procedureTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Dok</th>
                                    <th>Nama Dok</th>
                                    <th>Tanggal Berlaku</th>
                                    <th>No. Rev</th>
                                    <th>Tanggal Rev</th>
                                    <th>Status</th>
                                    <th style="width:130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Detail Prosedur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="showModalBody">
                    <div class="text-center py-4">
                        <span class="spinner-border" role="status" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#procedureTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('procedures.datatable') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_dok',
                        name: 'no_dok'
                    },
                    {
                        data: 'nama_dok',
                        name: 'nama_dok'
                    },
                    {
                        data: 'tanggal_berlaku',
                        name: 'tanggal_berlaku'
                    },
                    {
                        data: 'rev_no',
                        name: 'rev_no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_rev',
                        name: 'tanggal_rev',
                        orderable: false,
                        searchable: false
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

            // Show detail modal
            $(document).on('click', '.btn-show', function() {
                const id = $(this).data('id');

                $('#showModal').modal('show');
                $('#showModalBody').html(`
                <div class="text-center py-4">
                    <span class="spinner-border" role="status" aria-hidden="true"></span>
                </div>
            `);

                $.get("{{ url('procedures') }}/" + id, function(res) {
                    $('#showModalBody').html(res);
                }).fail(function(xhr) {
                    let msg = 'Gagal memuat detail.';
                    if (xhr.responseText) msg = xhr.responseText;
                    $('#showModalBody').html(`
                    <div class="alert alert-danger mb-0">
                        ${msg}
                    </div>
                `);
                });
            });

            // Saat modal ditutup, kosongkan body biar bersih
            $('#showModal').on('hidden.bs.modal', function() {
                $('#showModalBody').html('');
            });

        });
    </script>
@endsection
