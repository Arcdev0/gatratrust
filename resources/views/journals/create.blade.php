@extends('layout.app')

@section('title', 'Create Journal')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold text-primary mb-0">Create Journal</h3>
                <div class="text-muted small">Buat jurnal umum / kas masuk / kas keluar (double-entry).</div>
            </div>
            <a href="{{ route('journals.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        <div class="row g-3">

            {{-- HEADER FORM --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>Journal Header</strong>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Journal Date*</label>
                            <input type="date" class="form-control" id="journal_date"
                                value="{{ now()->toDateString() }}">
                            <div class="text-danger small d-none" id="err_journal_date">Tanggal wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type*</label>
                            <select class="form-control" id="type">
                                <option value="general" selected>General</option>
                                <option value="cash_in">Cash In</option>
                                <option value="cash_out">Cash Out</option>
                            </select>
                            <div class="text-danger small d-none" id="err_type">Type wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" class="form-control" id="category" placeholder="ex: operational_expense">
                            <div class="text-muted small mt-1">Opsional. Untuk filter laporan.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reference No</label>
                            <input type="text" class="form-control" id="reference_no"
                                placeholder="ex: INV-001 / FPU-0003">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status*</label>
                            <select class="form-control" id="status">
                                <option value="draft" selected>Draft</option>
                                <option value="posted">Posted</option>
                                <option value="void">Void</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Memo</label>
                            <textarea class="form-control" id="memo" rows="4" placeholder="Catatan jurnal..."></textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- LINES --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <div>
                            <strong>Journal Lines</strong>
                            <div class="text-muted small">Minimal 2 baris. Total debit harus sama dengan total credit.</div>
                        </div>
                        <button class="btn btn-sm btn-primary" id="btnAddRow">
                            <i class="fas fa-plus me-1"></i>Add Row
                        </button>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered align-middle" id="linesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 280px;">COA*</th>
                                    <th>Description</th>
                                    <th style="width: 140px;">Debit</th>
                                    <th style="width: 140px;">Credit</th>
                                    <th style="width: 70px;">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- rows via JS --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th><span id="totalDebit">0</span></th>
                                    <th><span id="totalCredit">0</span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-secondary" id="balanceBadge">NOT BALANCED</span>
                                <span class="text-muted small ms-2" id="balanceHint">Pastikan debit = credit.</span>
                            </div>
                            <button class="btn btn-success" id="btnSave">
                                <i class="fas fa-save me-2"></i>Save Journal
                            </button>
                        </div>

                        <div class="text-danger small mt-3 d-none" id="err_lines">
                            Lines belum valid. Pastikan minimal 2 baris dan debit=credit.
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

            // Select2 untuk header type/status (optional)
            $('#type, #status').select2({
                width: '100%'
            });

            // --- Data COA untuk dropdown lines (dari controller create())
            const COA_OPTIONS = @json(
                $coaSelectable->map(fn($c) => [
                            'id' => $c->id,
                            'text' => $c->code_account_id . ' - ' . $c->name,
                        ])->values());

            function rupiahNumber(n) {
                n = parseFloat(n || 0);
                if (isNaN(n)) n = 0;
                return n.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function parseNumber(val) {
                if (val === null || val === undefined) return 0;
                val = ('' + val).replaceAll(',', '').trim();
                const n = parseFloat(val);
                return isNaN(n) ? 0 : n;
            }

            function makeRow(index) {
                return `
            <tr class="lineRow" data-index="${index}">
                <td>
                    <select class="form-control coaSelect" style="width:100%">
                        <option value="">-- pilih akun --</option>
                    </select>
                    <div class="text-danger small d-none errCoa">COA wajib dipilih</div>
                </td>
                <td>
                    <input type="text" class="form-control lineDesc" placeholder="Keterangan baris...">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control lineDebit" placeholder="0.00">
                    <div class="text-danger small d-none errDebitCredit">Isi debit atau credit (salah satu)</div>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control lineCredit" placeholder="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btnRemoveRow">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            }

            let rowCount = 0;

            function addRow(prefill = {}) {
                rowCount++;
                $('#linesTable tbody').append(makeRow(rowCount));

                const $row = $('#linesTable tbody tr:last');

                // init select2 + load options
                const $select = $row.find('.coaSelect');
                $select.select2({
                    width: '100%',
                    data: COA_OPTIONS,
                    placeholder: '-- pilih akun --'
                });

                if (prefill.coa_id) $select.val(prefill.coa_id).trigger('change');
                if (prefill.description) $row.find('.lineDesc').val(prefill.description);
                if (prefill.debit) $row.find('.lineDebit').val(prefill.debit);
                if (prefill.credit) $row.find('.lineCredit').val(prefill.credit);

                recalcTotals();
            }

            // default 2 rows
            addRow();
            addRow();

            $('#btnAddRow').on('click', function() {
                addRow();
            });

            $(document).on('click', '.btnRemoveRow', function() {
                $(this).closest('tr').remove();
                recalcTotals();
            });

            // prevent debit & credit both
            $(document).on('input', '.lineDebit', function() {
                const $row = $(this).closest('tr');
                const debit = parseNumber($(this).val());
                if (debit > 0) {
                    $row.find('.lineCredit').val('');
                }
                recalcTotals();
            });

            $(document).on('input', '.lineCredit', function() {
                const $row = $(this).closest('tr');
                const credit = parseNumber($(this).val());
                if (credit > 0) {
                    $row.find('.lineDebit').val('');
                }
                recalcTotals();
            });

            $(document).on('change', '.coaSelect', function() {
                recalcTotals();
            });

            function recalcTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('#linesTable tbody tr').each(function() {
                    totalDebit += parseNumber($(this).find('.lineDebit').val());
                    totalCredit += parseNumber($(this).find('.lineCredit').val());
                });

                $('#totalDebit').text(rupiahNumber(totalDebit));
                $('#totalCredit').text(rupiahNumber(totalCredit));

                const balanced = (Math.round(totalDebit * 100) === Math.round(totalCredit * 100)) && totalDebit > 0;

                if (balanced) {
                    $('#balanceBadge').removeClass('bg-secondary bg-danger').addClass('bg-success').text(
                    'BALANCED');
                    $('#balanceHint').text('Siap disimpan.');
                } else {
                    $('#balanceBadge').removeClass('bg-success').addClass('bg-danger').text('NOT BALANCED');
                    $('#balanceHint').text('Pastikan total debit = total credit dan > 0.');
                }
            }

            function resetErrors() {
                $('#err_journal_date,#err_type,#err_lines').addClass('d-none');
                $('.errCoa,.errDebitCredit').addClass('d-none');
            }

            function collectLines() {
                const lines = [];
                $('#linesTable tbody tr').each(function(i) {
                    const coaId = $(this).find('.coaSelect').val();
                    const desc = $(this).find('.lineDesc').val();
                    const debit = parseNumber($(this).find('.lineDebit').val());
                    const credit = parseNumber($(this).find('.lineCredit').val());

                    lines.push({
                        line_no: i + 1,
                        coa_id: coaId ? parseInt(coaId) : null,
                        description: desc || null,
                        debit: debit,
                        credit: credit
                    });
                });
                return lines;
            }

            function validateForm() {
                resetErrors();

                let valid = true;

                const journalDate = $('#journal_date').val();
                const type = $('#type').val();

                if (!journalDate) {
                    $('#err_journal_date').removeClass('d-none');
                    valid = false;
                }
                if (!type) {
                    $('#err_type').removeClass('d-none');
                    valid = false;
                }

                const lines = collectLines();

                if (!Array.isArray(lines) || lines.length < 2) {
                    $('#err_lines').removeClass('d-none');
                    return false;
                }

                let totalDebit = 0;
                let totalCredit = 0;

                $('#linesTable tbody tr').each(function(idx) {
                    const coaId = $(this).find('.coaSelect').val();
                    const debit = parseNumber($(this).find('.lineDebit').val());
                    const credit = parseNumber($(this).find('.lineCredit').val());

                    if (!coaId) {
                        $(this).find('.errCoa').removeClass('d-none');
                        valid = false;
                    }

                    // must be one side
                    if ((debit > 0 && credit > 0) || (debit <= 0 && credit <= 0)) {
                        $(this).find('.errDebitCredit').removeClass('d-none');
                        valid = false;
                    }

                    totalDebit += debit;
                    totalCredit += credit;
                });

                if (Math.round(totalDebit * 100) !== Math.round(totalCredit * 100) || totalDebit <= 0) {
                    $('#err_lines').removeClass('d-none');
                    valid = false;
                }

                return valid;
            }

            $('#btnSave').on('click', function() {
                if (!validateForm()) return;

                const payload = {
                    journal_date: $('#journal_date').val(),
                    type: $('#type').val(),
                    category: $('#category').val(),
                    reference_no: $('#reference_no').val(),
                    memo: $('#memo').val(),
                    status: $('#status').val(),
                    lines: collectLines()
                };

                Swal.fire({
                    title: 'Simpan jurnal?',
                    text: 'Pastikan data sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
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
                        url: "{{ route('journals.store') }}",
                        method: "POST",
                        data: payload,
                        success: function(res) {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Jurnal berhasil disimpan.',
                                timer: 1400,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('journals.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.message ||
                                'Terjadi kesalahan.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection
