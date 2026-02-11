@extends('layout.app')

@section('title', 'Journal')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-primary">Journal</h3>
            <a href="{{ route('journals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Journal
            </a>
        </div>

        {{-- MODAL VIEW JOURNAL (Bootstrap 4) --}}
        <div class="modal fade" id="modalViewJournal" tabindex="-1" role="dialog" aria-labelledby="modalViewJournalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="modalViewJournalLabel">Journal Preview</h5>
                            {{-- <div class="text-muted small" id="viewSubTitle">-</div> --}}
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        {{-- HEADER (TABLE STYLE, NO BORDER, NO CARD) --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-sm mb-0" style="border: none;">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 15%;">Journal No</td>
                                        <td style="width: 35%;"><strong id="v_journal_no">-</strong></td>

                                        <td class="text-muted" style="width: 15%;">Date</td>
                                        <td style="width: 35%;"><strong id="v_journal_date">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Type</td>
                                        <td><strong id="v_type">-</strong></td>

                                        <td class="text-muted">Status</td>
                                        <td id="v_status">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Reference</td>
                                        <td colspan="3"><strong id="v_reference_no">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted align-top">Memo</td>
                                        <td colspan="3">
                                            <span id="v_memo" style="white-space: pre-line;">-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        {{-- LINES TABLE --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="viewLinesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:70px;">No</th>
                                        <th style="width:260px;">COA</th>
                                        <th>Description</th>
                                        <th style="width:160px;" class="text-right">Debit</th>
                                        <th style="width:160px;" class="text-right">Credit</th>
                                    </tr>
                                </thead>
                                <tbody id="v_lines_body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">-</td>
                                    </tr>
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="3" class="text-right">Total</th>
                                        <th class="text-right" id="v_total_debit">0.00</th>
                                        <th class="text-right" id="v_total_credit">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge badge-secondary" id="v_balance_badge">-</span>
                                <span class="text-muted small ml-2" id="v_balance_hint"></span>
                            </div>

                            <div>
                                <a href="#" class="btn btn-secondary mr-2" id="btnGoEdit">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        {{-- END MODAL VIEW JOURNAL --}}


        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="journalTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Journal No</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(function() {

            let table = $('#journalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('journals.datatable') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'journal_no',
                        name: 'journal_no'
                    },
                    {
                        data: 'journal_date',
                        name: 'journal_date'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'reference_no',
                        name: 'reference_no'
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
                    },
                ]
            });

            // Delete
            $(document).on('click', '.btnDeleteJournal', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus jurnal?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/journals/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Success', 'Jurnal berhasil dihapus', 'success');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ??
                                'Gagal menghapus', 'error');
                        }
                    });
                });
            });

        });
    </script>

    <script>
        $(function() {

            function formatNumber(n) {
                n = parseFloat(n || 0);
                if (isNaN(n)) n = 0;
                return n.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function badgeStatus(status) {
                const s = (status || '').toLowerCase();
                const map = {
                    posted: 'success',
                    draft: 'secondary',
                    void: 'danger'
                };
                const cls = map[s] || 'light';
                return `<span class="badge bg-${cls} text-uppercase">${s || '-'}</span>`;
            }

            function formatType(type) {
                return (type || '-').toString().replaceAll('_', ' ').toUpperCase();
            }

            function formatDate(dateStr) {
                if (!dateStr) return '-';

                // ambil hanya bagian tanggal sebelum "T" kalau ISO datetime
                const onlyDate = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;

                // expected: YYYY-MM-DD
                const parts = onlyDate.split('-');
                if (parts.length !== 3) return onlyDate;

                return `${parts[2]}-${parts[1]}-${parts[0]}`; // dd-mm-yyyy
            }
            // VIEW JOURNAL
            $(document).on('click', '.btnViewJournal', function() {
                const id = $(this).data('id');

                // reset UI
                $('#v_journal_no,#v_journal_date,#v_type,#v_reference_no,#v_memo').text('-');
                $('#v_status').html('-');
                $('#v_lines_body').html(
                    `<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>`);
                $('#v_total_debit').text('0.00');
                $('#v_total_credit').text('0.00');
                $('#v_balance_badge').removeClass().addClass('badge bg-secondary').text('-');
                $('#v_balance_hint').text('');
                $('#btnGoEdit').attr('href', '#');

                const modal = new bootstrap.Modal(document.getElementById('modalViewJournal'));
                modal.show();

                $.ajax({
                    url: "{{ url('journals/show') }}/" + id,
                    method: "GET",
                    success: function(res) {
                        // header
                        $('#v_journal_no').text(res.journal_no || '-');
                        $('#v_journal_date').text(formatDate(res.journal_date));
                        $('#v_type').text(formatType(res.type));
                        $('#v_status').html(badgeStatus(res.status));
                        $('#v_reference_no').text(res.reference_no || '-');
                        $('#v_memo').text(res.memo || '-');

                        // $('#viewSubTitle').text(`ID: ${res.id} • Created: ${res.created_at}`);

                        // edit link
                        $('#btnGoEdit').attr('href', "{{ url('journals') }}/" + id + "/edit");

                        // lines
                        let totalDebit = 0;
                        let totalCredit = 0;

                        const lines = Array.isArray(res.lines) ? res.lines : [];
                        if (lines.length === 0) {
                            $('#v_lines_body').html(
                                `<tr><td colspan="5" class="text-center text-muted">Tidak ada lines.</td></tr>`
                            );
                        } else {
                            // sort by line_no if any
                            lines.sort((a, b) => (a.line_no ?? 0) - (b.line_no ?? 0));

                            let html = '';
                            lines.forEach((l, idx) => {
                                const coaText = l.coa ?
                                    `${l.coa.code_account_id} - ${l.coa.name}` :
                                    (l.coa_id ?? '-');

                                const debit = parseFloat(l.debit || 0) || 0;
                                const credit = parseFloat(l.credit || 0) || 0;
                                totalDebit += debit;
                                totalCredit += credit;

                                html += `
              <tr>
                <td>${l.line_no ?? (idx+1)}</td>
                <td>${coaText}</td>
                <td>${l.description ?? ''}</td>
                <td class="text-end">${formatNumber(debit)}</td>
                <td class="text-end">${formatNumber(credit)}</td>
              </tr>
            `;
                            });
                            $('#v_lines_body').html(html);
                        }

                        $('#v_total_debit').text(formatNumber(totalDebit));
                        $('#v_total_credit').text(formatNumber(totalCredit));

                        // balance badge
                        const balanced = (Math.round(totalDebit * 100) === Math.round(
                            totalCredit * 100)) && totalDebit > 0;
                        if (balanced) {
                            $('#v_balance_badge').removeClass().addClass('badge bg-success')
                                .text('BALANCED');
                            $('#v_balance_hint').text('Debit dan Credit seimbang.');
                        } else {
                            $('#v_balance_badge').removeClass().addClass('badge bg-danger')
                                .text('NOT BALANCED');
                            $('#v_balance_hint').text(
                                'Periksa jurnal: total debit harus sama dengan total credit.'
                            );
                        }
                    },
                    error: function(xhr) {
                        $('#v_lines_body').html(
                            `<tr><td colspan="5" class="text-center text-danger">Gagal load data.</td></tr>`
                        );
                    }
                });
            });

        });
    </script>
@endsection
