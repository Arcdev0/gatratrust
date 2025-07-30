@extends('layout.app')
@section('title', 'Jabatan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Jabatan</h3>
                    <button id="openModalBtn" class="btn btn-success">
                        Tambah Tipe Jabatan
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table id="tableJabatan" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Jabatan</th>
                                        <th>Syarat</th>
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

    {{-- Modal Tambah --}}
    <div class="modal fade" id="addJabatanModal" tabindex="-1" aria-labelledby="addJabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJabatanModalLabel">Tambah Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formJabatan">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Jabatan</label>
                            <input type="text" name="nama_jabatan" class="form-control" required>
                        </div>
                        <label>Syarat Jabatan</label>
                        <div id="syarat-wrapper">
                            <div class="input-group mb-2">
                                <input type="text" name="nama_syarat[]" class="form-control" placeholder="Nama Syarat">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success add-syarat">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="saveJabatanBtn" class="btn btn-primary">Simpan Jabatan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editJabatanModal" tabindex="-1" aria-labelledby="editJabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="formEditJabatan">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Jabatan</label>
                            <input type="text" name="nama_jabatan" class="form-control" required>
                        </div>

                        <label>Syarat Jabatan</label>
                        <div id="edit-syarat-wrapper">
                            {{-- Akan diisi via JS --}}
                        </div>
                        <button type="button" class="btn btn-success add-syarat">+ Tambah Syarat</button>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Jabatan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            var table = $('#tableJabatan').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('jabatan.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_jabatan'
                    },
                    {
                        data: 'syarat_list',
                        orderable: false,
                        searchable: false
                    }, // list syarat
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            // Buka modal tambah
            $('#openModalBtn').on('click', function() {
                $('#formJabatan')[0].reset();
                $('#syarat-wrapper').html(`
            <div class="input-group mb-2">
                <input type="text" name="nama_syarat[]" class="form-control" placeholder="Nama Syarat">
                <div class="input-group-append">
                    <button type="button" class="btn btn-success add-syarat">+</button>
                </div>
            </div>
        `);
                $('#addJabatanModal').modal('show');
            });

            // Tambah input syarat dinamis (untuk kedua modal)
            $(document).on('click', '.add-syarat', function() {
                let wrapper = $(this).closest('.modal-body').find('[id$="-wrapper"]');
                wrapper.append(`
            <div class="input-group mb-2">
                <input type="text" name="nama_syarat[]" class="form-control" placeholder="Nama Syarat">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-syarat">-</button>
                </div>
            </div>
        `);
            });

            // Hapus input syarat
            $(document).on('click', '.remove-syarat', function() {
                $(this).closest('.input-group').remove();
            });

            // Submit tambah jabatan
            $('#formJabatan').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('jabatan.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        $('#addJabatanModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

            // Buka modal edit
            $(document).on('click', '.edit', function() {
                var id = $(this).data('id');
                $.get('/jabatan/' + id + '/edit', function(data) {
                    $('#editJabatanModal input[name="nama_jabatan"]').val(data.nama_jabatan);
                    $('#editJabatanModal input[name="id"]').val(data.id);

                    var wrapper = $('#edit-syarat-wrapper');
                    wrapper.html('');
                    data.syarat_jabatan.forEach(function(s) {
                        wrapper.append(`
                    <div class="input-group mb-2">
                        <input type="text" name="nama_syarat[]" class="form-control" value="${s.nama_syarat}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-syarat">-</button>
                        </div>
                    </div>
                `);
                    });

                    $('#editJabatanModal').modal('show');
                });
            });

            // Submit edit jabatan
            $('#formEditJabatan').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editJabatanModal input[name="id"]').val();
                $.ajax({
                    url: '/jabatan/' + id,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function() {
                        $('#editJabatanModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
        });
    </script>
@endsection
