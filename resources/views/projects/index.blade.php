@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Daftar Project</h3>
                    @if (Auth::user()->role_id == 1)
                        <button id="tbhProject" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">
                            <i class="fas fa-user-plus"></i> Tambah Project
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="container">
                <table class="table" id="projectTable">
                    <thead>
                        <tr>
                            <th>No. Project</th>
                            <th>Nama Project</th>
                            <th>Client</th>
                            <th>Jenis Kerjaan</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Project -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formTambahProject" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterLabel">Tambah Project Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No. Project</label>
                            <input type="text" name="no_project" placeholder="Masukkan No. Project" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" placeholder="Masukkan Nama Project" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="" selected disabled>Pilih Client</option>
                                @foreach ($listclient as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}- {{ $client->company ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kerjaan</label>
                            <select name="kerjaan_id" class="form-control" required>
                                <option value="" selected disabled>Pilih Kerjaan</option>
                                @foreach ($listkerjaan as $kerjaan)
                                    <option value="{{ $kerjaan->id }}">{{ $kerjaan->nama_kerjaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" placeholder="Masukkan deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Mulai</label>
                            <input type="date" name="start" class="form-control" id="start" required>
                        </div>
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="date" name="end" class="form-control" id="end" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Project -->
    <div class="modal fade" id="EditProjectModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditProject">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Project</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No. Project</label>
                            <input type="text" name="no_project_edit" class="form-control" id="edit_no_project" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" class="form-control" id="edit_nama_project" required>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" class="form-control" id="edit_client_id" required>
                                <option value="">Pilih Client</option>
                                @foreach ($listclient as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }}- {{ $client->company ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kerjaan</label>
                            <select name="kerjaan_id" class="form-control" id="edit_kerjaan_id" required>
                                <option value="">Pilih Kerjaan</option>
                                @foreach ($listkerjaan as $kerjaan)
                                    <option value="{{ $kerjaan->id }}">{{ $kerjaan->nama_kerjaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" id="edit_deskripsi"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Mulai</label>
                            <input type="date" name="start" class="form-control" id="edit_start">
                        </div>
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="date" name="end" class="form-control" id="edit_end">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let today = new Date().toISOString().split('T')[0];
        $('input[name="start"], input[name="end"]').attr('min', today);

        // Jika ingin end tidak boleh sebelum start
        $('input[name="start"]').on('change', function () {
            let startDate = $(this).val();
            $('input[name="end"]').attr('min', startDate);
        });

        // Reset min end saat modal dibuka
        $('#exampleModalCenter, #EditProjectModal').on('show.bs.modal', function () {
            let startVal = $(this).find('input[name="start"]').val();
            if (startVal) {
                $(this).find('input[name="end"]').attr('min', startVal);
            } else {
                $(this).find('input[name="end"]').attr('min', today);
            }
        });

        $('#projectTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('projects.list') }}',
            columns: [{
                data: 'no_project',
                name: 'no_project'
            },
            {
                data: 'nama_project',
                name: 'nama_project'
            },
            {
                data: 'client',
                name: 'client.name'
            },
            {
                data: 'kerjaan',
                name: 'kerjaan.nama_kerjaan'
            },
            {
                data: 'periode',
                name: 'periode',
                orderable: false,
                searchable: false
            },
            {
                data: 'aksi',
                name: 'aksi',
                orderable: false,
                searchable: false
            },
            ]
        });



        // Tambah Project
        $('#formTambahProject').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('projects.store') }}",
                method: "POST",
                data: form.serialize(),
                success: function (res) {
                    $('#exampleModalCenter').modal('hide'); // Fixed modal ID
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Project berhasil ditambahkan!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // reload halaman
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menambah project. Pastikan data sudah benar.'
                    });
                },
                complete: function () {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });

        $(document).on('click', '.btn-edit-project', function () {
            // Ambil data dari tombol yang diklik
            var no = $(this).data('no');
            var nama = $(this).data('nama');
            var client = $(this).data('client');
            var kerjaan = $(this).data('kerjaan');
            var deskripsi = $(this).data('deskripsi');
            var start = $(this).data('start');
            var end = $(this).data('end');

            // Isi form dalam modal dengan data
            $('#edit_no_project').val(no);
            $('#edit_nama_project').val(nama);
            $('#edit_client_id').val(client);
            $('#edit_kerjaan_id').val(kerjaan);
            $('#edit_deskripsi').val(deskripsi);
            $('#edit_start').val(start);
            $('#edit_end').val(end);
        });

        // Edit Project - Submit
        $('#formEditProject').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            var actionUrl = form.attr('action');

            btn.prop('disabled', true).text('Menyimpan...');
            const formData = $(this).serializeArray();

            $.ajax({
                url: actionUrl,
                method: "POST",
                data: form.serialize(),
                success: function (res) {
                    $('#EditProjectModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Project berhasil diperbarui!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengedit project. ' + (xhr.responseJSON
                            ?.message || '')
                    });
                },
                complete: function () {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });
    </script>
@endsection