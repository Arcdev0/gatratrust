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
                <div class="table-responsive">
                    <div class="container">
                        <table class="table table-bordered table-hover" id="fpuTable">
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
    </script>
@endsection
