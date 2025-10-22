@extends('layout.app')

@section('title', 'Project')

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
            <div class="table-responsive">
                <div class="container">
                    <table class="table" id="projectTable">
                        <thead>
                            <tr>
                                <th>No. Project</th>
                                <th>PIC</th>
                                <th>Company</th>
                                <th>Jenis Kerjaan</th>
                                <th>Periode</th>

                                @if (Auth::user()->role_id == 1)
                                    <th>Nilai Project</th>
                                    <th>Sisa Pembayaran</th>
                                @endif

                                <th>Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
                            <input type="text" id="no_projectGenerate" name="no_project"
                                placeholder="Masukkan No. Project" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" placeholder="Masukkan Nama Project"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="" selected disabled>Pilih Client</option>
                                @foreach ($listclient as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }}{{ $client->company ? ' - ' . $client->company : '' }}
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
                            <label>Deskripsi (Optional)</label>
                            <textarea name="deskripsi" placeholder="Masukkan deskripsi" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Biaya Project</label>
                            <input type="text" class="form-control input-biaya" placeholder="Masukkan Biaya Project">
                            <input type="hidden" name="total_biaya_project" class="biaya-hidden">
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
                <form id="formEditProject" method="POST">
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
                            <input type="text" name="no_project" class="form-control" id="edit_no_project" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Project</label>
                            <input type="text" name="nama_project" class="form-control" id="edit_nama_project"
                                required>
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
                            <label>Deskripsi (Optional)</label>
                            <textarea name="deskripsi" class="form-control" id="edit_deskripsi"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Biaya Project</label>
                            <input type="text" id="edit_biaya_display" class="form-control input-biaya">
                            <input type="hidden" name="total_biaya_project" id="edit_biaya" class="biaya-hidden">
                        </div>
                        <div class="form-group">
                            <label>Mulai</label>
                            <input type="date" name="start_project" class="form-control" id="edit_start">
                        </div>
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="date" name="end_project" class="form-control" id="edit_end">
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
        $('#exampleModalCenter').on('show.bs.modal', function(e) {
            $.ajax({
                url: '/projects/generate-no',
                method: 'GET',
                success: function(response) {
                    $('#no_projectGenerate').val(response.no_project);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal generate No. Project.'
                    });
                }
            });
        });


        $(document).on('click', '.btnDeletProject', function() {
            var projectId = $(this).data('id');

            // Konfirmasi penghapusan
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/projects/delete/${projectId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire(
                                    'Deleted!',
                                    res.message,
                                    'success'
                                ).then(() => {
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message ||
                                        'Terjadi kesalahan saat menghapus project.'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Gagal menghapus project.';

                            // Coba ambil pesan error dari response JSON
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        });
    </script>
    <script>
        let today = new Date().toISOString().split('T')[0];
        $('input[name="start"], input[name="end"]');

        // Jika ingin end tidak boleh sebelum start
        $('input[name="start"]').on('change', function() {
            let startDate = $(this).val();
            $('input[name="end"]').attr('min', startDate);
        });

        // Reset min end saat modal dibuka
        $('#exampleModalCenter, #EditProjectModal').on('show.bs.modal', function() {
            let startVal = $(this).find('input[name="start"]').val();
            if (startVal) {
                $(this).find('input[name="end"]').attr('min', startVal);
            } else {
                $(this).find('input[name="end"]').attr('min', today);
            }
        });

        let table = $('#projectTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('projects.list') }}',
            columns: [{
                    data: 'no_project',
                    name: 'no_project'
                },
                {
                    data: 'client',
                    name: 'client.name'
                },
                {
                    data: 'company',
                    name: 'client.company'
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
                @if (Auth::user()->role_id == 1)
                    {
                        data: 'total_biaya_project',
                        name: 'total_biaya_project',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data == null) return '-';
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    }, {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                @endif {
                    data: 'selesai',
                    name: 'selesai',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }
            ],
            autoWidth: false
        });


        $('#exampleModalCenter').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function parseRupiah(str) {
            return parseInt(str.replace(/[^0-9]/g, '')) || 0;
        }

        // Tambah Project
        $('#formTambahProject').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('projects.store') }}",
                method: "POST",
                data: form.serialize(),
                success: function(res) {
                    $('#exampleModalCenter').modal('hide'); // Fixed modal ID
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Project berhasil ditambahkan!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        table.ajax.reload();
                        $('#formTambahProject')[0].reset(); // Reset form
                        $('#exampleModalCenter').modal('hide');
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menambah project. Pastikan data sudah benar.'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });

        $(document).on('click', '.btn-edit-project', function() {
            // Ambil data dari tombol yang diklik
            var projectId = $(this).data('id');
            var no = $(this).data('no');
            var nama = $(this).data('nama');
            var client = $(this).data('client');
            var kerjaan = $(this).data('kerjaan');
            var deskripsi = $(this).data('deskripsi');
            var start = $(this).data('start');
            var end = $(this).data('end');
            var biaya = $(this).data('biaya'); // pastikan tombol punya data-biaya

            // Isi form dalam modal dengan data
            $('#edit_no_project').val(no);
            $('#edit_nama_project').val(nama);
            $('#edit_client_id').val(client);
            $('#edit_kerjaan_id').val(kerjaan);
            $('#edit_deskripsi').val(deskripsi);
            $('#edit_start').val(start);
            $('#edit_end').val(end);

            // isi biaya (tampilkan dalam format Rp di input, dan angka asli di hidden)
            $('#edit_biaya_display').val(formatRupiah(biaya.toString()));
            $('#edit_biaya').val(biaya);

            // Simpan projectId di form (untuk action / AJAX update)
            $('#formEditProject').data('project-id', projectId);
        });

        $(document).on('input', '.input-biaya', function() {
            let angka = parseRupiah($(this).val());
            $(this).siblings('.biaya-hidden').val(angka);
            $(this).val(formatRupiah(angka));
        });


        let originalKerjaan = null;
        let originalStart = null;

        // Simpan nilai awal setelah data dimasukkan ke form
        $('#EditProjectModal').on('shown.bs.modal', function() {
            originalKerjaan = $('#edit_kerjaan_id').val();
            originalStart = $('#edit_start').val() ? $('#edit_start').val().split('T')[0] : null; // normalize
        });

        // Saat form submit
        $('#formEditProject').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const btn = form.find('button[type="submit"]');
            const projectId = form.data('project-id');
            const actionUrl = '/projects/update/' + projectId;

            const newKerjaan = $('#edit_kerjaan_id').val();
            const newStartRaw = $('#edit_start').val();
            const newStart = newStartRaw ? newStartRaw.split('T')[0] : null; // normalize format

            const kerjaanChanged = newKerjaan !== originalKerjaan;
            const startChanged = newStart !== originalStart;

            if (kerjaanChanged || startChanged) {
                Swal.fire({
                    title: "Perubahan Sensitif!",
                    html: "Mengubah <b>Jenis Kerjaan</b> atau <b>Tanggal Mulai</b> akan <span style='color:red;'>menghapus semua detail project lama</span> dan membuat ulang dari awal.<br><br>Apakah Anda yakin ingin melanjutkan?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, lanjutkan",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitEditAjax(form, btn, actionUrl);
                    }
                });
            } else {
                submitEditAjax(form, btn, actionUrl);
            }
        });

        // Fungsi AJAX update project
        function submitEditAjax(form, btn, actionUrl) {
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: actionUrl,
                method: "POST",
                data: form.serialize(),
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Project berhasil diperbarui!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        table.ajax.reload();
                        $('#EditProjectModal').modal('hide');
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengedit project. ' + (xhr.responseJSON?.message || '')
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update');
                }
            });
        }



        //hapus project
        $(document).on('click', '.DeleteProjectModal', function() {
            var projectId = $(this).data('id');
            var projectName = $(this).data('name');
            // Konfirmasi penghapusan
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/projects/delete/${projectId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire(
                                'Deleted!',
                                'Project berhasil dihapus.',
                                'success'
                            ).then(() => {
                                table.ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menghapus project. Pastikan data sudah benar.'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
