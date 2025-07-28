@extends('layout.app')
@section('title', 'Accounting')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary font-weight-bold">Jurnal</h3>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="container-fluid px-2 px-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">

                                    </div>
                                    <div class="card shadow-sm rounded-3">
                                        <div class="card-body p-3 p-md-4">
                                            <form id="formAccounting" enctype="multipart/form-data">
                                                @csrf

                                                {{-- Tanggal --}}
                                                <div class="mb-3">
                                                    <label for="tanggal" class="form-label">Tanggal</label>
                                                    <input type="datetime-local" name="tanggal" id="tanggal"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>
                                                </div>

                                                {{-- Tipe Jurnal --}}
                                                <div class="mb-3">
                                                    <label class="form-label d-block">Tipe Jurnal</label>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @php
                                                            $types = [
                                                                'M' => 'Modal',
                                                                'P' => 'Piutang',
                                                                'JU' => 'Jurnal Umum',
                                                                'JP' => 'Jurnal Proyek',
                                                            ];
                                                        @endphp
                                                        @foreach ($types as $value => $label)
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input tipe-jurnal" type="radio"
                                                                    name="tipe_jurnal" id="tipe_{{ $value }}"
                                                                    value="{{ $value }}" required>
                                                                <label class="form-check-label"
                                                                    for="tipe_{{ $value }}">{{ $label }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- No Jurnal --}}
                                                <div class="mb-3">
                                                    <label for="no_jurnal" class="form-label">No Jurnal</label>
                                                    <input type="text" name="no_jurnal" id="no_jurnal"
                                                        class="form-control form-control-sm" readonly required>
                                                </div>

                                                {{-- Deskripsi --}}
                                                <div class="mb-3">
                                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                                    <textarea name="deskripsi" id="deskripsi" class="form-control form-control-sm" rows="2"></textarea>
                                                </div>

                                                {{-- Total --}}
                                                <div class="mb-3">
                                                    <label for="total" class="form-label">Total</label>
                                                    <input type="number" name="total" id="total"
                                                        class="form-control form-control-sm" step="0.01" required>
                                                </div>

                                                {{-- Upload File Dinamis --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Upload File</label>
                                                    <div id="fileWrapper" class="d-flex flex-column gap-2">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="file_names[]" class="form-control"
                                                                placeholder="Nama File" required>
                                                            <input type="file" name="files[]" class="form-control"
                                                                required>
                                                            <button type="button"
                                                                class="btn btn-success addFileBtn">+</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol --}}
                                                <div class="d-grid gap-2 mt-4">
                                                    <button type="submit" class="btn btn-primary"
                                                        id="btnSave">Simpan</button>
                                                    <a href="{{ route('accounting.index') }}"
                                                        class="btn btn-secondary btn-sm">Kembali</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // Auto-generate nomor jurnal berdasarkan tipe
        $('.tipe-jurnal').on('change', function() {
            let tipe = $(this).val();
            if (tipe) {
                $.get('{{ route('accounting.generateNo') }}', {
                    tipe_jurnal: tipe
                }, function(res) {
                    $('#no_jurnal').val(res.no_jurnal);
                });
            }
        });

        // Add field upload file dinamis
        $(document).on('click', '.addFileBtn', function() {
            let html = `
            <div class="input-group mb-2">
                <input type="text" name="file_names[]" class="form-control" placeholder="Nama File" required>
                <input type="file" name="files[]" class="form-control" required>
                <button type="button" class="btn btn-danger removeFileBtn">-</button>
            </div>
        `;
            $('#fileWrapper').append(html);
        });

        // Remove field file
        $(document).on('click', '.removeFileBtn', function() {
            $(this).closest('.input-group').remove();
        });

        // Submit form pakai AJAX
        $('#formAccounting').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $('#btnSave').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '{{ route('accounting.store') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#btnSave').prop('disabled', false).text('Simpan');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data berhasil disimpan'
                    }).then(() => {
                        window.location.href = '{{ route('accounting.index') }}';
                    });
                },
                error: function(xhr) {
                    $('#btnSave').prop('disabled', false).text('Simpan');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorText = '';
                        $.each(errors, function(key, val) {
                            errorText += val[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: errorText
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                }
            });
        });
    </script>

@endsection
