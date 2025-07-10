@extends('layout.app')
@section('title', 'Tipe Project')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Tipe Project</h3>
                    <button id="openModalBtn" class="btn btn-success">
                        Tambah Tipe Project
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-bordered" id="kerjaanTable">
                                <thead>
                                    <tr>
                                        <th>Nama Tipe Project</th>
                                        <th>Dibuat Pada</th>
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

    <!-- Modal Tambah Pekerjaan -->
    <div class="modal fade" id="addPekerjaanModal" tabindex="-1" aria-labelledby="addPekerjaanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPekerjaanModalLabel">Tambah tipe project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Input Nama Pekerjaan -->
                    <div class="mb-3">
                        <label for="namaPekerjaan" class="form-label">Nama Pekerjaan</label>
                        <input type="text" class="form-control" id="namaPekerjaan"
                            placeholder="Contoh: Welding Prosedure 0.1">
                    </div>

                    <hr>

                    <h6>Tambah Proses</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="prosesSelect">Pilih Proses</label>
                            <select id="prosesSelect" class="form-control">
                                <option value="">-- Pilih Proses --</option>
                                @foreach ($listProses as $proses)
                                    <option value="{{ $proses->nama_proses }}" data-id="{{ $proses->id }}">
                                        {{ $proses->nama_proses }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="addProsesBtn" class="btn btn-success w-100">Tambah</button>
                        </div>
                    </div>

                    <!-- Tabel Proses -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Proses</th>
                                <th>Deadline (Hari)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="listProsesTable">
                            <!-- List proses akan muncul di sini -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="savePekerjaanBtn" class="btn btn-primary">Simpan Pekerjaan</button>
                    <button type="button" class="btn btn-secondary" id="closeModalFooterBtn">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Edit Pekerjaan -->
    <div class="modal fade" id="editPekerjaanModal" tabindex="-1" aria-labelledby="editPekerjaanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPekerjaanModalLabel">Edit tipe project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Input Nama Pekerjaan -->
                    <input type="hidden" id="editKerjaanId">
                    <div class="mb-3">
                        <label for="editNamaPekerjaan" class="form-label">Nama Pekerjaan</label>
                        <input type="text" class="form-control" id="editNamaPekerjaan"
                            placeholder="Contoh: Welding Procedure 0.1">
                    </div>

                    <hr>

                    <h6>Edit Proses</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="editProsesSelect">Pilih Proses</label>
                            <select id="editProsesSelect" class="form-control">
                                <option value="">-- Pilih Proses --</option>
                                @foreach ($listProses as $proses)
                                    <option value="{{ $proses->nama_proses }}" data-id="{{ $proses->id }}">
                                        {{ $proses->nama_proses }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="editAddProsesBtn" class="btn btn-success w-100">Tambah</button>
                        </div>
                    </div>

                    <!-- Tabel Proses -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Proses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="editListProsesTable">
                            <!-- List proses akan muncul di sini -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="saveEditPekerjaanBtn" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" id="closeModalEditFooterBtn">Tutup</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        let listProses = [];

        $(document).ready(function() {
            $(function() {
                $('#kerjaanTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('kerjaan.data') }}',
                    columns: [{
                            data: 'nama_kerjaan',
                            name: 'nama_kerjaan'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });

            $(document).on('click', '.delete-kerjaan', function() {
                let id = $(this).data('id'); // Ambil ID dari data-id

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data pekerjaan ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/kerjaan/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                $('#kerjaanTable').DataTable().ajax.reload();

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6'
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus data.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });

            // Show modal
            $('#openModalBtn').click(function() {
                $('#addPekerjaanModal').modal('show');
            });

            // Close modal
            $('#closeModalBtn, #closeModalFooterBtn').click(function() {
                $('#addPekerjaanModal').modal('hide');
                resetModal();
            });

            // Tambahkan proses ke list
            $('#addProsesBtn').click(function() {
                let selectedOption = $('#prosesSelect option:selected');
                let prosesId = selectedOption.data('id');
                let prosesText = selectedOption.text();

                if (prosesId) {

                    let urutan = listProses.length + 1;
                    listProses.push({
                        id: prosesId,
                        proses: prosesText,
                        urutan: urutan
                    });

                    renderTable();

                    // Reset select
                    $('#prosesSelect').val('');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih proses terlebih dahulu!',
                        confirmButtonColor: '#3085d6',
                    });
                }
            });

            // Simpan pekerjaan dengan AJAX
            $('#savePekerjaanBtn').click(function() {
                let namaPekerjaan = $('#namaPekerjaan').val();

                if (!namaPekerjaan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nama pekerjaan harus diisi!',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }

                if (listProses.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Minimal 1 proses harus ditambahkan!',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }

                $('#listProsesTable tr').each(function(index) {
                    let hariInput = $(this).find('input[type="number"]').val();
                    listProses[index].hari = parseInt(hariInput) || 0;
                });

                // Kirim data ke backend
                $.ajax({
                    url: '{{ route('kerjaan.store') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        nama_pekerjaan: namaPekerjaan,
                        proses: listProses,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Menyimpan...',
                            html: 'Sedang menyimpan data pekerjaan',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addPekerjaanModal').modal('hide');
                                    resetModal();
                                    $('#kerjaanTable').DataTable().ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan data: ' + error,
                            confirmButtonColor: '#3085d6',
                        });
                    }
                });
            });
        });

        // Render tabel proses
        function renderTable() {
            let html = '';
            listProses.forEach((item, index) => {
                html += `
                <tr>
                    <td>${item.urutan}</td>
                    <td>${item.proses}</td>
                    <td>
                        <input type="number" 
                            class="form-control" 
                            value="${item.hari ?? 0}" 
                            min="0" 
                            onchange="updateHari(${index}, this.value)">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="removeProses(${index})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            });
            $('#listProsesTable').html(html);
        }

        // Hapus proses dari list
        function removeProses(index) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Proses ini akan dihapus dari daftar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    listProses.splice(index, 1);

                    // Update urutan otomatis
                    listProses.forEach((item, i) => {
                        item.urutan = i + 1;
                    });

                    renderTable();

                    Swal.fire(
                        'Dihapus!',
                        'Proses telah dihapus.',
                        'success'
                    );
                }
            });
        }

        // Reset modal saat ditutup
        function resetModal() {
            $('#namaPekerjaan').val('');
            $('#prosesSelect').val('');
            listProses = [];
            renderTable();
        }

        let editListProses = [];

        // Saat klik tombol edit
        $(document).on('click', '.edit-kerjaan', function() {
            let id = $(this).data('id');

            // Ambil data kerjaan + proses dari server
            $.get('/kerjaan/edit/' + id, function(data) {
                $('#editKerjaanId').val(data.id);
                $('#editNamaPekerjaan').val(data.nama_kerjaan);

                // Isi list proses
                editListProses = data.proses.map((item, index) => {
                    return {
                        id: item.list_proses_id,
                        proses: item.proses,
                        urutan: index + 1
                    };
                });
                renderEditTable();

                // Tampilkan modal
                $('#editPekerjaanModal').modal('show');
            });
        });

        // Close modal
        $('#closeModalEditFooterBtn, #closeModalEditFooterBtn').click(function() {
            $('#editPekerjaanModal').modal('hide');
            // resetModal();
        });

        // Tambahkan proses ke list edit
        $('#editAddProsesBtn').click(function() {
            let selectedOption = $('#editProsesSelect option:selected');
            let prosesId = selectedOption.data('id');
            let prosesText = selectedOption.text();

            if (prosesId) {
                let urutan = editListProses.length + 1;
                editListProses.push({
                    id: prosesId,
                    proses: prosesText,
                    urutan: urutan
                });
                renderEditTable();
                $('#editProsesSelect').val('');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih proses terlebih dahulu!',
                    confirmButtonColor: '#3085d6',
                });
            }
        });

        // Render table proses edit
        function renderEditTable() {
            let html = '';
            editListProses.forEach((item, index) => {
                html += `
        <tr>
            <td>${item.urutan}</td>
            <td>${item.proses}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeEditProses(${index})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
        `;
            });
            $('#editListProsesTable').html(html);
        }

        // Hapus proses dari list edit
        function removeEditProses(index) {
            editListProses.splice(index, 1);
            // Reset urutan setelah hapus
            editListProses.forEach((item, idx) => {
                item.urutan = idx + 1;
            });
            renderEditTable();
        }

        // Simpan perubahan
        $('#saveEditPekerjaanBtn').click(function() {
            let id = $('#editKerjaanId').val();
            let formData = {
                nama_pekerjaan: $('#editNamaPekerjaan').val(),
                proses: editListProses,
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            };

            $.ajax({
                url: '/kerjaan/update/' + id,
                type: 'POST',
                data: formData,
                success: function(res) {
                    $('#editPekerjaanModal').modal('hide');
                    $('#kerjaanTable').DataTable().ajax.reload();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                },
                error: function() {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menyimpan perubahan.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    </script>

@endsection
