@extends('layout.app')

@section('title', 'Accounting Settings')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-primary fw-bold">Accounting Settings</h3>
            <button class="btn btn-success" id="btnSaveSetting">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>

        <div class="row">

            {{-- LEFT: SETTING FORM --}}
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Default Account Mapping</strong>
                        {{-- <div class="text-muted small">Wallet dipakai sebagai sumber dana untuk cash-in/out & pembayaran.
                        </div> --}}
                    </div>

                    <div class="card-body">

                        {{-- ✅ WALLET MULTI-SELECT (list COA yang dianggap wallet) --}}
                        <div class="form-group">
                            <label class="font-weight-semibold">Wallet (Sumber Dana)</label>
                            <select class="form-control" id="wallet_coa_ids" multiple>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ in_array($coa->id, $walletSelectedIds ?? []) ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-muted">
                                Pilih <b>lebih dari satu</b> COA untuk menjadi Wallet.
                                List ini akan muncul di menu cash-in/out, pembayaran invoice, dan FPU sebagai sumber dana.
                            </small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-semibold">Default Accounts Receivable (AR)</label>
                            <select class="form-control" id="default_ar_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_ar_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Dipakai untuk auto-journal Invoice/Kwitansi sebagai akun
                                Piutang.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">
                                Default Accounts Payable (AP)
                            </label>
                            <select class="form-control" id="default_ap_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_ap_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai untuk FPU saat approve (utang ke vendor).
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Default Sales / Revenue</label>
                            <select class="form-control" id="default_sales_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_sales_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Dipakai untuk auto-journal Invoice sebagai akun Pendapatan.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Default Tax Payable</label>
                            <select class="form-control" id="default_tax_payable_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_tax_payable_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika invoice punya pajak, akan otomatis credit ke akun ini.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Default Expense</label>
                            <select class="form-control" id="default_expense_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_expense_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Dipakai untuk FPU jika user tidak memilih akun beban.</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-semibold">Default Suspense Account</label>
                            <select class="form-control" id="default_suspense_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_suspense_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Akun penampung sementara jika jurnal belum lengkap.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-semibold">Default Retained Earnings</label>
                            <select class="form-control" id="default_retained_earning_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_retained_earning_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                    </div>
                </div>
            </div>

            {{-- RIGHT: NUMBERING & PREVIEW --}}
            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Journal Numbering</strong>
                        {{-- <div class="text-muted small">Atur prefix & running number jurnal.</div> --}}
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-semibold">Journal Prefix</label>
                            <input type="text" class="form-control" id="journal_prefix"
                                value="{{ optional($setting)->journal_prefix ?? 'JR' }}" maxlength="10">
                            <small class="text-muted">Contoh: JR, JNL, GTR</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Running Number</label>
                            <input type="number" class="form-control" id="journal_running_number"
                                value="{{ optional($setting)->journal_running_number ?? 1 }}" min="1">
                            <small class="text-muted">Nomor terakhir (akan bertambah saat buat jurnal baru).</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Fiscal Year Start Month</label>
                            <select class="form-control" id="fiscal_year_start_month">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}"
                                        {{ (optional($setting)->fiscal_year_start_month ?? 1) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <hr>

                        <div class="p-3 bg-light rounded">
                            <div class="font-weight-semibold">Preview Journal No</div>
                            <div class="text-muted small">Contoh hasil nomor jurnal (berdasarkan setting sekarang)</div>
                            <div class="h5 mt-1 mb-0" id="previewJournalNo"></div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Info</strong>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Settings ini dipakai untuk auto-journal: <b>Invoice, Kwitansi, FPU</b>.</li>
                            <li>Isi minimal: <b>AR</b> dan <b>Sales</b> agar auto-journal invoice tidak error.</li>
                            <li>Jika invoice punya pajak, isi juga <b>Tax Payable</b>.</li>
                            <li>COA yang bisa dipilih hanya akun <b>non-group</b>.</li>
                            <li>Wallet dipakai sebagai sumber dana (bisa dipilih di transaksi).</li>
                        </ul>
                    </div>
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

            // Select2
            $('#wallet_coa_ids, #default_ar_coa_id, #default_sales_coa_id, #default_tax_payable_coa_id, #default_expense_coa_id, #default_suspense_coa_id, #default_retained_earning_coa_id, #fiscal_year_start_month, #default_ap_coa_id')
                .select2({
                    width: '100%',
                    placeholder: 'Pilih...',
                    allowClear: true
                });

            // khusus multi wallet: biar enak
            $('#wallet_coa_ids').select2({
                width: '100%',
                placeholder: 'Pilih wallet (bisa lebih dari satu)...'
            });

            function pad6(num) {
                num = parseInt(num || 1, 10);
                return String(num).padStart(6, '0');
            }

            function refreshPreview() {
                const prefix = $('#journal_prefix').val() || 'JR';
                const runNo = $('#journal_running_number').val() || 1;
                const year = new Date().getFullYear();
                $('#previewJournalNo').text(`${prefix}-${year}-${pad6(runNo)}`);
            }

            refreshPreview();
            $('#journal_prefix, #journal_running_number').on('keyup change', refreshPreview);

            function showMessage(type, text) {
                return Swal.fire({
                    icon: type,
                    title: text,
                    showConfirmButton: true,
                    timer: 1500
                });
            }

            $('#btnSaveSetting').on('click', function() {

                Swal.fire({
                    title: 'Simpan setting?',
                    text: "Perubahan akan mempengaruhi penomoran & default akun transaksi.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
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
                        url: "{{ route('accounting-settings.save') }}",
                        method: 'POST',
                        data: {
                            wallet_coa_ids: $('#wallet_coa_ids').val(), // ✅ multi

                            default_ar_coa_id: $('#default_ar_coa_id').val(),
                            default_sales_coa_id: $('#default_sales_coa_id').val(),
                            default_tax_payable_coa_id: $('#default_tax_payable_coa_id')
                                .val(),
                            default_expense_coa_id: $('#default_expense_coa_id').val(),

                            default_suspense_coa_id: $('#default_suspense_coa_id').val(),
                            default_retained_earning_coa_id: $(
                                '#default_retained_earning_coa_id').val(),

                            journal_prefix: $('#journal_prefix').val(),
                            journal_running_number: $('#journal_running_number').val(),
                            fiscal_year_start_month: $('#fiscal_year_start_month').val(),
                            default_ap_coa_id: $('#default_ap_coa_id').val(),

                        },
                        success: function(res) {
                            Swal.close();
                            showMessage('success', 'Settings berhasil disimpan');
                            refreshPreview();
                        },
                        error: function(xhr) {
                            Swal.close();
                            const msg = xhr.responseJSON?.message ||
                                'Terjadi kesalahan.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection
