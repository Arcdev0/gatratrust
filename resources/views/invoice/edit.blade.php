@extends('layout.app')

@section('title', 'Edit Invoice')

@section('content')
    <div class="container-fluid">
        <h4>Edit Invoice</h4>
        <div class="card mb-3">
            <div class="card-body">
                <form id="invoiceForm" method="POST" action="{{ route('invoice.update', $invoice->id) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Invoice No.</label>
                            <input type="text" name="invoice_no" value="{{ $invoice->invoice_no }}"
                                   class="form-control" readonly required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ $invoice->date }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control"
                                   value="{{ $invoice->customer_name }}" required>
                        </div>

                        <div class="col-md-4 my-2">
                            <label class="form-label">No. Project</label>
                            <div class="d-flex gap-2 align-items-center">
                                <select id="no_project" name="project_id" class="form-control" required>
                                    <option value="">-- Pilih No. Project --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $invoice->project_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->no_project }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <label class="form-label">Customer Address</label>
                    <textarea name="customer_address" class="form-control">{{ $invoice->customer_address }}</textarea>

                    {{-- Tabel Deskripsi & Jumlah --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered align-middle" id="invoiceTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%">Deskripsi</th>
                                    <th style="width: 40%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div id="editor-0" class="quill-editor" style="height:50vh;"></div>
                                        <input type="hidden" name="inputDesc" id="inputDesc"
                                               value="{{ $invoice->description }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control amountDisplay text-end"
                                               value="Rp. {{ number_format($invoice->gross_total, 0, ',', '.') }}">
                                        <input type="hidden" name="inputAmmount" class="amount"
                                               value="{{ $invoice->gross_total }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary --}}
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <th>Gross Total</th>
                                    <td>
                                        <input type="text" id="grossTotalDisplay"
                                               class="form-control text-end"
                                               value="Rp. {{ number_format($invoice->gross_total, 0, ',', '.') }}" readonly>
                                        <input type="hidden" name="gross_total" id="grossTotal"
                                               value="{{ $invoice->gross_total }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discount</th>
                                    <td>
                                        <input type="number" name="discount" id="discount"
                                               class="form-control text-end"
                                               value="{{ $invoice->discount ?? 0 }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Down Payment</th>
                                    <td>
                                        <input type="text" id="downPaymentDisplay" class="form-control text-end"
                                               value="Rp. {{ number_format($invoice->down_payment ?? 0, 0, ',', '.') }}">
                                        <input type="hidden" name="down_payment" id="downPayment"
                                               value="{{ $invoice->down_payment ?? 0 }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tax (%)</th>
                                    <td>
                                        <input type="number" name="tax" id="tax" class="form-control text-end"
                                               value="{{ $invoice->tax ?? 0 }}">
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <th>Net Total</th>
                                    <td>
                                        <input type="text" id="netTotalDisplay" class="form-control text-end"
                                               value="Rp. {{ number_format($invoice->net_total, 0, ',', '.') }}" readonly>
                                        <input type="hidden" name="net_total" id="netTotal"
                                               value="{{ $invoice->net_total }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#no_project').select2({
                placeholder: "-- Pilih Project --",
                width: '100%'
            });

            // Init Quill
            let editor = new Quill('#editor-0', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean']
                    ]
                }
            });

            // Set nilai dari DB ke Quill
            editor.root.innerHTML = `{!! $invoice->description !!}`;
            $('#inputDesc').val(editor.root.innerHTML);

            // Sinkronisasi ke hidden input
            editor.on('text-change', function() {
                $('#inputDesc').val(editor.root.innerHTML);
            });

            // Fungsi formatting sama dengan create
            function formatRupiah(angka) {
                return 'Rp. ' + new Intl.NumberFormat('id-ID').format(angka);
            }
            function parseRupiah(str) {
                return parseFloat(str.replace(/[^0-9]/g, '')) || 0;
            }

            $(document).on('input', '.amountDisplay', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                $(this).closest('td').find('.amount').val(val);
                calculateTotals();
            });

            function calculateTotals() {
                let gross = 0;
                $('.amount').each(function() {
                    gross += parseRupiah($(this).val());
                });

                $('#grossTotalDisplay').val(formatRupiah(gross));
                $('#grossTotal').val(gross);

                let discount = parseFloat($('#discount').val()) || 0;
                let downPayment = parseRupiah($('#downPaymentDisplay').val());
                let taxPercent = parseFloat($('#tax').val()) || 0;

                let afterDiscount = gross - discount - downPayment;
                let tax = afterDiscount * (taxPercent / 100);
                let net = afterDiscount + tax;

                $('#netTotalDisplay').val(formatRupiah(net));
                $('#netTotal').val(net);
                $('#downPayment').val(downPayment);
            }

            $(document).on('input', '#downPaymentDisplay', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                calculateTotals();
            });

            $(document).on('input', '.amount, #discount, #tax', calculateTotals);

            calculateTotals();

            // Submit dengan SweetAlert konfirmasi
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = form.serialize();

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Perubahan invoice akan disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Harap tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            url: form.attr('action'),
                            method: form.attr('method'),
                            data: formData,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = "{{ route('invoice.index') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Server error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
