@extends('layout.app') {{-- ganti sesuai layout utama kamu --}}

@section('title', 'Create Quotation')

@section('content')
    <div class="container-fluid">
        <h4>Create Quotation</h4>
        <form id="quotationForm" method="POST" action="{{ route('quotations.store') }}">
            @csrf

            {{-- Header Quotation --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quotation No</label>
                            <input type="text" name="quo_no" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Address</label>
                            <textarea name="customer_address" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rev" class="form-label">Revision</label>
                            <input type="number" name="rev" id="rev" class="form-control" min="0"
                                value="">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Attention</label>
                            <input type="text" name="attention" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>Quotation Items</span>
                    <button type="button" class="btn btn-sm btn-success" id="addItem">+ Add Item</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- baris dinamis --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Scope of Work --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>Scope of Work</span>
                    <button type="button" class="btn btn-sm btn-success" id="addScope">+ Add Scope</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="scopeTable">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Responsible PT GPT</th>
                                <th>Responsible Client</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- baris dinamis --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Submit --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Save Quotation</button>
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Format angka ke Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // Parse Rupiah ke angka
            function parseRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            // Tambah baris item
            $('#addItem').click(function() {
                const index = $('#itemsTable tbody tr').length;
                const itemRow = `
        <tr>
            <td><input type="text" name="items[${index}][description]" class="form-control" required></td>
            <td><input type="number" name="items[${index}][qty]" class="form-control qty" min="1" required></td>
            <td>
                <input type="text" class="form-control unit_price" required>
                <input type="hidden" name="items[${index}][unit_price]" class="unit_price_raw">
            </td>
            <td>
                <input type="text" class="form-control total_price" readonly>
                <input type="hidden" name="items[${index}][total_price]" class="total_price_raw">
            </td>
            <td><button type="button" class="btn btn-danger btn-sm removeItem">×</button></td>
        </tr>
        `;
                $('#itemsTable tbody').append(itemRow);
            });

            // Hapus baris item + reindex
            $(document).on('click', '.removeItem', function() {
                $(this).closest('tr').remove();
                reindexItems();
                calculateGrandTotal();
            });

            // Reindex semua baris items supaya urut
            function reindexItems() {
                $('#itemsTable tbody tr').each(function(i, row) {
                    $(row).find('input, select, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/items\[\d+\]/, `items[${i}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            // Hitung total per baris
            $(document).on('input', '.qty, .unit_price', function() {
                const row = $(this).closest('tr');
                const qty = parseFloat(row.find('.qty').val()) || 0;
                const rawPrice = parseRupiah(row.find('.unit_price').val());

                const total = qty * rawPrice;

                row.find('.unit_price_raw').val(rawPrice);
                row.find('.total_price_raw').val(total);

                row.find('.unit_price').val(formatRupiah(rawPrice));
                row.find('.total_price').val(formatRupiah(total));

                calculateGrandTotal();
            });

            // Hitung grand total
            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.total_price_raw').each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });
                $('#grandTotal').text(formatRupiah(grandTotal));
            }

            // Tambah baris scope
            $('#addScope').click(function() {
                const index = $('#scopeTable tbody tr').length;
                $('#scopeTable tbody').append(`
        <tr>
            <td><input type="text" name="scopes[${index}][description]" class="form-control" required></td>
            <td class="text-center"><input type="checkbox" name="scopes[${index}][responsible_pt_gpt]" value="1"></td>
            <td class="text-center"><input type="checkbox" name="scopes[${index}][responsible_client]" value="1"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeScope">×</button></td>
        </tr>
        `);
            });

            // Hapus scope + reindex
            $(document).on('click', '.removeScope', function() {
                $(this).closest('tr').remove();
                reindexScopes();
            });

            function reindexScopes() {
                $('#scopeTable tbody tr').each(function(i, row) {
                    $(row).find('input, select, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/scopes\[\d+\]/, `scopes[${i}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            // Submit form
            $('#quotationForm').on('submit', function(e) {
                e.preventDefault();

                if ($('#itemsTable tbody tr').length === 0) {
                    Swal.fire('Error!', 'Minimal tambahkan 1 item', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Simpan Quotation?',
                    text: 'Pastikan data sudah benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $(this);
                        const btn = form.find('button[type="submit"]');
                        const btnOriginalText = btn.text();

                        btn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                        );

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location
                                    .reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan pada server.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
