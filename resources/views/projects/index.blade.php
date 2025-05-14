@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Daftar Project</h3>
                    <button id="tbhProject" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">
                        <i class="fas fa-user-plus"></i> Tambah Project
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="projectTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Project</th>
                            <th>Nama Project</th>
                            <th>Client</th>
                            <th>Jenis Kerjaan</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $project->no_project }}</td>
                                <td>{{ $project->nama_project }}</td>
                                <td>{{ $project->client->name }}</td>
                                <td>{{ $project->kerjaan->nama_kerjaan }}</td>
                                <td>
                                    @if($project->start && $project->end)
                                        <small class="d-block">Mulai: {{ $project->start?->format('d M Y') }}</small>
                                        <small class="d-block">Selesai: {{ $project->end?->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info" href="{{ route('projects.show', $project->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning btn-edit-project" data-toggle="modal"
                                        data-target="#EditProjectModal" data-id="{{ $project->id }}"
                                        data-no="{{ $project->no_project }}" data-nama="{{ $project->nama_project }}"
                                        data-client="{{ $project->client_id }}" data-kerjaan="{{ $project->kerjaan_id }}"
                                        data-deskripsi="{{ $project->deskripsi }}"
                                        data-start="{{ $project->start?->format('Y-m-d') }}"
                                        data-end="{{ $project->end?->format('Y-m-d') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('projects.destroy', $project->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin?')">
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
                            <input type="text" name="no_project" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">Pilih Client</option>
                                @foreach($listclient as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}- {{ $client->company ?? ''}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kerjaan</label>
                            <select name="kerjaan_id" class="form-control" required>
                                <option value="">Pilih Kerjaan</option>
                                @foreach($listkerjaan as $kerjaan)
                                    <option value="{{ $kerjaan->id }}">{{ $kerjaan->nama_kerjaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Mulai</label>
                            <input type="date" name="start" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="date" name="end" class="form-control">
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
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Project</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No. Project</label>
                            <input type="text" name="no_project" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">Pilih Client</option>
                                @foreach($listclient as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }}- {{ $client->company ?? ''}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kerjaan</label>
                            <select name="kerjaan_id" class="form-control" required>
                                <option value="">Pilih Kerjaan</option>
                                @foreach($listkerjaan as $kerjaan)
                                    <option value="{{ $kerjaan->id }}">{{ $kerjaan->nama_kerjaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Mulai</label>
                            <input type="date" name="start" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="date" name="end" class="form-control">
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
@endsection

@section('script')
    <script>
        $(function () {
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

            // Edit Project - Set Data
            $('.btn-edit-project').on('click', function () {
                let btn = $(this);
                let form = $('#formEditProject');
                let projectId = btn.data('id');

                // Set form action URL
                form.attr('action', `/projects/update/${projectId}`);

                // Fill form with project data
                form.find('input[name="no_project"]').val(btn.data('no'));
                form.find('input[name="nama_project"]').val(btn.data('nama'));
                form.find('select[name="client_id"]').val(btn.data('client'));
                form.find('select[name="kerjaan_id"]').val(btn.data('kerjaan'));
                form.find('textarea[name="deskripsi"]').val(btn.data('deskripsi'));
                form.find('input[name="start"]').val(btn.data('start'));
                form.find('input[name="end"]').val(btn.data('end'));
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
                            text: 'Gagal mengedit project. ' + (xhr.responseJSON?.message || '')
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false).text('Simpan');
                    }
                });
            });
        });
    </script>
@endsection