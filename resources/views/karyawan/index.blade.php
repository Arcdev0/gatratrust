@extends('layout.app')
@section('title', 'Karyawan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Karyawan</h3>
                    <a href="{{ route('karyawan.create') }}" class="btn btn-success">
                        Tambah Karyawan
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-bordered" id="karyawanTable" style="width:100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No. Karyawan</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="showKaryawanModal" tabindex="-1" aria-labelledby="showKaryawanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showKaryawanLabel">Detail Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalKaryawanBody">
                    <!-- Konten dari AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            let table = $('#karyawanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('karyawan.data') }}',
                columns: [{
                        data: 'no_karyawan',
                        name: 'no_karyawan'
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                let form = $(this).closest('form');
                let actionUrl = form.attr('action'); // URL delete
                let formData = form.serialize(); // Ambil token & method

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data ini akan hilang permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: actionUrl,
                            type: 'POST',
                            data: formData,
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null,
                                    false);
                            },
                            error: function(err) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data'
                                });
                            }
                        });
                    }
                });
            });
        });


        function showKaryawan(id) {
            $.get('karyawan/show/' + id, function(res) {
                // Foto
                let fotoPreview = `
            <div class="border rounded p-2 d-flex align-items-center justify-content-center" style="min-height:250px;">
        `;
                if (res.foto) {
                    if (res.foto_ext === 'pdf') {
                        fotoPreview +=
                            `<a href="${res.foto}" target="_blank" class="text-primary">Lihat Foto (PDF)</a>`;
                    } else {
                        fotoPreview +=
                            `<img src="${res.foto}" class="img-fluid rounded shadow" style="max-height:240px;">`;
                    }
                } else {
                    fotoPreview += `<p class="text-muted text-center mb-0">Tidak ada foto</p>`;
                }
                fotoPreview += `</div>`;

                // Sertifikat Inhouse
                let sertifikatInhouseHTML = '';
                if (res.sertifikat_inhouse.length > 0) {
                    res.sertifikat_inhouse.forEach(s => {
                        if (s.file) {
                            if (s.ext === 'pdf') {
                                sertifikatInhouseHTML += `
                            <div class="mb-2">
                                <strong style="font-size:1.05rem;">${s.nama_sertifikat}</strong>
                                &nbsp;—&nbsp;
                                <a href="${s.file}" target="_blank" class="text-primary">Lihat PDF</a>
                            </div>`;
                            } else {
                                sertifikatInhouseHTML += `
                            <div class="mb-2">
                                <strong style="font-size:1.05rem;">${s.nama_sertifikat}</strong><br>
                                <img src="${s.file}" class="img-fluid border rounded p-1" style="max-height:120px;">
                            </div>`;
                            }
                        }
                    });
                } else {
                    sertifikatInhouseHTML = `<p class="text-muted">Tidak ada sertifikat inhouse</p>`;
                }

                // Sertifikat External
                let sertifikatExternalHTML = '';
                if (res.sertifikat_external.length > 0) {
                    res.sertifikat_external.forEach(s => {
                        if (s.file) {
                            if (s.ext === 'pdf') {
                                sertifikatExternalHTML += `
                            <div class="mb-2">
                                <strong style="font-size:1.05rem;">${s.nama_sertifikat}</strong>
                                &nbsp;—&nbsp;
                                <a href="${s.file}" target="_blank" class="text-primary">Lihat PDF</a>
                            </div>`;
                            } else {
                                sertifikatExternalHTML += `
                            <div class="mb-2">
                                <strong style="font-size:1.05rem;">${s.nama_sertifikat}</strong><br>
                                <img src="${s.file}" class="img-fluid border rounded p-1" style="max-height:120px;">
                            </div>`;
                            }
                        }
                    });
                } else {
                    sertifikatExternalHTML = `<p class="text-muted">Tidak ada sertifikat external</p>`;
                }

                // Isi modal
                $('#modalKaryawanBody').html(`
            <div class="row">
                <!-- Kolom kiri -->
                <div class="col-md-6">
                    <p><strong>No Karyawan:</strong> ${res.no_karyawan}</p>
                    <p><strong>Nama Lengkap:</strong> ${res.nama_lengkap}</p>
                    <p><strong>Jenis Kelamin:</strong> ${res.jenis_kelamin}</p>
                    <p><strong>Tempat, Tanggal Lahir:</strong> ${res.tempat_lahir}, ${res.tanggal_lahir}</p>
                    <p><strong>Alamat:</strong> ${res.alamat_lengkap}</p>
                    <p><strong>Jabatan:</strong> ${res.jabatan}</p>
                    <p><strong>Status:</strong> ${res.status}</p>
                    <p><strong>Nomor Telepon:</strong> ${res.nomor_telepon ?? '-'}</p>
                    <p><strong>Email:</strong> ${res.email ?? '-'}</p>
                </div>

                <!-- Kolom kanan: Foto -->
                <div class="col-md-6 text-center">
                    ${fotoPreview}
                </div>
            </div>

            <hr>
            <h6>Sertifikat Inhouse:</h6>
            ${sertifikatInhouseHTML}
            <hr>
            <h6>Sertifikat External:</h6>
            ${sertifikatExternalHTML}
        `);

                $('#showKaryawanModal').modal('show');
            });
        }
    </script>
@endsection
