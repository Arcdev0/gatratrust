@extends('layout.app')

@section('title', 'Quotation List')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Quotation</h3>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">+ Create Quotation</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="container">
                        <table class="table" id="quotationTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Quotation No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLabel">Quotation Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="showModalBody">
                    <!-- content dari ajax -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="convertToInvoiceBtn" class="btn btn-primary" style="display:none;">
                        <i class="fas fa-file-invoice"></i> Convert to Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    {{-- <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Quotation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="quotation_id" id="rejectQuotationId">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Submit Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}


@endsection

@section('script')
    <script>
        $(function() {
            let table = $('#quotationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('quotations.getDataTable') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'quo_no',
                        name: 'quo_no'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, 'Rp ')
                    },
                    {
                        data: 'status_name',
                        name: 'status_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete quotation
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this quotation?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/quotations/delete/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: res.message ??
                                            'Quotation has been deleted.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed!',
                                        text: res.message ??
                                            'Quotation could not be deleted.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: 'Something went wrong.'
                                });
                            }
                        });
                    }
                });
            });

            // Show quotation
            $(document).on('click', '.showBtn', function() {
                let id = $(this).data('id');

                // fungsi format rupiah
                function formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                }

                // fungsi format tanggal
                function formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }


                $.get('/quotations/show/' + id, function(res) {
                    if (res.success) {
                        let q = res.data;

                        $('#showModalLabel').text("Quotation " + q.quo_no);
                        $('#showModalBody').html(`
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <th style="width:150px;">Date</th>
                                <td>: ${formatDate(q.date)}</td>
                            </tr>
                            <tr>
                                <th>Customer</th>
                                <td>: ${q.customer_name}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>: ${q.customer_address ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Attention</th>
                                <td>: ${q.attention ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:
                                    ${
                                        q.status
                                            ? `
                                                                    <span class="badge ${
                                                                        q.status.name === 'Pending'
                                                                            ? 'bg-yellow text-white'
                                                                            : q.status.name === 'Approve'
                                                                                ? 'bg-success text-white'
                                                                                : 'bg-danger text-white'
                                                                    }">
                                                                        ${q.status.name}
                                                                    </span>
                                                                    ${
                                                                        q.status.name === 'Rejected' && q.rejected_reason
                                                                            ? `<div class="mt-1 text-danger small">${q.rejected_reason}</div>`
                                                                            : ''
                                                                    }
                                                                `
                                            : '-'
                                    }
                                </td>
                            </tr>
                        </table>
                <hr>
                <h5>Items</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${q.items.map(i => `
                                                                                            <tr>
                                                                                                <td>${i.description}</td>
                                                                                                <td>${i.qty}</td>
                                                                                                <td>${formatRupiah(i.unit_price)}</td>
                                                                                                <td>${formatRupiah(i.total_price)}</td>
                                                                                            </tr>
                                                                                        `).join('')}
                    </tbody>
                </table>

                ${q.scopes.length > 0 ? `
                                                                                    <hr>
                                                                                    <h5>Scopes</h5>
                                                                                    <table class="table table-sm table-bordered">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Description</th>
                                                                                                <th>Responsible PT GPT</th>
                                                                                                <th>Responsible Client</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            ${q.scopes.map(s => `
                                <tr>
                                    <td>${s.description}</td>
                                    <td class="text-center">${s.responsible_pt_gpt == 1 ? '✔️' : '-'}</td>
                                    <td class="text-center">${s.responsible_client == 1 ? '✔️' : '-'}</td>
                                </tr>
                            `).join('')}
                                                                                        </tbody>
                                                                                    </table>
                                                                                ` : ''}


                    ${q.terms.length > 0 ? `
                                        <hr>
                                        <h5>Terms & Conditions</h5>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${q.terms.map((t, index) => `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${t.description}</td>
                            </tr>
                        `).join('')}
                                            </tbody>
                                        </table>
                                    ` : ''}
            `);

                        if (q.status && q.status.name === 'Approve') {
                            $('#convertToInvoiceBtn')
                                .attr('href', '/invoices/create?quotation_id=' + q.id)
                                .show();
                        } else {
                            $('#convertToInvoiceBtn').hide();
                        }
                        $('#showModal').modal('show');

                    }
                });
            });

            // Approve
            $(document).on('click', '.approveBtn', function() {
                let id = $(this).data('id');
                let $btn = $(this); // tombol yang diklik
                let originalText = $btn.html(); // simpan text asli tombol

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This quotation will be approved!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tambahkan loading state
                        $btn.prop('disabled', true);
                        $btn.html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Approving...'
                        );

                        $.ajax({
                            url: '/quotations/' + id + '/approve',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: 'Something went wrong!',
                                });
                            },
                            complete: function() {
                                // Kembalikan tombol ke keadaan semula
                                $btn.prop('disabled', false);
                                $btn.html(originalText);
                            }
                        });
                    }
                });
            });

            // Reject
            $(document).on('click', '.rejectBtn', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Reject Reason',
                    input: 'textarea',
                    inputLabel: 'Please enter the reason for rejection',
                    inputPlaceholder: 'Type your reason here...',
                    inputAttributes: {
                        'aria-label': 'Type your reason here'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    confirmButtonColor: '#fb6340',
                    cancelButtonColor: '#6c757d',
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage('Reason is required');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/quotations/' + id + '/reject',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                reason: result.value
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Rejected!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
