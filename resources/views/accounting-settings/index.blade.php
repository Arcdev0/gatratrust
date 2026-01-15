@extends('layout.app')

@section('title', 'Accounting Settings')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-primary fw-bold">Accounting Settings</h3>
            <button class="btn btn-success" id="btnSaveSetting">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>

        <div class="row g-3">

            {{-- LEFT: SETTING FORM --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Default Account Mapping</strong>
                        {{-- <div class="text-muted small">Pilih akun default yang akan dipakai otomatis di transaksi/jurnal.</div> --}}
                    </div>
                    
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Cash Account (Kas)</label>
                            <select class="form-control" id="default_cash_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_cash_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="small text-muted mt-1">Dipakai untuk transaksi cash_out/cash_in jika user tidak
                                memilih akun kas.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Bank Account</label>
                            <select class="form-control" id="default_bank_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_bank_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Suspense Account</label>
                            <select class="form-control" id="default_suspense_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_suspense_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="small text-muted mt-1">Akun penampung sementara jika jurnal belum lengkap.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Retained Earnings</label>
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
                        <div class="text-muted small">Atur prefix & running number jurnal.</div>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Journal Prefix</label>
                            <input type="text" class="form-control" id="journal_prefix"
                                value="{{ optional($setting)->journal_prefix ?? 'JR' }}" maxlength="10">
                            <div class="small text-muted mt-1">Contoh: JR, JNL, GTR</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Running Number</label>
                            <input type="number" class="form-control" id="journal_running_number"
                                value="{{ optional($setting)->journal_running_number ?? 1 }}" min="1">
                            <div class="small text-muted mt-1">Nomor terakhir (akan bertambah saat buat jurnal baru).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fiscal Year Start Month</label>
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
                            <div class="fw-semibold">Preview Journal No</div>
                            <div class="text-muted small">Contoh hasil nomor jurnal (berdasarkan setting sekarang)</div>
                            <div class="fs-5 mt-1" id="previewJournalNo"></div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Info</strong>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Settings ini dipakai untuk auto-journal (FPU, Invoice, dll).</li>
                            <li>COA yang bisa dipilih hanya akun <b>non-group</b>.</li>
                            <li>Pastikan akun Kas & Bank diisi agar modul cash-in/out lancar.</li>
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
            $('#default_cash_coa_id, #default_bank_coa_id, #default_suspense_coa_id, #default_retained_earning_coa_id, #fiscal_year_start_month')
                .select2({
                    width: '100%'
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
                            default_cash_coa_id: $('#default_cash_coa_id').val(),
                            default_bank_coa_id: $('#default_bank_coa_id').val(),
                            default_suspense_coa_id: $('#default_suspense_coa_id').val(),
                            default_retained_earning_coa_id: $(
                                '#default_retained_earning_coa_id').val(),
                            journal_prefix: $('#journal_prefix').val(),
                            journal_running_number: $('#journal_running_number').val(),
                            fiscal_year_start_month: $('#fiscal_year_start_month').val(),
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
