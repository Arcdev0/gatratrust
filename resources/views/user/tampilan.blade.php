@extends('layout.app')
@section('title', 'User Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Manajemen User</h3>
                    <button id="addUserBtn" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Tambah User
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-bordered" id="usersTable">
                                <thead style="background-color: #f2f2f2;">
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status </th>
                                        <th>Company</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role->name }}</td>
                                            <td>{{ $user->status }}</td>
                                            <td>{{ $user->company ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-secondary btnEditUser"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}" data-role="{{ $user->role_id }}"
                                                    data-company="{{ $user->company ?? '' }}"
                                                    data-status="{{ $user->is_active }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btnDeleteUser">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addUserForm" action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" placeholder="Masukkan nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Masukkan email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Masukkan password" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role_id" class="form-control" required>
                                <option value="" selected disabled>Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Perusahaan (opsional)</label>
                            <input type="text" name="company" placeholder="Masukkan perusahaan" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="1">Active</option>
                                <option value="0">Non Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password (biarkan kosong jika tidak diubah)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role_id" id="editRole" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Perusahaan (opsional)</label>
                            <input type="text" name="company" id="editCompany" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" id="editIsActive" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Non Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('user.list') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role.name'
                    },
                    {
                        data: 'status',
                        name: 'is_active',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            // Tampilkan Modal Tambah User
            $('#addUserBtn').click(function() {
                $('#addUserModal').modal('show');
            });

            // Submit Form Tambah User
            $('#addUserForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(response) {
                        $('#addUserModal').modal('hide');
                        form[0].reset();
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Gagal menambahkan user. Pastikan data valid.');
                    }
                });
            });


            // Tampilkan Modal Edit User
            $('#usersTable').on('click', '.btnEditUser', function() {
                var userId = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var role = $(this).data('role');
                var company = $(this).data('company');
                var status = $(this).data('status');

                $('#editName').val(name);
                $('#editEmail').val(email);
                $('#editRole').val(String(role));
                $('#editCompany').val(company);
                $('#editIsActive').val(String(status));

                $('#editUserForm').attr('action', `/user/update/${userId}`);
                $('#editUserModal').modal('show');
            });

            // Submit Form Edit User
            $('#editUserForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(response) {
                        $('#editUserModal').modal('hide');
                        form[0].reset();
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Gagal mengedit user. Pastikan data valid.');
                    }
                });
            });

            $(document).on('click', '.btnDeleteUser', function() {
                var userId = $(this).data('id'); // Ambil ID user

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
                        $.ajax({
                            url: `/user/delete/${userId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    $('#usersTable').DataTable().ajax.reload(null,
                                        false);
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Terjadi kesalahan pada server.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
