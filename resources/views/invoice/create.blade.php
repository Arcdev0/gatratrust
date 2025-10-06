@extends('layout.app')

@section('title', 'Create Invoice')

@section('content')
    <div class="container-fluid">
        <h3 class="text-primary font-weight-bold">Create Invoice</h3>
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
                            <input type="text" name="customer_name" class="form-control"
                                value="{{ $quotation->customer_name ?? '' }}" required>
                        </div>

                        <div class="col-md-6 my-2">
                            <label class="form-label">No. Project</label>
                            <div class="d-flex align-items-center">
                                <select id="no_project" name="project_id" class="form-control" required>
                                    <option value="">-- Pilih No. Project --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}" data-sisa="{{ $p->sisa_nominal }}">
                                            {{ $p->no_project }} ({{ $p->client->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="sisa_nominal_text" class="ml-2" style="font-weight:bold; min-width:200px;"></div>
                            </div>
                        </div>
                    </div>
                    <label class="form-label">Customer Address</label>
                    <textarea name="customer_address" class="form-control">{{ $quotation->customer_address ?? '' }}</textarea>

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
                                        <input type="text" class="form-control amountDisplay text-end" value="Rp. 0">
                                        <input type="hidden" name="inputAmmount" class="amount">
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
                                        <input type="text" id="grossTotalDisplay" class="form-control text-end" readonly>
                                        <input type="hidden" name="gross_total" id="grossTotal">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Discount</th>
                                    <td>
                                        <input type="number" name="discount" id="discount" class="form-control text-end"
                                            value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Down Payment</th>
                                    <td>
                                        <input type="text" id="downPaymentDisplay" class="form-control text-end"
                                            value="Rp. 0">
                                        <input type="hidden" name="down_payment" id="downPayment">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tax (%)</th>
                                    <td>
                                        <input type="number" name="tax" id="tax" class="form-control text-end"
                                            value="0">
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <th>Net Total</th>
                                    <td>
                                        <input type="text" id="netTotalDisplay" class="form-control text-end">
                                        <input type="hidden" name="net_total" id="netTotal">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
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
            // Inisialisasi Quill
            $('#no_project').select2({
                placeholder: "-- Pilih Project --",
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


            // Trigger ulang kalau project berubah
            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            $(document).on('change', '#no_project', function() {
                let sisa = $('option:selected', this).data('sisa');
                if (sisa !== undefined) {
                    $('#sisa_nominal_text').text("Sisa nominal: " + formatRupiah(sisa));
                } else {
                    $('#sisa_nominal_text').text('');
                }
            });

            // Sinkronisasi ke hidden input
            editor.on('text-change', function() {
                $('#inputDesc').val(editor.root.innerHTML);
            });

            @if ($quotation && $quotation->items->count())
                var items = @json($quotation->items);

                var total = 0;
                var listHtml = "<ol>";

                $.each(items, function(index, item) {
                    listHtml += `<li>${item.description}
                (Qty: ${item.qty}, Unit: Rp${Number(item.unit_price).toLocaleString()},
                Total: Rp${Number(item.total_price).toLocaleString()})</li>`;
                    total += parseFloat(item.total_price);
                });

                listHtml += "</ol>";

                // Masukkan ke Quill
                editor.root.innerHTML = listHtml;

                // Sinkronisasi ke hidden input
                $('#inputDesc').val(editor.root.innerHTML);

                // Isi input amount otomatis (display + hidden)
                $('.amountDisplay').val("Rp. " + total.toLocaleString("id-ID"));
                $('.amount').val(total);

                // Update summary
                $('#grossTotalDisplay').val("Rp. " + total.toLocaleString());
                $('#grossTotal').val(total);
                // $('#netTotalDisplay').val("Rp. " + total.toLocaleString());
                // $('#netTotal').val(total);
            @endif

            // Format angka ke Rupiah
            function formatRupiah(angka) {
                return 'Rp. ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            function parseRupiah(str) {
                return parseFloat(str.replace(/[^0-9]/g, '')) || 0;
            }

            $(document).on('input', '.amountDisplay', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));

                // update hidden input di sibling-nya
                $(this).closest('td').find('.amount').val(val);

                calculateTotals();
            });

            // Hitung Gross Total (hanya dari amount)
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

                if (!netTotalManual) {
                    $('#netTotalDisplay').val(formatRupiah(net));
                    $('#netTotal').val(net);
                }

                $('#downPayment').val(downPayment);
            }



            // format input angka (rupiah) untuk DP & Net Total manual
            $(document).on('input', '#downPaymentDisplay', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                calculateTotals();
            });

            let netTotalManual = false;

            // Saat user edit net total, set flag manual
            $(document).on('input', '#netTotalDisplay', function() {
                let val = parseRupiah($(this).val());
                let gross = parseRupiah($('#grossTotal').val());

                // Batasi supaya tidak lebih dari gross
                if (val > gross) {
                    val = gross;
                }

                $(this).val(formatRupiah(val));
                $('#netTotal').val(val);
                netTotalManual = true; // aktifkan manual mode
            });


            // recalc jika ada perubahan
            $(document).on('input', '.amount, #discount, #tax, #downPaymentDisplay', function() {
                netTotalManual = false;
                calculateTotals();
            });

            // Hitung gross total pertama kali
            calculateTotals();

            $('#invoiceForm').submit(function() {
                $('#input-editor-0').val(editor.root.innerHTML);
            });

            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Invoice ini akan disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // tampilkan loading
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Harap tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        // kirim data via ajax
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
                                        window.location.href =
                                            "{{ route('invoice.index') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message ||
                                            'Terjadi kesalahan'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message ||
                                        'Server error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
