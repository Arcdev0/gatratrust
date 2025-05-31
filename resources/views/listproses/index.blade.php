@extends('layout.app')
@section('title', 'List Proses')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">List Proses</h3>
                    <button id="addListProsesBtn" class="btn btn-success" data-toggle="modal" data-target="#addProsesModal">
                        <i class="fas fa-plus"></i> Tambah Proses
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                        <table class="table table-bordered" id="listProsesTable">
                            <thead style="background-color: #f2f2f2;">
                                <tr>
                                    <th>Nama Proses</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listProses as $index => $proses)
                                    <tr>
                                        <td>{{ $proses->nama_proses }}</td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm btnEditProses" data-id="{{ $proses->id }}"
                                                data-nama="{{ $proses->nama_proses }}" data-deskripsi="{{ $proses->deskripsi }}"
                                                data-toggle="modal" data-target="#editProsesModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('listproses.destroy', $proses->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btnDeleteListProses">
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
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addProsesModal" tabindex="-1" role="dialog" aria-labelledby="addProsesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addProsesForm" action="{{ route('listproses.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProsesModalLabel">Tambah Proses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_proses">Nama Proses</label>
                            <input type="text" class="form-control" name="nama_proses" id="nama_proses" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editProsesModal" tabindex="-1" role="dialog" aria-labelledby="editProsesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editProsesForm">
                <input type="hidden" name="id" id="editId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProsesModalLabel">Edit Proses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editNamaProses">Nama Proses</label>
                            <input type="text" class="form-control" name="nama_proses" id="editNamaProses" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#listProsesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });

        $(document).ready(function () {
            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Tampilkan Modal Tambah Proses
            $('#addListProsesBtn').click(function () {
                $('#addProsesForm')[0].reset(); // Reset form
                $('#addProsesModal').modal('show');
            });

            // Submit Form Tambah Proses
            $('#addProsesForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('listproses.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#addProsesModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Gagal menambah proses: ' + xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                });
            });

            // Tampilkan Modal Edit Proses
            $('.btnEditProses').click(function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const deskripsi = $(this).data('deskripsi');

                $('#editId').val(id);
                $('#editNamaProses').val(nama);
                $('#editDeskripsi').val(deskripsi);
                $('#editProsesForm').attr('action', "{{ url('listproses/update') }}/" + id);
                $('#editProsesModal').modal('show');
            });

            // Submit Form Edit Proses
            $('#editProsesForm').submit(function (e) {
                e.preventDefault();
                const id = $('#editId').val();
                $.ajax({
                    url: "{{ url('listproses/update') }}/" + id,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#editProsesModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Gagal mengupdate proses: ' + xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                });
            });

            $(document).on('click', '.btnDeleteListProses', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "User akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        });

    </script>
@endsection