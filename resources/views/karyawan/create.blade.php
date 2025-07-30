@extends('layout.app')
@section('title', 'Tambah Karyawan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 class="text-primary font-weight-bold mb-3">Tambah Karyawan</h3>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('karyawan.store') }}" method="POST">
                            @csrf

                            {{-- No Karyawan --}}
                            <div class="mb-3">
                                <label for="no_karyawan" class="form-label">No. Karyawan</label>
                                <input type="text" name="no_karyawan" id="no_karyawan" class="form-control"
                                    value="{{ $noKaryawan }}" readonly required>

                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                                    value="{{ old('nama_lengkap') }}" required>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            {{-- Tempat & Tanggal Lahir --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                        required>
                                </div>
                                <div class="col">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                        required>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="mb-3">
                                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" rows="3" required></textarea>
                            </div>

                            {{-- Jabatan --}}
                            <div class="mb-3">
                                <label for="jabatan_id" class="form-label">Jabatan</label>
                                <select name="jabatan_id" id="jabatan_id" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>

                            {{-- Nomor Telepon & Email --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                    <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>

                            {{-- Nomor Identitas --}}
                            <div class="mb-3">
                                <label for="nomor_identitas" class="form-label">Nomor Identitas (KTP)</label>
                                <input type="text" name="nomor_identitas" id="nomor_identitas" class="form-control">
                            </div>

                            {{-- Status Perkawinan --}}
                            <div class="mb-3">
                                <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                                <select name="status_perkawinan" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Duda">Duda</option>
                                    <option value="Janda">Janda</option>
                                </select>
                            </div>

                            {{-- Kewarganegaraan & Agama --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="kewarganegaraan" class="form-label">Kewarganegaraan</label>
                                    <select name="kewarganegaraan" class="form-control">
                                        <option value="WNI">WNI</option>
                                        <option value="WNA">WNA</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="agama" class="form-label">Agama</label>
                                    <select name="agama" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Pekerjaan --}}
                            {{-- <div class="mb-3">
                                <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                            </div> --}}

                            {{-- DOH --}}
                            <div class="mb-3">
                                <label for="doh" class="form-label">DOH (Date Of Hire)</label>
                                <input type="date" name="doh" id="doh" class="form-control">
                            </div>

                            {{-- Foto --}}
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" name="foto" id="foto" class="form-control"
                                    accept="image/*">
                            </div>

                            <div id="syarat-box" class="mt-3" style="display:none;">
                                <div class="alert alert-danger">
                                    <strong>Syarat Jabatan:</strong>
                                    <ul id="syarat-list"></ul>
                                </div>
                            </div>

                            <hr>
                            <h5 class="text-primary">Sertifikat Inhouse</h5>
                            <div id="inhouse-wrapper">
                                <div class="row mb-2 inhouse-item">
                                    <div class="col">
                                        <input type="text" name="sertifikat_inhouse[0][nama_sertifikat]"
                                            class="form-control" placeholder="Nama Sertifikat">
                                    </div>
                                    <div class="col">
                                        <input type="file" name="sertifikat_inhouse[0][file_sertifikat]"
                                            class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-success add-inhouse">+</button>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="text-primary">Sertifikat External</h5>
                            <div id="external-wrapper">
                                <div class="row mb-2 external-item">
                                    <div class="col">
                                        <input type="text" name="sertifikat_external[0][nama_sertifikat]"
                                            class="form-control" placeholder="Nama Sertifikat">
                                    </div>
                                    <div class="col">
                                        <input type="file" name="sertifikat_external[0][file_sertifikat]"
                                            class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-success add-external">+</button>
                                    </div>
                                </div>
                            </div>


                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        let inhouseIndex = 1;
        let externalIndex = 1;

        $(document).on('click', '.add-inhouse', function() {
            $('#inhouse-wrapper').append(`
        <div class="row mb-2 inhouse-item">
            <div class="col">
                <input type="text" name="sertifikat_inhouse[${inhouseIndex}][nama_sertifikat]" class="form-control" placeholder="Nama Sertifikat">
            </div>
            <div class="col">
                <input type="file" name="sertifikat_inhouse[${inhouseIndex}][file_sertifikat]" class="form-control">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove-inhouse">-</button>
            </div>
        </div>
    `);
            inhouseIndex++;
        });

        $(document).on('click', '.remove-inhouse', function() {
            $(this).closest('.inhouse-item').remove();
        });

        $(document).on('click', '.add-external', function() {
            $('#external-wrapper').append(`
        <div class="row mb-2 external-item">
            <div class="col">
                <input type="text" name="sertifikat_external[${externalIndex}][nama_sertifikat]" class="form-control" placeholder="Nama Sertifikat">
            </div>
            <div class="col">
                <input type="file" name="sertifikat_external[${externalIndex}][file_sertifikat]" class="form-control">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove-external">-</button>
            </div>
        </div>
    `);
            externalIndex++;
        });

        $(document).on('click', '.remove-external', function() {
            $(this).closest('.external-item').remove();
        });

        let syaratData = [];

        function checkSyaratCompletion() {
            let completed = 0;

            $('#syarat-list li').each(function() {
                let syaratText = $(this).data('syarat');
                let matchFound = false;

                // Cek di sertifikat Inhouse
                $('input[name^="sertifikat_inhouse"]').each(function() {
                    if ($(this).val().trim().toLowerCase() === syaratText.toLowerCase()) {
                        matchFound = true;
                    }
                });

                // Cek di sertifikat External
                $('input[name^="sertifikat_external"]').each(function() {
                    if ($(this).val().trim().toLowerCase() === syaratText.toLowerCase()) {
                        matchFound = true;
                    }
                });

                if (matchFound) {
                    $(this).css("text-decoration", "line-through");
                    completed++;
                } else {
                    $(this).css("text-decoration", "none");
                }
            });

            // Jika semua syarat terpenuhi, ubah warna alert
            if (completed === syaratData.length && syaratData.length > 0) {
                $('#syarat-box .alert')
                    .removeClass('alert-danger')
                    .addClass('alert-success');
            } else {
                $('#syarat-box .alert')
                    .removeClass('alert-success')
                    .addClass('alert-danger');
            }
        }

        // Event ketika pilih jabatan
        $('#jabatan_id').on('change', function() {
            let jabatanId = $(this).val();
            if (jabatanId) {
                $.get(`/karyawan/jabatan/${jabatanId}/syarat`, function(data) {
                    syaratData = data;
                    $('#syarat-list').empty();
                    if (data.length > 0) {
                        data.forEach(function(s) {
                            $('#syarat-list').append(`<li data-syarat="${s}">${s}</li>`);
                        });
                        $('#syarat-box').show();
                    } else {
                        $('#syarat-list').html('<li>Tidak ada syarat</li>');
                        $('#syarat-box').show();
                    }
                    checkSyaratCompletion();
                });
            } else {
                $('#syarat-box').hide();
            }
        });

        // Event cek ulang setiap input sertifikat berubah
        $(document).on('input', 'input[name^="sertifikat_inhouse"], input[name^="sertifikat_external"]', function() {
            checkSyaratCompletion();
        });


        $('form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this)[0];
            let formData = new FormData(form);

            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false, // penting untuk FormData
                contentType: false, // penting untuk FormData
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data karyawan berhasil ditambahkan'
                    }).then(() => {
                        window.location.href = "{{ route('karyawan.index') }}";
                    });
                },
                error: function(xhr) {
                    let errMsg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errMsg
                    });
                }
            });
        });
    </script>
@endsection
