@extends('layout.app')

@section('title', 'FPU - Form Pengajuan Uang')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-primary font-weight-bold">Form Pengajuan Uang (FPU)</h3>
            <a href="{{ route('fpus.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Buat FPU
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover w-100 mx-auto" id="fpuTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>FPU No</th>
                            <th>Tanggal</th>
                            <th>Requester</th>
                            <th>Wallet</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Approved</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalViewFpu" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Detail FPU</h5>
                        <div class="text-muted small" id="v_fpu_no">-</div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- HEADER INFO --}}
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <th width="150">Project</th>
                            <td id="v_project">-</td>
                            <th width="150">Tanggal</th>
                            <td id="v_date">-</td>
                        </tr>
                        <tr>
                            <th>Requester</th>
                            <td id="v_requester">-</td>
                            <th>Status</th>
                            <td id="v_status">-</td>
                        </tr>
                        <tr>
                            <th>Wallet</th>
                            <td id="v_wallet">-</td>
                            <th>Purpose</th>
                            <td id="v_purpose">-</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td colspan="3" id="v_notes">-</td>
                        </tr>
                    </table>

                    {{-- LINES --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>Description</th>
                                    <th width="180" class="text-right">Amount</th>
                                    <th width="180">Attachments</th>
                                </tr>
                            </thead>
                            <tbody id="v_lines_body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">-</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">TOTAL</th>
                                    <th class="text-right" id="v_total">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>


    {{-- ===================== MODAL APPROVE ===================== --}}
    <div class="modal fade" id="modalApproveFpu" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Approve FPU</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="approve_fpu_id">

                    <div class="form-group">
                        <label class="font-weight-semibold">Pilih Wallet (Sumber Dana)</label>
                        <select class="form-control" id="approve_wallet_coa_id">
                            <option value="">-- pilih wallet --</option>
                            @foreach (\App\Models\Wallet::with('coa')->get() as $w)
                                <option value="{{ $w->coa_id }}">
                                    {{ $w->coa->code_account_id }} - {{ $w->coa->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Wallet ini akan dipakai untuk pembayaran FPU (cash / bank).
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="btnConfirmApprove">
                        <i class="fas fa-check mr-1"></i>Approve
                    </button>
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

            // ===================== DATATABLE =====================
            const table = $('#fpuTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('fpus.datatable') }}",
                order: [
                    [2, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fpu_no'
                    },
                    {
                        data: 'request_date'
                    },
                    {
                        data: 'requester'
                    },
                    {
                        data: 'wallet',
                        defaultContent: '-'
                    },
                    {
                        data: 'total_amount',
                        className: 'text-right'
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'approved_info',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // ===================== SELECT2 =====================
            $('#approve_wallet_coa_id').select2({
                width: '100%',
                dropdownParent: $('#modalApproveFpu')
            });

            // ===================== OPEN APPROVE MODAL =====================
            $(document).on('click', '.btnApproveFpu', function() {
                const id = $(this).data('id');
                $('#approve_fpu_id').val(id);
                $('#approve_wallet_coa_id').val('').trigger('change');
                $('#modalApproveFpu').modal('show');
            });

            // ===================== CONFIRM APPROVE =====================
            $('#btnConfirmApprove').on('click', function() {

                const fpuId = $('#approve_fpu_id').val();
                const walletCoaId = $('#approve_wallet_coa_id').val();

                if (!walletCoaId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wallet wajib dipilih'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Approve FPU?',
                    text: 'FPU akan di-approve dan jurnal accrual akan dibuat.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, approve',
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
                        url: `/fpus/${fpuId}/approve`,
                        method: 'POST',
                        data: {
                            wallet_coa_id: walletCoaId
                        },
                        success: function(res) {
                            Swal.close();
                            $('#modalApproveFpu').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: res.message
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan'
                            });
                        }
                    });
                });
            });

        });

        $(document).on('click', '.btnViewFpu', function() {
            const id = $(this).data('id');

            $('#modalViewFpu').modal('show');

            // reset
            $('#v_lines_body').html('<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>');

            $.get(`/fpus/${id}`, function(res) {
                const fpu = res.data;

                $('#v_fpu_no').text(fpu.fpu_no);
                $('#v_project').text(fpu.project?.no_project || '-');


                const dateText = fpu.request_date ?
                    new Date(fpu.request_date).toLocaleString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    '-';

                $('#v_date').text(dateText);



                $('#v_requester').text(fpu.requester?.name || fpu.requester_name);
                $('#v_status').html(`<span class="badge badge-info">${fpu.status}</span>`);
                $('#v_wallet').text(
                    fpu.wallet_coa ?
                    `${fpu.wallet_coa.code_account_id} - ${fpu.wallet_coa.name}` :
                    '-'
                );
                $('#v_purpose').text(fpu.purpose || '-');
                $('#v_notes').text(fpu.notes || '-');

                let rows = '';
                let total = 0;

                fpu.lines.forEach((l, i) => {
                    total += parseFloat(l.amount);

                    let attHtml = '-';
                    if (l.attachments.length > 0) {
                        attHtml = l.attachments.map(a =>
                            `<a href="${a.file_url}" target="_blank" class="d-block">📎 ${a.file_name}</a>`
                        ).join('');
                    }

                    rows += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${l.description}</td>
                    <td class="text-right">${formatNumber(l.amount)}</td>
                    <td>${attHtml}</td>
                </tr>
            `;
                });

                $('#v_lines_body').html(rows);
                $('#v_total').text(formatNumber(total));
            });
        });

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    </script>
@endsection
