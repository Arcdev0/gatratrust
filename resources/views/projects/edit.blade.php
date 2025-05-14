@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary font-weight-bold">Daftar Project</h3>
                <button id="tbhProject" class="btn btn-success">
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
                                <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $project->id }}"
                                    data-no_project="{{ $project->no_project }}"
                                    data-nama_project="{{ $project->nama_project }}"
                                    data-client_id="{{ $project->client_id }}" data-kerjaan_id="{{ $project->kerjaan_id }}"
                                    data-deskripsi="{{ $project->deskripsi }}" data-start="{{ $project->start }}"
                                    data-end="{{ $project->end }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal Edit Project -->
<div class="modal fade" id="modalEditProject" tabindex="-1" role="dialog" aria-labelledby="modalEditProjectLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formEditProject" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditProjectLabel">Edit Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>No. Project</label>
                        <input type="text" name="no_project" id="edit_no_project" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Project</label>
                        <input type="text" name="nama_project" id="edit_nama_project" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Client</label>
                        <select name="client_id" id="edit_client_id" class="form-control" required>
                            <option value="">Pilih Client</option>
                            @foreach($listclient as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}- {{ $client->company ?? ''}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kerjaan</label>
                        <select name="kerjaan_id" id="edit_kerjaan_id" class="form-control" required>
                            <option value="">Pilih Kerjaan</option>
                            @foreach($listkerjaan as $kerjaan)
                                <option value="{{ $kerjaan->id }}">{{ $kerjaan->nama_kerjaan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="date" name="start" id="edit_start" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Selesai</label>
                        <input type="date" name="end" id="edit_end" class="form-control">
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

@section('script')
    <script>
        $(function () {
            // Tampilkan modal edit dan isi data
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Tombol yang diklik
                var id = button.data('id');
                var no_project = button.data('no_project');
                var nama_project = button.data('nama_project');
                var client_id = button.data('client_id');
                var pekerjaan_id = button.data('pekerjaan_id');
                var deskripsi = button.data('deskripsi');
                var start = button.data('start');
                var end = button.data('end');

                // Isi data ke dalam modal
                $('#edit_id').val(id);
                $('#edit_no_project').val(no_project);
                $('#edit_nama_project').val(nama_project);
                $('#edit_client_id').val(client_id);
                $('#edit_kerjaan_id').val(pekerjaan_id);
                $('#edit_deskripsi').val(deskripsi);
                $('#edit_start').val(start);
                $('#edit_end').val(end);

            });
            // Submit form edit via AJAX
            $('#formEditProject').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                var form = $(this);
                var btn = form.find('button[type="submit"]');
                btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: '/projects/' + id, // pastikan route resourceful
                    method: 'POST',
                    data: form.serialize(),
                    success: function (res) {
                        $('#modalEditProject').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Gagal update project. Pastikan data sudah benar.');
                    },
                    complete: function () {
                        btn.prop('disabled', false).text('Update');
                    }
                });
            });
        });
        // Tampilkan modal tambah project
        $('#tbhProject').on('click', function () {
            $('#modalEditProject').modal('show');
        });
    </script>
@endsection