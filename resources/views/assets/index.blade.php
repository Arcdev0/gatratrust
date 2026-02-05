@extends('layout.app')

@section('title', 'Asset')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold mb-0">Asset</h3>


            <div>
                <button type="button" id="createAsset" class="btn btn-primary" data-toggle="modal"
                    data-target="#modalCreateAsset">
                    <i class="fas fa-plus"></i> Tambah Asset
                </button>
                <button type="button" class="btn btn-success" id="btnExportExcel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>



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

                                    <th width="120">Faktur</th>
                                    <th width="90">Tahun</th>
                                    <th width="130">Remark</th>

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
                                <label class="form-label">Upload Faktur Pembelian (opsional)</label>
                                <input type="file" name="faktur_pembelian" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF/JPG/PNG, max 4MB</small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tahun Dibeli (opsional)</label>
                                <input type="number" name="tahun_dibeli" class="form-control" min="1990"
                                    max="{{ date('Y') }}" placeholder="2025">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Remark (opsional)</label>
                                <select name="remark" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <option value="baik">Baik</option>
                                    <option value="perlu_perbaikan">Perlu Perbaikan</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>


                            {{-- <div class="col-md-6 mb-3">
                                <label class="form-label">URL Gambar (opsional)</label>
                                <input type="text" name="url_gambar" class="form-control" placeholder="https://...">
                            </div> --}}

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


    <div class="modal fade" id="modalEditAsset" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="editAssetForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="edit_id" name="id">

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">No Asset</label>
                                <input type="text" name="no_asset" id="edit_no_asset" class="form-control" readonly
                                    required>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merek</label>
                                <input type="text" name="merek" id="edit_merek" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Seri</label>
                                <input type="text" name="no_seri" id="edit_no_seri" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" id="edit_lokasi" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" id="edit_jumlah" class="form-control"
                                    min="1" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" name="harga" id="edit_harga" class="form-control"
                                    min="0" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Faktur Pembelian (opsional)</label>
                                <input type="file" name="faktur_pembelian" id="edit_faktur_pembelian"
                                    class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Jika upload baru, faktur lama akan diganti.</small>

                                <div class="mt-2">
                                    <a href="#" target="_blank" id="edit_faktur_link" style="display:none;">
                                        <i class="fas fa-file-alt"></i> Lihat Faktur Lama
                                    </a>
                                    <div id="edit_faktur_empty" class="text-muted" style="display:none;">Tidak ada faktur
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tahun Dibeli</label>
                                <input type="number" name="tahun_dibeli" id="edit_tahun_dibeli" class="form-control"
                                    min="1990" max="{{ date('Y') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Remark</label>
                                <select name="remark" id="edit_remark" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <option value="baik">Baik</option>
                                    <option value="perlu_perbaikan">Perlu Perbaikan</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>


                            {{-- <div class="col-md-6 mb-3">
                                <label class="form-label">URL Gambar (opsional)</label>
                                <input type="text" name="url_gambar" id="edit_url_gambar" class="form-control"
                                    placeholder="https://...">
                                <small class="text-muted">Jika upload dipilih, URL akan ditimpa.</small>
                            </div> --}}

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Gambar (opsional)</label>
                                <input type="file" name="gambar" id="edit_gambar" class="form-control"
                                    accept="image/*">
                            </div>

                            <div class="col-12">
                                <div class="border rounded p-2 text-center">
                                    <div class="small text-muted mb-2">Preview</div>
                                    <img id="edit_preview_img" src="" alt="Preview"
                                        style="max-height:180px; display:none; border-radius:8px; border:1px solid #eee;">
                                    <div id="edit_preview_empty" class="text-muted">Tidak ada gambar</div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateAsset">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


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

                    // ✅ kolom baru
                    {
                        data: 'faktur_pembelian',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tahun_dibeli',
                        searchable: false
                    },
                    {
                        data: 'remark',
                        orderable: false,
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

                if (!barcodeUrl || barcodeUrl === 'null' || barcodeUrl === 'undefined') return;

                // cache bust
                const src = barcodeUrl + (barcodeUrl.includes('?') ? '&' : '?') + 'v=' + Date.now();

                $('#barcodeImage')
                    .off('error')
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

            // =====================
            // CREATE MODAL OPEN -> GET next no_asset
            // =====================
            $('#createAsset').on('click', function() {
                const form = document.getElementById('createAssetForm');
                if (form) form.reset();

                // no_asset auto
                $.get("{{ route('assets.nextNo') }}")
                    .done(function(res) {
                        if (res.status) {
                            $('#no_asset').val(res.no_asset).prop('readonly', true);
                        }
                    })
                    .fail(function() {
                        $('#no_asset').prop('readonly', false);
                    });
            });

            // =====================
            // CREATE SUBMIT (AJAX)
            // =====================
            $(document).on('submit', '#createAssetForm', function(e) {
                e.preventDefault();

                const $form = $(this);
                const formData = new FormData(this);

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

                            $('#assetTable').DataTable().ajax.reload(null, false);
                            $form[0].reset();

                            // ✅ Bootstrap 4 close modal
                            $('#modalCreateAsset').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan.'
                            });
                        }
                    },

                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            let msg = '';
                            Object.keys(errors).forEach(function(key) {
                                msg += `• ${errors[key][0]}\n`;
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi gagal',
                                html: msg ? msg.replace(/\n/g, '<br>') :
                                    'Periksa input.'
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

            // =====================
            // EDIT CLICK -> fill modal
            // =====================
            $(document).on('click', '.btn-edit-asset', function() {
                const id = $(this).data('id');
                const no_asset = $(this).data('no_asset') || '';
                const nama = $(this).data('nama') || '';
                const merek = $(this).data('merek') || '';
                const no_seri = $(this).data('no_seri') || '';
                const lokasi = $(this).data('lokasi') || '';
                const jumlah = $(this).data('jumlah') || 1;
                const harga = $(this).data('harga') || 0;
                const url_gambar = $(this).data('url_gambar') || '';

                // ✅ field baru
                const tahun = $(this).data('tahun_dibeli') || '';
                const remark = $(this).data('remark') || '';
                const fakturUrl = $(this).data('faktur_pembelian') || '';

                $('#edit_id').val(id);
                $('#edit_no_asset').val(no_asset);
                $('#edit_nama').val(nama);
                $('#edit_merek').val(merek);
                $('#edit_no_seri').val(no_seri);
                $('#edit_lokasi').val(lokasi);
                $('#edit_jumlah').val(jumlah);
                $('#edit_harga').val(harga);

                // ❌ kamu tidak punya input edit_url_gambar (comment), jadi jangan set itu
                // $('#edit_url_gambar').val(url_gambar);

                // reset file input
                $('#edit_gambar').val('');
                $('#edit_faktur_pembelian').val('');

                // preview gambar
                if (url_gambar) {
                    $('#edit_preview_img').attr('src', url_gambar).show();
                    $('#edit_preview_empty').hide();
                } else {
                    $('#edit_preview_img').attr('src', '').hide();
                    $('#edit_preview_empty').show();
                }

                // set tahun + remark
                $('#edit_tahun_dibeli').val(tahun);
                $('#edit_remark').val(remark);

                // link faktur lama
                if (fakturUrl) {
                    $('#edit_faktur_link').attr('href', fakturUrl).show();
                    $('#edit_faktur_empty').hide();
                } else {
                    $('#edit_faktur_link').hide().attr('href', '#');
                    $('#edit_faktur_empty').show();
                }
            });

            // preview gambar saat pilih file
            $('#edit_gambar').on('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit_preview_img').attr('src', e.target.result).show();
                    $('#edit_preview_empty').hide();
                };
                reader.readAsDataURL(file);
            });

            // =====================
            // EDIT SUBMIT (AJAX)
            // =====================
            $(document).on('submit', '#editAssetForm', function(e) {
                e.preventDefault();

                const id = $('#edit_id').val();
                const formData = new FormData(this);
                const url = "{{ url('/assets') }}/" + id;

                const $btn = $('#btnUpdateAsset');
                $btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: url,
                    method: "POST", // _method=PUT
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
                                timer: 1300,
                                showConfirmButton: false
                            });

                            $('#modalEditAsset').modal('hide');
                            $('#assetTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan.'
                            });
                        }
                    },

                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            let msg = '';
                            Object.keys(errors).forEach(function(key) {
                                msg += `• ${errors[key][0]}\n`;
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi gagal',
                                html: msg ? msg.replace(/\n/g, '<br>') :
                                    'Periksa input.'
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
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
                    }
                });
            });

            // =====================
            // DELETE (SweetAlert + AJAX)
            // =====================
            $(document).on('click', '.btn-delete-asset', function() {
                const id = $(this).data('id');
                const label = $(this).data('label') || 'asset ini';

                if (!id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID asset tidak ditemukan.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Hapus Asset?',
                    html: `Yakin ingin menghapus <b>${label}</b>?<br><small class="text-muted">Data dan file terkait akan ikut terhapus.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ url('/assets') }}/" + id,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },

                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: res.message ||
                                        'Asset berhasil dihapus.',
                                    timer: 1200,
                                    showConfirmButton: false
                                });

                                $('#assetTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message ||
                                        'Gagal menghapus asset.'
                                });
                            }
                        },

                        error: function(xhr) {
                            let msg = 'Server error. Coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
                                .responseJSON.message;

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
                            });
                        }
                    });
                });
            });


            $('#btnExportExcel').on('click', function() {
                window.open("{{ route('assets.exportExcel') }}", '_blank');
            });

        });
    </script>

@endsection
