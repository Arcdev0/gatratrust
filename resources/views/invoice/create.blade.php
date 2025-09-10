@extends('layout.app')

@section('title', 'Create Invoice')

@section('content')
    <div class="container-fluid">
        <h4>Create Invoice</h4>
        <div class="card mb-3">
            <div class="card-body">
                <form id="invoiceForm" method="POST" action="{{ route('invoice.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Invoice No.</label>
                            <input type="text" name="invoice_no" value="{{ $newInvoiceNo ?? '' }}" class="form-control"
                                readonly required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>

                        <div class="col-md-4 my-2">
                            <label class="form-label">No. Project</label>
                            <div class="d-flex gap-2 align-items-center">
                                <select id="no_project" class="form-control">
                                    <option value="">-- Pilih No. Project --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_project }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <label class="form-label">Customer Address</label>
                    <textarea name="customer_address" class="form-control"></textarea>
            </div>

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
                                <input type="hidden" name="inputDesc" id="inputDesc">
                            </td>
                            <td>
                                <input type="text" name="inputAmmount" class="form-control amount text-end"
                                    value="Rp. 0">
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
                            <td><input type="text" name="gross_total" id="grossTotal" class="form-control text-end"
                                    readonly></td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td><input type="number" name="discount" id="discount" class="form-control text-end"
                                    value="0">
                            </td>
                        </tr>
                        <tr>
                            <th>Down Payment</th>
                            <td><input type="text" name="down_payment" id="downPayment" class="form-control text-end"
                                    value="Rp. 0"></td>
                        </tr>
                        <tr>
                            <th>Tax (%)</th>
                            <td><input type="number" name="tax" id="tax" class="form-control text-end"
                                    value="0"></td>
                        </tr>
                        <tr class="table-light">
                            <th>Net Total</th>
                            <td><input type="text" name="net_total" id="netTotal" class="form-control text-end"
                                    readonly>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('invoice.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi Quill
            $('#no_project').select2({
                placeholder: "-- Pilih Quotation Lama --",
                // allowClear: true,
                width: '100%'
            });
            let editor = new Quill('#editor-0', {
                theme: 'snow',
                placeholder: 'Tulis deskripsi...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['clean']
                    ]
                }
            });

            // Sinkronisasi ke hidden input
            editor.on('text-change', function() {
                $('#inputDesc').val(editor.root.innerHTML);
            });

            // Format angka ke Rupiah
            function formatRupiah(angka) {
                return 'Rp. ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            function parseRupiah(str) {
                return parseFloat(str.replace(/[^0-9]/g, '')) || 0;
            }

            // Hitung total
            function calculateTotals() {
                let gross = 0;
                $('.amount').each(function() {
                    gross += parseRupiah($(this).val());
                });

                $('#grossTotal').val(formatRupiah(gross));

                let discount = parseFloat($('#discount').val()) || 0;
                let downPayment = parseRupiah($('#downPayment').val());
                let taxPercent = parseFloat($('#tax').val()) || 0;

                let afterDiscount = gross - discount - downPayment;
                let tax = afterDiscount * (taxPercent / 100);
                let net = afterDiscount + tax;

                $('#netTotal').val(formatRupiah(net));
            }

            // Format input rupiah
            $(document).on('input', '.amount, #downPayment', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                calculateTotals();
            });

            // Recalculate jika discount/tax berubah
            $(document).on('input', '#discount, #tax', calculateTotals);

            // Paksa update Quill ke hidden input saat submit
            $('#invoiceForm').submit(function() {
                $('#input-editor-0').val(editor.root.innerHTML);
            });

            calculateTotals();
        });
    </script>
@endsection
