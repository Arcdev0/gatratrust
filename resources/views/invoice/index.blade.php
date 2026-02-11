@extends('layout.app')

@section('title', 'Invoice List')

@section('content')
    <style>
        .dropdown-action {
            position: relative;
            display: inline-block;
        }

        .dropdown-action .dropbtn {
            background-color: #4A85F5;
            color: white;
            padding: 5px 12px;
            font-size: 13px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .dropdown-action:hover .dropbtn {
            background-color: #346cd4;
        }

        .dropdown-action .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 170px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.2);
            z-index: 10;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown-action .dropdown-content a {
            color: #333;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            font-size: 13px;
        }

        .dropdown-action .dropdown-content a:hover {
            background-color: #f2f2f2;
        }

        .dropdown-action:hover .dropdown-content {
            display: block;
        }
    </style>


    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade" id="invoiceModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Invoice Detail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="invoiceModalBody">
                        <!-- Konten invoice akan dimasukkan via JS -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="#" id="convertToKwitansiBtn" class="btn btn-primary">
                            <i class="fas fa-file-invoice"></i> Convert to Kwitansi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Reject -->
        <div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formReject" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRejectLabel">Reject Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="rejectInvoiceId" name="invoice_id">

                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">Reason</label>
                            <textarea name="reason" id="rejectReason" rows="3" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Submit Reject</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary font-weight-bold">Invoice</h3>
            <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Invoice
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="invoiceTable" class="table table-bordered table-striped table-hover w-100 mx-auto">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Down Payment</th>
                            <th>Net Total</th>
                            {{-- <th>Remaining</th> --}}
                            <th>Approval Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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

            let table = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('invoice.data') }}',
                columns: [{
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'down_payment',
                        name: 'down_payment',
                        render: function(data) {
                            return data ? 'Rp ' + parseFloat(data.replace(/\./g, ''))
                                .toLocaleString('id-ID') : 'Rp 0';
                        }
                    },
                    {
                        data: 'net_total',
                        name: 'net_total',
                        render: function(data) {
                            return data ? 'Rp ' + parseFloat(data.replace(/\./g, ''))
                                .toLocaleString('id-ID') : 'Rp 0';
                        }
                    },
                    {
                        data: 'approval_status_badge',
                        name: 'approval_status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    // {
                    //     data: 'remaining',
                    //     name: 'remaining',
                    //     render: function(data) {
                    //         return data ? 'Rp ' + parseFloat(data.replace(/\./g, ''))
                    //             .toLocaleString('id-ID') : 'Rp 0';
                    //     }
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            let badgeClass = '';
                            let label = data.toLowerCase();

                            switch (label) {
                                case 'open':
                                    badgeClass = 'yellow'; // kuning
                                    break;
                                case 'partial':
                                    badgeClass = 'danger'; // merah
                                    break;
                                case 'close':
                                    badgeClass = 'secondary'; // abu-abu
                                    break;
                                case 'cancel':
                                    badgeClass = 'info'; // biru
                                    break;
                                default:
                                    badgeClass = 'secondary'; // default abu-abu
                            }
                            return `<span class="badge bg-${badgeClass} text-white">${label.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            // Event delete dummy
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: "Yakin?",
                    text: "Invoice akan dihapus!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/invoice/delete/' + id,
                            type: 'DELETE',
                            beforeSend: function() {
                                Swal.fire({
                                    title: "Menghapus...",
                                    text: "Tunggu sebentar",
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil",
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal",
                                    text: xhr.responseJSON?.message ||
                                        "Terjadi kesalahan!"
                                });
                            }
                        });
                    }
                });
            });

            // Event edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                window.location.href = "/invoice/" + id + "/edit";
            });

            $(document).on('click', '.btn-view', function() {
                let id = $(this).data('id');

                $.get("{{ route('invoice.show', ':id') }}".replace(':id', id), function(res) {
                    if (res.success) {
                        let inv = res.data;

                        // Tentukan warna badge status
                        let labelModal = inv.status ? inv.status.toLowerCase() : '';
                        let badgeClass = '';
                        switch (labelModal) {
                            case 'open':
                                badgeClass = 'yellow'; // kuning
                                break;
                            case 'partial':
                                badgeClass = 'danger'; // merah
                                break;
                            case 'close':
                                badgeClass = 'secondary'; // abu-abu
                                break;
                            case 'cancel':
                                badgeClass = 'info'; // biru
                                break;
                            default:
                                badgeClass = 'secondary'; // default abu-abu
                        }
                        let statusBadge =
                            `<span class="badge bg-${badgeClass} text-white">${labelModal.toUpperCase()}</span>`;

                        let html = `
                <table class="table table-sm table-borderless">
                    <tr><th style="width:150px;">Invoice No</th><td>${inv.invoice_no}</td></tr>
                    <tr><th>Date</th><td>${inv.date}</td></tr>
                    <tr><th>Customer</th><td>${inv.customer_name}</td></tr>
                    <tr><th>Address</th><td>${inv.customer_address ?? '-'}</td></tr>
                    <tr><th>Status</th><td>${statusBadge}</td></tr>
                </table>

                <h6 class="mt-3">Description</h6>
                <div class="border p-2 rounded">
                    ${inv.description ?? '-'}
                </div>

                <h6 class="mt-3">Summary</h6>
                <table class="table table-sm">
                    <tr><th>Gross Total</th><td>Rp ${Number(inv.gross_total).toLocaleString()}</td></tr>
                    <tr><th>Discount</th><td>Rp ${Number(inv.discount).toLocaleString()}</td></tr>
                    <tr><th>Down Payment</th><td>Rp ${Number(inv.down_payment).toLocaleString()}</td></tr>
                    <tr><th>Tax</th><td>Rp ${Number(inv.tax).toLocaleString()}</td></tr>
                    <tr class="fw-bold"><th>Net Total</th><td>Rp ${Number(inv.net_total).toLocaleString()}</td></tr>
                </table>
            `;

                        $('#invoiceModalBody').html(html);

                        let convertBtn = $('#convertToKwitansiBtn');

                        // Set default link dulu
                        convertBtn.attr('href',
                            "{{ route('kwitansi.create') }}?invoice_id=" + inv.id);

                        // 🔥 LOGIC DISABLE
                        if (inv.status.toLowerCase() === 'close') {
                            convertBtn.addClass('disabled');
                            convertBtn.removeAttr('href');
                            convertBtn.text('Sudah Lunas');
                        } else {
                            convertBtn.removeClass('disabled');
                            convertBtn.text('Convert to Kwitansi');
                        }


                        $('#invoiceModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.btn-approve', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Approve Invoice?',
                    text: "Anda yakin ingin menyetujui invoice ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Approve',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '/invoice/' + id + '/approve',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: res.message ||
                                        'Invoice berhasil disetujui.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat approve invoice.',
                                });
                            }
                        });

                    }
                });
            });

            $(document).on('click', '.btn-reject', function() {
                let id = $(this).data('id');
                $('#rejectInvoiceId').val(id);
                $('#rejectReason').val('');

                // Modal tetap dibuka seperti biasa
                $('#modalReject').modal('show');
            });

            $('#formReject').on('submit', function(e) {
                e.preventDefault();

                let id = $('#rejectInvoiceId').val();
                let reason = $('#rejectReason').val();

                if (reason.trim() === '') {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Alasan wajib diisi',
                        text: 'Silakan masukkan alasan reject.',
                    });
                }

                Swal.fire({
                    title: 'Reject Invoice?',
                    text: "Anda yakin ingin menolak invoice ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reject',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '/invoice/' + id + '/reject',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reason: reason
                            },
                            success: function(res) {
                                $('#modalReject').modal('hide');

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Rejected!',
                                    text: res.message ||
                                        'Invoice berhasil ditolak.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat reject invoice.',
                                });
                            }
                        });
                    }
                });
            });




        });
    </script>
@endsection
