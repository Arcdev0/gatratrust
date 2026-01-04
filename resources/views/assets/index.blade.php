@extends('layout.app')

@section('title', 'Asset')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold mb-0">Asset</h3>

            <button type="button" id="createAsset" class="btn btn-primary" data-toggle="modal" data-target="#modalCreateAsset">
                <i class="fas fa-plus"></i> Tambah Asset
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="assetTable">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>No Asset</th>
                                    <th>Nama</th>
                                    <th>Merek</th>
                                    <th>No Seri</th>
                                    <th>Lokasi</th>
                                    <th width="70">Jumlah</th>
                                    <th width="120">Harga</th>
                                    <th width="120">Total</th>
                                    <th width="90">Gambar</th>
                                    <th width="160">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================
    MODAL BARCODE
========================= --}}
    <div class="modal fade" id="modalBarcodeAsset" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <div class="font-weight-bold" id="barcodeAssetName">-</div>
                    <div class="text-muted small mb-3" id="barcodeAssetNo">-</div>

                    <div id="barcodeWrapper" class="border rounded p-3 mb-3">
                        <img id="barcodeImage" src="" alt="QR" class="img-fluid" style="display:none;">
                        <div id="barcodeEmpty" class="text-muted">QR belum tersedia</div>
                    </div>

                    <div class="d-flex justify-content-center" style="gap:8px;">
                        <a id="btnOpenScan" href="#" target="_blank" class="btn btn-dark btn-sm">
                            <i class="fas fa-link"></i> Scan
                        </a>

                        <a id="btnDownloadBarcode" href="#" download class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- =========================
    MODAL PLACEHOLDER
========================= --}}
    <div class="modal fade" id="modalCreateAsset" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="createAssetForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No Asset</label>
                                <input type="text" name="no_asset" id="no_asset" class="form-control" readonly required>
                                <small class="text-muted">Auto dari sistem</small>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merek</label>
                                <input type="text" name="merek" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Seri</label>
                                <input type="text" name="no_seri" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" value="1" min="1"
                                    required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" name="harga" class="form-control" value="0" min="0"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">URL Gambar (opsional)</label>
                                <input type="text" name="url_gambar" class="form-control" placeholder="https://...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Gambar (opsional)</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <small class="text-muted">Jika upload dipilih, URL Gambar akan ditimpa.</small>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveAsset">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEditAsset" tabindex="-1"></div>

@endsection

@section('script')
    <script>
        $(function() {

            // =====================
            // DATATABLE
            // =====================
            const table = $('#assetTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('assets.datatable') }}",
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_asset'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'merek'
                    },
                    {
                        data: 'no_seri'
                    },
                    {
                        data: 'lokasi'
                    },
                    {
                        data: 'jumlah'
                    },
                    {
                        data: 'harga',
                        searchable: false
                    },
                    {
                        data: 'total',
                        searchable: false
                    },
                    {
                        data: 'url_gambar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // =====================
            // BARCODE MODAL HANDLER
            // =====================
            $(document).on('click', '.btn-barcode-asset', function() {

                const nama = $(this).data('nama') || '-';
                const noAsset = $(this).data('no_asset') || '-';
                const scanUrl = $(this).data('scan_url') || '#';
                const barcodeUrl = $(this).data('url_barcode') || '';

                $('#barcodeAssetName').text(nama);
                $('#barcodeAssetNo').text(noAsset);
                $('#btnOpenScan').attr('href', scanUrl);

                // reset state
                $('#barcodeImage').hide().attr('src', '');
                $('#barcodeEmpty').show().text('QR belum tersedia');
                $('#btnDownloadBarcode').hide().attr('href', '#');

                if (!barcodeUrl || barcodeUrl === 'null' || barcodeUrl === 'undefined') {
                    return;
                }

                // cache bust biar QR baru langsung kebaca
                const src = barcodeUrl + (barcodeUrl.includes('?') ? '&' : '?') + 'v=' + Date.now();

                $('#barcodeImage')
                    .off('error') // biar ga dobel handler
                    .on('error', function() {
                        $(this).hide().attr('src', '');
                        $('#barcodeEmpty').show().text('QR gagal dimuat (file tidak ditemukan)');
                        $('#btnDownloadBarcode').hide();
                    })
                    .attr('src', src)
                    .show();

                $('#barcodeEmpty').hide();

                $('#btnDownloadBarcode')
                    .attr('href', barcodeUrl)
                    .attr('download', 'QR-' + noAsset)
                    .show();
            });



        });

        $('#createAsset').on('click', function() {
            // reset form
            const form = document.getElementById('createAssetForm');
            if (form) form.reset();

            // isi no_asset otomatis (butuh route assets.nextNo)
            $.get("{{ route('assets.nextNo') }}")
                .done(function(res) {
                    if (res.status) {
                        $('#no_asset').val(res.no_asset);
                    }
                })
                .fail(function() {
                    // fallback: biar bisa input manual kalau endpoint error
                    $('#no_asset').prop('readonly', false);
                });
        });


        $(document).on('submit', '#createAssetForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const formData = new FormData(this);

            // disable button biar gak double submit
            const $btn = $('#btnSaveAsset');
            $btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('assets.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // reload datatable
                        $('#assetTable').DataTable().ajax.reload(null, false);

                        // reset form
                        $form[0].reset();

                        // tutup modal
                        const modalEl = document.getElementById('modalCreateAsset');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan.'
                        });
                    }
                },
                error: function(xhr) {
                    // Laravel validation error -> 422
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors || {};
                        let msg = '';
                        Object.keys(errors).forEach(function(key) {
                            msg += `• ${errors[key][0]}\n`;
                        });

                        Swal.fire({
                            icon: 'warning',
                            title: 'Validasi gagal',
                            text: msg ? msg : 'Periksa input.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Server error. Coba lagi.'
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                }
            });
        });
    </script>

@endsection
