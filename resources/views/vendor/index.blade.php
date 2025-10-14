@extends('layout.app')

@section('title', 'Vendor')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Daftar Vendor</h3>
                    <button id="tbhVendor" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">
                        <i class="fas fa-user-plus"></i> Tambah Vendor
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="container">
                    <table class="table table-bordered" id="vendorTable">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Vendor</th>
                                <th>Nama Perusahaan</th>
                                <th>Alamat</th>
                                <th>Nomor Telepon</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit vendor -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formVendor">
                    @csrf
                    <input type="hidden" id="vendor_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterLabel">Tambah Vendor Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Vendor</label>
                            <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            var table = $('#vendorTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('vendor.getData') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_vendor',
                        name: 'nama_vendor'
                    },
                    {
                        data: 'nama_perusahaan',
                        name: 'nama_perusahaan',
                        render: function(data) {
                            return data ?? '-';
                        }
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'nomor_telepon',
                        name: 'nomor_telepon'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Reset form saat tambah vendor
            $('#tbhVendor').click(function() {
                $('#exampleModalCenterLabel').text('Tambah Vendor Baru');
                $('#formVendor')[0].reset();
                $('#vendor_id').val('');
            });

            // Simpan / Update vendor
            $('#formVendor').on('submit', function(e) {
                e.preventDefault();
                let id = $('#vendor_id').val();
                let url = id ? '/vendor/' + id : '/vendor';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama_vendor: $('#nama_vendor').val(),
                        nama_perusahaan: $('#nama_perusahaan').val(),
                        alamat: $('#alamat').val(),
                        nomor_telepon: $('#nomor_telepon').val(),
                        email: $('#email').val(),
                    },
                    success: function(response) {
                        $('#exampleModalCenter').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    text: value[0]
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // Edit vendor
            $('#vendorTable').on('click', '.editVendor', function() {
                var id = $(this).data('id');
                $.get('/vendor/' + id, function(data) {
                    $('#exampleModalCenterLabel').text('Edit Vendor');
                    $('#vendor_id').val(data.id);
                    $('#nama_vendor').val(data.nama_vendor);
                    $('#nama_perusahaan').val(data.nama_perusahaan);
                    $('#alamat').val(data.alamat);
                    $('#nomor_telepon').val(data.nomor_telepon);
                    $('#email').val(data.email);
                    $('#exampleModalCenter').modal('show');
                });
            });

            // Hapus vendor dengan SweetAlert2
            $('#vendorTable').on('click', '.deleteVendor', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Data vendor akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/vendor/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
