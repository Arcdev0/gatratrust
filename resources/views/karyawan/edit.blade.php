@extends('layout.app')
@section('title', 'Edit Karyawan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 class="text-primary font-weight-bold mb-3">Edit Karyawan</h3>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- No Karyawan --}}
                            <div class="mb-3">
                                <label for="no_karyawan" class="form-label">No. Karyawan</label>
                                <input type="text" name="no_karyawan" id="no_karyawan" class="form-control"
                                    value="{{ old('no_karyawan', $karyawan->no_karyawan) }}" readonly required>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                                    value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="Laki-laki"
                                        {{ $karyawan->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan"
                                        {{ $karyawan->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            {{-- Tempat & Tanggal Lahir --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                        value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}" required>
                                </div>
                                <div class="col">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                        value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir) }}" required>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="mb-3">
                                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" rows="3" required>{{ old('alamat_lengkap', $karyawan->alamat_lengkap) }}</textarea>
                            </div>

                            {{-- Jabatan --}}
                            <div class="mb-3">
                                <label for="jabatan_id" class="form-label">Jabatan</label>
                                <select name="jabatan_id" id="jabatan_id" class="form-control" required>
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->id }}"
                                            {{ $j->id == $karyawan->jabatan_id ? 'selected' : '' }}>
                                            {{ $j->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $karyawan->status == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $karyawan->status == 0 ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>

                            {{-- Nomor Telepon & Email --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                    <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control"
                                        value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}">
                                </div>
                                <div class="col">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email', $karyawan->email) }}">
                                </div>
                            </div>

                            {{-- Nomor Identitas --}}
                            <div class="mb-3">
                                <label for="nomor_identitas" class="form-label">Nomor Identitas (KTP)</label>
                                <input type="text" name="nomor_identitas" id="nomor_identitas" class="form-control"
                                    value="{{ old('nomor_identitas', $karyawan->nomor_identitas) }}">
                            </div>

                            {{-- Status Perkawinan --}}
                            <div class="mb-3">
                                <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                                <select name="status_perkawinan" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="Belum Kawin"
                                        {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Belum Kawin' ? 'selected' : '' }}>
                                        Belum Kawin</option>
                                    <option value="Kawin"
                                        {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Kawin' ? 'selected' : '' }}>
                                        Kawin</option>
                                    <option value="Duda"
                                        {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Duda' ? 'selected' : '' }}>
                                        Duda</option>
                                    <option value="Janda"
                                        {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Janda' ? 'selected' : '' }}>
                                        Janda</option>
                                </select>
                            </div>

                            {{-- Kewarganegaraan & Agama --}}
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="kewarganegaraan" class="form-label">Kewarganegaraan</label>
                                    <select name="kewarganegaraan" class="form-control">
                                        <option value="WNI"
                                            {{ old('kewarganegaraan', $karyawan->kewarganegaraan) == 'WNI' ? 'selected' : '' }}>
                                            WNI</option>
                                        <option value="WNA"
                                            {{ old('kewarganegaraan', $karyawan->kewarganegaraan) == 'WNA' ? 'selected' : '' }}>
                                            WNA</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="agama" class="form-label">Agama</label>
                                    <select name="agama" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <option value="Islam"
                                            {{ old('agama', $karyawan->agama) == 'Islam' ? 'selected' : '' }}>Islam
                                        </option>
                                        <option value="Kristen"
                                            {{ old('agama', $karyawan->agama) == 'Kristen' ? 'selected' : '' }}>Kristen
                                        </option>
                                        <option value="Katolik"
                                            {{ old('agama', $karyawan->agama) == 'Katolik' ? 'selected' : '' }}>Katolik
                                        </option>
                                        <option value="Hindu"
                                            {{ old('agama', $karyawan->agama) == 'Hindu' ? 'selected' : '' }}>Hindu
                                        </option>
                                        <option value="Buddha"
                                            {{ old('agama', $karyawan->agama) == 'Buddha' ? 'selected' : '' }}>Buddha
                                        </option>
                                        <option value="Konghucu"
                                            {{ old('agama', $karyawan->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- DOH --}}
                            <div class="mb-3">
                                <label for="doh" class="form-label">DOH (Date Of Hire)</label>
                                <input type="date" name="doh" id="doh" class="form-control"
                                    value="{{ old('doh', $karyawan->doh ? \Carbon\Carbon::parse($karyawan->doh)->format('Y-m-d') : '') }}">
                            </div>


                            {{-- Foto --}}
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label><br>
                                @if ($karyawan->foto)
                                    <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto Karyawan"
                                        width="100" class="mb-2"><br>
                                @endif
                                <input type="file" name="foto" id="foto" class="form-control"
                                    accept="image/*">
                            </div>

                            {{-- Sertifikat Inhouse --}}
                            <hr>
                            <h5 class="text-primary">Sertifikat Inhouse</h5>
                            <div id="inhouse-wrapper">
                                @foreach ($karyawan->sertifikatInhouse as $i => $sertifikat)
                                    <div class="row mb-2 inhouse-item">
                                        <div class="col">
                                            <input type="text"
                                                name="sertifikat_inhouse[{{ $i }}][nama_sertifikat]"
                                                value="{{ $sertifikat->nama_sertifikat }}" class="form-control">
                                        </div>
                                        <div class="col">
                                            <input type="file"
                                                name="sertifikat_inhouse[{{ $i }}][file_sertifikat]"
                                                class="form-control">
                                            @if ($sertifikat->file_sertifikat)
                                                <small><a href="{{ asset('storage/' . $sertifikat->file_sertifikat) }}"
                                                        target="_blank">Lihat File</a></small>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger remove-inhouse">-</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Sertifikat External --}}
                            <hr>
                            <h5 class="text-primary">Sertifikat External</h5>
                            <div id="external-wrapper">
                                @foreach ($karyawan->sertifikatExternal as $i => $sertifikat)
                                    <div class="row mb-2 external-item">
                                        <div class="col">
                                            <input type="text"
                                                name="sertifikat_external[{{ $i }}][nama_sertifikat]"
                                                value="{{ $sertifikat->nama_sertifikat }}" class="form-control">
                                        </div>
                                        <div class="col">
                                            <input type="file"
                                                name="sertifikat_external[{{ $i }}][file_sertifikat]"
                                                class="form-control">
                                            @if ($sertifikat->file_sertifikat)
                                                <small><a href="{{ asset('storage/' . $sertifikat->file_sertifikat) }}"
                                                        target="_blank">Lihat File</a></small>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger remove-external">-</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update</button>
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
        // Set awal index dari jumlah sertifikat yang sudah ada
        let inhouseIndex = {{ $karyawan->sertifikatInhouse->count() }};
        let externalIndex = {{ $karyawan->sertifikatExternal->count() }};
        let syaratData = [];

        // --- Tambah & Hapus Sertifikat Inhouse ---
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
            checkSyaratCompletion();
        });

        $(document).on('click', '.remove-inhouse', function() {
            $(this).closest('.inhouse-item').remove();
            checkSyaratCompletion();
        });

        // --- Tambah & Hapus Sertifikat External ---
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
            checkSyaratCompletion();
        });

        $(document).on('click', '.remove-external', function() {
            $(this).closest('.external-item').remove();
            checkSyaratCompletion();
        });

        // --- Cek Syarat Jabatan ---
        function checkSyaratCompletion() {
            let completed = 0;
            $('#syarat-list li').each(function() {
                let syaratText = $(this).data('syarat');
                let matchFound = false;

                $('input[name^="sertifikat_inhouse"]').each(function() {
                    if ($(this).val().trim().toLowerCase() === syaratText.toLowerCase()) {
                        matchFound = true;
                    }
                });

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

            if (completed === syaratData.length && syaratData.length > 0) {
                $('#syarat-box .alert').removeClass('alert-danger').addClass('alert-success');
            } else {
                $('#syarat-box .alert').removeClass('alert-success').addClass('alert-danger');
            }
        }

        // --- Load Syarat saat Jabatan berubah ---
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

        // --- Prefill Syarat saat halaman load ---
        $(document).ready(function() {
            let initialJabatan = $('#jabatan_id').val();
            if (initialJabatan) {
                $('#jabatan_id').trigger('change');
            }
        });

        // --- Event cek ulang saat input sertifikat berubah ---
        $(document).on('input', 'input[name^="sertifikat_inhouse"], input[name^="sertifikat_external"]', function() {
            checkSyaratCompletion();
        });

        // --- Submit AJAX Update ---
        $('form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this)[0];
            let formData = new FormData(form);

            Swal.fire({
                title: 'Menyimpan Perubahan...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data karyawan berhasil diperbarui'
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
