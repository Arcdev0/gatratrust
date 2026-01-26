@extends('layout.app')

@section('title', 'Buat FPU')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0 text-primary font-weight-bold">Buat FPU</h3>
                <div class="text-muted small">Form Pengajuan Uang</div>
            </div>

            <div class="d-flex">
                <a href="{{ route('fpus.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>

                {{-- SAVE DRAFT --}}
                <button type="button" class="btn btn-outline-primary mr-2" id="btnSaveDraft">
                    <i class="fas fa-save mr-1"></i>Save Draft
                </button>

                {{-- SUBMIT --}}
                <button type="button" class="btn btn-success" id="btnSubmitFpu">
                    <i class="fas fa-paper-plane mr-1"></i>Submit
                </button>
            </div>
        </div>


        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-8">

                {{-- HEADER --}}
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Header</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Tanggal *</label>
                                <input type="date" class="form-control" id="request_date"
                                    value="{{ now()->toDateString() }}">
                                <div class="text-danger small d-none" id="err_request_date">Tanggal wajib diisi</div>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">No Project (Opsional)</label>
                                <select class="form-control" id="project_id" style="width:100%;">
                                    <option value="">-- pilih project (opsional) --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_project }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Ketik No Project untuk mencari. Jika project dipilih, item otomatis ditarik dari PAK
                                    (kategori A/B/C).
                                </small>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Requester (Opsional)</label>
                                <input type="text" class="form-control" id="requester_name" placeholder="Nama pengaju">
                                <small class="text-muted">Jika kosong, otomatis pakai user login.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold">Purpose</label>
                                <select class="form-control" id="purpose" style="width:100%;">
                                    <option value="">-- pilih --</option>
                                    <option value="tagihan_rutin">Tagihan Rutin</option>
                                    <option value="pembelian_material">Pembelian Material</option>
                                    <option value="akomodasi_operasional">Akomodasi Operasional</option>
                                    <option value="bayar_vendor">Bayar Vendor</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group col-md-8">
                                <label class="font-weight-semibold">Notes / Deskripsi</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Tulis deskripsi / alasan pengajuan..."></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- LINES --}}
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Detail Pengeluaran</strong>
                            <div class="text-muted small" id="pakInfoHint">-</div>
                        </div>
                        <div class="d-flex">
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2" id="btnClearLines">
                                <i class="fas fa-eraser mr-1"></i>Reset Lines
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddLine">
                                <i class="fas fa-plus mr-1"></i>Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="linesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th>Description *</th>
                                        <th style="width:180px;" class="text-right">Amount *</th>
                                        <th style="width:90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="linesBody">
                                    {{-- rows by JS --}}
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="2" class="text-right">TOTAL</th>
                                        <th class="text-right" id="grandTotal">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="p-3">
                            <small class="text-muted">
                                * Minimal 1 baris. Amount harus &gt; 0.00.
                                Jika project dipilih, lines akan <b>replace</b> dari PAK (kategori A/B/C), tetapi kamu masih
                                bisa tambah manual.
                            </small>
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Info Flow</strong>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Finance akan <b>Approve</b> + pilih wallet.</li>
                            <li>Setelah approved, upload bukti dilakukan per line.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Tips</strong>
                    </div>
                    <div class="card-body">
                        <div class="text-muted small">
                            Description gunakan format jelas: contoh "VENDOR - LAB HI-TEST", "TRANSPORT - TOL", dll.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // =========================
            // SELECT2: PURPOSE
            // =========================
            $('#purpose').select2({
                width: '100%',
                placeholder: '-- pilih --',
                allowClear: true
            });

            // =========================
            // SELECT2: PROJECT (AJAX)
            // =========================
            $('#project_id').select2({
                width: '100%',
                placeholder: 'Pilih No Project',
                allowClear: true
            });


            // =========================
            // HELPERS
            // =========================
            function moneyFormat(n) {
                n = parseFloat(n || 0);
                return n.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function renumberLines() {
                $('#linesBody tr').each(function(i) {
                    $(this).find('.line-no').text(i + 1);
                });
            }

            function calcTotal() {
                let total = 0;
                $('.line-amount').each(function() {
                    const val = parseFloat($(this).val() || 0);
                    total += val;
                });
                $('#grandTotal').text(moneyFormat(total));
                return total;
            }

            function addLineRow(desc = '', amt = '') {
                const row = `
            <tr>
                <td class="text-center align-middle line-no">1</td>
                <td>
                    <input type="text" class="form-control line-desc" placeholder="contoh: VENDOR - LAB HI-TEST" value="${escapeHtml(desc)}">
                    <div class="text-danger small d-none err-line-desc">Description wajib diisi</div>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control text-right line-amount" placeholder="0.00" value="${amt}">
                    <div class="text-danger small d-none err-line-amount">Amount wajib &gt; 0</div>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger btnRemoveLine">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
                $('#linesBody').append(row);
                renumberLines();
                calcTotal();
            }

            function replaceLines(lines) {
                $('#linesBody').html('');
                if (!lines || lines.length === 0) {
                    addLineRow();
                    return;
                }
                lines.forEach(function(l) {
                    addLineRow(l.description || '', l.amount || '');
                });
                renumberLines();
                calcTotal();
            }

            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            // init 1 line
            addLineRow();

            // add line
            $('#btnAddLine').on('click', function() {
                addLineRow();
            });

            // remove line
            $(document).on('click', '.btnRemoveLine', function() {
                $(this).closest('tr').remove();
                if ($('#linesBody tr').length === 0) addLineRow();
                renumberLines();
                calcTotal();
            });

            // recalc on change
            $(document).on('keyup change', '.line-amount', function() {
                calcTotal();
            });

            // reset lines manual
            $('#btnClearLines').on('click', function() {
                replaceLines([]);
                $('#pakInfoHint').text('-');
            });

            // =========================
            // ON PROJECT SELECTED -> LOAD PAK ITEMS
            // =========================
            $('#project_id').on('change', function() {
                const projectId = $(this).val();
                if (!projectId) {
                    $('#pakInfoHint').text('-');
                    return;
                }

                $('#pakInfoHint').text('Mengambil item dari PAK...');

                $.ajax({
                    url: "{{ route('fpus.ajax.project.pak-items', ['project' => '___ID___']) }}"
                        .replace('___ID___', projectId),
                    method: 'GET',
                    success: function(res) {
                        if (!res.success) {
                            $('#pakInfoHint').text('-');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'Gagal ambil PAK items'
                            });
                            return;
                        }

                        if (!res.has_pak || !res.items || res.items.length === 0) {
                            $('#pakInfoHint').text(
                                'Project dipilih, tetapi PAK / item kategori A,B,C tidak ditemukan (manual tetap bisa).'
                            );
                            Swal.fire({
                                icon: 'warning',
                                title: 'PAK tidak ditemukan',
                                text: 'Project ini belum memiliki PAK / item kategori A,B,C. Kamu tetap bisa input manual.'
                            });
                            return;
                        }

                        // Replace ALL lines from PAK items
                        replaceLines(res.items);

                        $('#pakInfoHint').text(
                            `Lines di-generate dari PAK (kategori A,B,C): ${res.items.length} item.`
                        );
                    },
                    error: function(xhr) {
                        $('#pakInfoHint').text('-');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal ambil PAK items'
                        });
                    }
                });
            });

            // =========================
            // VALIDATION
            // =========================
            function validateForm() {
                let ok = true;

                // reset error
                $('#err_request_date').addClass('d-none');
                $('.err-line-desc, .err-line-amount').addClass('d-none');

                const requestDate = $('#request_date').val();
                if (!requestDate) {
                    $('#err_request_date').removeClass('d-none');
                    ok = false;
                }

                const rows = $('#linesBody tr');
                if (rows.length < 1) {
                    ok = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimal 1 baris detail'
                    });
                    return false;
                }

                rows.each(function() {
                    const desc = $(this).find('.line-desc').val().trim();
                    const amt = parseFloat($(this).find('.line-amount').val() || 0);

                    if (!desc) {
                        $(this).find('.err-line-desc').removeClass('d-none');
                        ok = false;
                    }
                    if (!amt || amt <= 0) {
                        $(this).find('.err-line-amount').removeClass('d-none');
                        ok = false;
                    }
                });

                return ok;
            }

            // =========================
            // SAVE (AJAX)
            // =========================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btnSaveDraft').on('click', function() {
                submitFpu('draft');
            });

            $('#btnSubmitFpu').on('click', function() {
                submitFpu('submit');
            });

            function submitFpu(action) {
                if (!validateForm()) return;

                const lines = [];
                $('#linesBody tr').each(function() {
                    lines.push({
                        description: ($(this).find('.line-desc').val() || '').trim(),
                        amount: $(this).find('.line-amount').val()
                    });
                });

                // teks konfirmasi dinamis
                const isSubmit = action === 'submit';
                const confirmTitle = isSubmit ? 'Submit FPU?' : 'Simpan Draft FPU?';
                const confirmText = isSubmit ?
                    'FPU akan disubmit dan tidak bisa diedit lagi (kecuali ditolak/direset sesuai flow).' :
                    'FPU akan tersimpan sebagai draft dan masih bisa diedit.';

                Swal.fire({
                    title: confirmTitle,
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: isSubmit ? 'Ya, submit' : 'Ya, simpan draft',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: "{{ route('fpus.store') }}",
                        method: "POST",
                        data: {
                            action: action,
                            project_id: $('#project_id').val(),
                            request_date: $('#request_date').val(),
                            requester_name: $('#requester_name').val(),
                            purpose: $('#purpose').val(),
                            notes: $('#notes').val(),
                            lines: lines
                        },
                        success: function(res) {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: res.message || (isSubmit ?
                                    'FPU berhasil disubmit' :
                                    'FPU berhasil disimpan'),
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('fpus.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                });
            }

        });
    </script>
@endsection
