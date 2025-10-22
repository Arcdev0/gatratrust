@extends('layout.app')

@section('title', 'MPI Tests')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">MPI Tests</h3>
                    <button id="btn-add" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalForm">Tambah
                        MPI
                        Test</button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="container">
                                <table id="mpi_table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama PT</th>
                                            <th>Tanggal Running</th>
                                            <th>Tanggal Inspection</th>
                                            <th>Person</th>
                                            <th>Creator</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Create / Edit MPI Test -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formMpi" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah MPI Test</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="mpi_id" name="mpi_id" value="">
                    <div class="mb-3">
                        <label for="nama_pt" class="form-label">Nama PT</label>
                        <input type="text" class="form-control" id="nama_pt" name="nama_pt" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_running" class="form-label">Tanggal Running</label>
                        <input type="date" class="form-control" id="tanggal_running" name="tanggal_running">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_inspection" class="form-label">Tanggal Inspection</label>
                        <input type="date" class="form-control" id="tanggal_inspection" name="tanggal_inspection">
                    </div>
                    <div class="mb-3">
                        <label for="person" class="form-label">Person (jumlah)</label>
                        <input type="number" class="form-control" id="person" name="person" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Delete confirm -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Yakin ingin menghapus data ini?</p>
                    <input type="hidden" id="delete_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // init DataTable
            const table = $('#mpi_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('mpi.tests.data') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_pt',
                        name: 'nama_pt'
                    },
                    {
                        data: 'tanggal_running',
                        name: 'tanggal_running'
                    },
                    {
                        data: 'tanggal_inspection',
                        name: 'tanggal_inspection'
                    },
                    {
                        data: 'person',
                        name: 'person'
                    },
                    {
                        data: 'creator_name',
                        name: 'creator_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '220px'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });


            // Open Create modal: reset form
            $('#btn-add').on('click', function() {
                $('#modalTitle').text('Tambah MPI Test');
                $('#formMpi')[0].reset();
                $('#mpi_id').val('');
                $('#modalForm').modal('show');
            });

            // Submit Create / Update via AJAX
            $('#formMpi').on('submit', function(e) {
                e.preventDefault();
                const id = $('#mpi_id').val();
                const url = id ? '{{ url('mpi-tests') }}/' + id : '{{ route('mpi.store') }}';
                const method = id ? 'PUT' : 'POST';
                const formData = {
                    nama_pt: $('#nama_pt').val(),
                    tanggal_running: $('#tanggal_running').val() || null,
                    tanggal_inspection: $('#tanggal_inspection').val() || null,
                    person: $('#person').val() || null
                };

                $('#btnSave').prop('disabled', true);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        table.ajax.reload(null, false);
                        alert(res.message || 'Success');
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        alert(msg);
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false);
                    }
                });
            });

            // Edit button (delegated)
            $('#mpi_table').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $('#modalTitle').text('Edit MPI Test');
                $('#formMpi')[0].reset();
                $('#mpi_id').val(id);
                // load data
                $.getJSON('{{ url('mpi-tests') }}/' + id + '/edit', function(data) {
                    $('#nama_pt').val(data.nama_pt);
                    $('#tanggal_running').val(data.tanggal_running ? data.tanggal_running.split(
                        'T')[0] : '');
                    $('#tanggal_inspection').val(data.tanggal_inspection ? data.tanggal_inspection
                        .split('T')[0] : '');
                    $('#person').val(data.person ?? '');
                    $('#modalForm').modal('show');
                }).fail(function() {
                    alert('Gagal memuat data untuk edit.');
                });
            });

            // Delete: open modal
            $('#mpi_table').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                $('#delete_id').val(id);
                $('#modalDelete').modal('show');
            });

            // Confirm delete
            $('#confirmDelete').on('click', function() {
                const id = $('#delete_id').val();
                $('#confirmDelete').prop('disabled', true);
                $.ajax({
                    url: '{{ url('mpi-tests') }}/' + id,
                    method: 'DELETE',
                    success: function(res) {
                        $('#modalDelete').modal('hide');
                        table.ajax.reload(null, false);
                        alert(res.message || 'Deleted');
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Gagal menghapus');
                    },
                    complete: function() {
                        $('#confirmDelete').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
