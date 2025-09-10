@extends('layout.app') {{-- ganti sesuai layout utama kamu --}}

@section('title', 'Create Quotation')

@section('content')
    <div class="container-fluid">
        <h4>Create Quotation</h4>



        {{-- Header Quotation --}}
        <div class="card mb-3">
            <div class="card-body">
                <div>
                    <label class="form-label">Copy From Quotation</label>
                    <div class="d-flex gap-2 align-items-center">
                        <select id="copyFrom" class="form-control">
                            <option value="">-- Pilih Quotation Lama --</option>
                            @foreach ($quotations as $q)
                                <option value="{{ $q->id }}">{{ $q->quo_no }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="resetCopy" class="btn btn-danger ml-2 h-100">Reset</button>
                    </div>
                </div>
                <form id="quotationForm" method="POST" action="{{ route('quotations.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quotation No</label>
                            <input type="text" name="quo_no" value="{{ $newQuotationNo }}" class="form-control" readonly
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Address</label>
                            <textarea name="customer_address" class="form-control"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Revision</label>
                            <input type="number" name="rev" id="rev" class="form-control" min="0"
                                value="">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Attention</label>
                            <input type="text" name="attention" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Your Reference</label>
                            <input type="text" name="your_reference" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Terms</label>
                            <input type="text" name="terms" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Job No</label>
                            <input type="text" name="job_no" class="form-control">
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
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label>Discount (%)</label>
                        <input type="number" id="discountPercent" class="form-control" min="0" max="100"
                            value="0">
                    </div>
                    <div class="col-md-3">
                        <label>Discount (Rp)</label>
                        <input type="text" id="discountAmount" class="form-control" value="0">
                        <input type="hidden" name="discount_amount" id="discountAmountHidden" value="0">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <h5 class="ms-3">Grand Total: <span id="grandTotal">Rp0</span></h5>
                    </div>
                </div>
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

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Terms & Conditions</h5>

                <table class="table table-bordered" id="termsTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Description</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic rows akan masuk di sini -->
                    </tbody>
                </table>

                <button type="button" class="btn btn-sm btn-primary" id="addTerm">+ Add Term</button>
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

            function formatDate(dateStr) {
                if (!dateStr) return '';
                return dateStr.split('T')[0];
            }

            $('#copyFrom').select2({
                placeholder: "-- Pilih Quotation Lama --",
                // allowClear: true,
                width: '100%'
            });


            $('#copyFrom').on('change', function() {
                let id = $(this).val();
                if (!id) return;

                $.get("{{ url('/quotations') }}/" + id + "/copy", function(res) {
                    if (res.success) {
                        let q = res.data;
                        // $('input[name="quo_no"]').val(q.quo_no);
                        // $('input[name="date"]').val(formatDate(q.date));
                        $('input[name="customer_name"]').val(q.customer_name);
                        $('textarea[name="customer_address"]').val(q.customer_address);
                        $('#rev').val(q.rev);
                        $('input[name="attention"]').val(q.attention);
                        $('input[name="your_reference"]').val(q.your_reference);
                        $('input[name="terms"]').val(q.terms);
                        $('input[name="job_no"]').val(q.job_no);

                        $('#itemsTable tbody').empty();
                        q.items.forEach((item, i) => {
                            $('#itemsTable tbody').append(`
                    <tr>
                        <td><input type="text" name="items[${i}][description]" class="form-control" value="${item.description}"></td>
                        <td><input type="number" name="items[${i}][qty]" class="form-control qty" value="${item.qty}"></td>
                        <td>
                            <input type="text" class="form-control unit_price" value="${formatRupiah(item.unit_price)}">
                            <input type="hidden" name="items[${i}][unit_price]" class="unit_price_raw" value="${item.unit_price}">
                        </td>
                        <td>
                            <input type="text" class="form-control total_price" value="${formatRupiah(item.total_price)}">
                            <input type="hidden" name="items[${i}][total_price]" class="total_price_raw" value="${item.total_price}">
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm removeItem">×</button></td>
                    </tr>
                `);
                        });

                        // scope
                        $('#scopeTable tbody').empty();
                        q.scopes.forEach((scope, i) => {
                            $('#scopeTable tbody').append(`
                    <tr>
                        <td><input type="text" name="scopes[${i}][description]" class="form-control" value="${scope.description}"></td>
                        <td class="text-center"><input type="checkbox" name="scopes[${i}][responsible_pt_gpt]" value="1" ${scope.responsible_pt_gpt ? 'checked' : ''}></td>
                        <td class="text-center"><input type="checkbox" name="scopes[${i}][responsible_client]" value="1" ${scope.responsible_client ? 'checked' : ''}></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeScope">×</button></td>
                    </tr>
                `);
                        });

                        // terms
                        $('#termsTable tbody').empty();
                        q.terms_conditions.forEach((term, i) => {
                            $('#termsTable tbody').append(`
                    <tr>
                        <td class="text-center">${i+1}</td>
                        <td><input type="text" name="terms_conditions[${i}][description]" class="form-control" value="${term.description}"></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-term">x</button></td>
                    </tr>
                `);
                        });

                        calculateGrandTotal();
                    }
                });
            });

            $('#resetCopy').on('click', function() {
                // reset dropdown
                $('#copyFrom').val('');

                // reset form input
                // $('input[name="date"]').val('');
                $('input[name="customer_name"]').val('');
                $('textarea[name="customer_address"]').val('');
                $('#rev').val('');
                $('input[name="attention"]').val('');
                $('input[name="your_reference"]').val('');
                $('input[name="terms"]').val('');
                $('input[name="job_no"]').val('');

                // reset tabel
                $('#itemsTable tbody').empty();
                $('#scopeTable tbody').empty();
                $('#termsTable tbody').empty();

                // hitung ulang total biar nol
                calculateGrandTotal();
            });


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

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.total_price_raw').each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });

                let discountPercent = parseFloat($('#discountPercent').val()) || 0;
                let discountAmountInput = parseRupiah($('#discountAmount').val());

                let discountFromPercent = 0;
                if (discountPercent > 0) {
                    discountFromPercent = (grandTotal * discountPercent) / 100;
                    discountAmountInput = discountFromPercent; // override amount dari persen
                    $('#discountAmount').val(formatRupiah(discountAmountInput));
                }

                // Jangan lebih besar dari total
                let totalDiscount = Math.min(discountAmountInput, grandTotal);

                let finalTotal = grandTotal - totalDiscount;

                // update view
                $('#grandTotal').text(formatRupiah(finalTotal));

                // simpan ke hidden input untuk dikirim ke server
                $('#discountAmountHidden').val(Math.round(totalDiscount));
            }

            // Event listener untuk discount
            $(document).on('input', '#discountPercent, #discountAmount', function() {
                if ($(this).attr('id') === 'discountAmount') {
                    let val = parseRupiah($(this).val());
                    $(this).val(formatRupiah(val));
                }
                calculateGrandTotal();
            });

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

            let termIndex = 0;

            $('#addTerm').on('click', function() {
                termIndex++;
                let row = `
            <tr>
                <td class="text-center">${termIndex}</td>
                <td>
                    <input type="text" name="terms_conditions[${termIndex}][description]"
                           class="form-control" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-term">x</button>
                </td>
            </tr>
        `;
                $('#termsTable tbody').append(row);
                updateTermNumbers();
            });

            // Hapus baris
            $(document).on('click', '.remove-term', function() {
                $(this).closest('tr').remove();
                updateTermNumbers();
            });

            // Update nomor urut
            function updateTermNumbers() {
                $('#termsTable tbody tr').each(function(index, row) {
                    $(row).find('td:first').text(index + 1);
                });
            }

            // Submit form
            $('#quotationForm').on('submit', function(e) {
                e.preventDefault();

                if ($('#itemsTable tbody tr').length === 0) {
                    Swal.fire('Error!', 'Minimal tambahkan 1 item', 'error');
                    return;
                }

                if (typeof reindexItems === 'function') reindexItems();
                if (typeof reindexScopes === 'function') reindexScopes();
                if (typeof updateTermNumbers === 'function') updateTermNumbers();

                const formEl = this;
                const $form = $(formEl);
                const btn = $form.find('button[type="submit"]');
                const btnOriginalText = btn.text();

                let formData = new FormData(formEl);

                $('#itemsTable tbody, #scopeTable tbody, #termsTable tbody').find('input, select, textarea')
                    .each(function() {
                        const name = $(this).attr('name');
                        if (!name) return;

                        if ($(this).is(':checkbox')) {
                            // kirim 1 jika checked, 0 jika tidak (sesuaikan kalau mau behavior berbeda)
                            formData.set(name, $(this).is(':checked') ? $(this).val() : '0');
                        } else {
                            formData.set(name, $(this).val());
                        }
                    });

                Swal.fire({
                    title: 'Simpan Quotation?',
                    text: 'Pastikan data sudah benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    // disable tombol dan tampilkan spinner
                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                    );

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false, // penting untuk FormData
                        contentType: false, // penting untuk FormData
                        cache: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message ||
                                        'Quotation tersimpan.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // reload atau redirect sesuai kebutuhan
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: (response && response.message) ?
                                        response.message : 'Terjadi kesalahan.',
                                    icon: 'error'
                                });
                                btn.prop('disabled', false).html(btnOriginalText);
                            }
                        },
                        error: function(xhr) {
                            // opsi: tampilkan error detail saat development
                            let msg = 'Terjadi kesalahan pada server.';
                            if (xhr && xhr.responseJSON && xhr.responseJSON.message)
                                msg = xhr.responseJSON.message;
                            Swal.fire({
                                title: 'Error!',
                                text: msg,
                                icon: 'error'
                            });
                            btn.prop('disabled', false).html(btnOriginalText);
                        }
                    });
                });
            });
        });
    </script>

@endsection
