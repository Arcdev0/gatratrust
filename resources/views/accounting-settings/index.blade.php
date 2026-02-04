@extends('layout.app')

@section('title', 'Accounting Settings')

@section('content')
    <div class="container-fluid" id="container-wrapper">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-primary fw-bold">Accounting Settings</h3>
            <button class="btn btn-success" id="btnSaveSetting">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </div>

        <div class="row">

            {{-- KIRI: FORM SETTING --}}
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Pemetaan Akun Otomatis</strong>
                        {{-- <div class="text-muted small mt-1">
                            Atur akun default yang akan dipakai sistem saat membuat <b>jurnal otomatis</b>
                            untuk <b>Invoice</b>, <b>Kwitansi</b>, dan <b>FPU</b>.
                        </div> --}}
                    </div>

                    <div class="card-body">

                        {{-- SUMBER DANA (multi select) --}}
                        <div class="form-group">
                            <label class="font-weight-semibold">Sumber Dana (Kas/Bank)</label>
                            <select class="form-control" id="wallet_coa_ids" multiple>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ in_array($coa->id, $walletSelectedIds ?? []) ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-muted">
                                Pilih akun yang dianggap sebagai <b>Kas/Bank</b>. Daftar ini akan muncul saat:
                                <b>pembayaran invoice</b>, <b>cash in/out</b>, dan <b>realisasi FPU</b>.
                            </small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Piutang Usaha (Default)</label>
                            <select class="form-control" id="default_ar_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_ar_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai saat invoice terbit untuk mencatat <b>piutang</b> (klien belum bayar).
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Utang (Default)</label>
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
                                Dipakai saat FPU <b>disetujui</b> untuk mencatat <b>utang biaya</b> (uang belum keluar).
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Pendapatan Jasa (Default)</label>
                            <select class="form-control" id="default_sales_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_sales_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai untuk mencatat <b>pendapatan jasa</b> saat invoice dibuat.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Utang Pajak (PPN Keluaran)</label>
                            <select class="form-control" id="default_tax_payable_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_tax_payable_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Jika invoice ada pajak, sistem akan menambahkan kredit ke akun ini.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Beban Default</label>
                            <select class="form-control" id="default_expense_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_expense_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai untuk FPU jika user tidak memilih akun beban secara manual.
                            </small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-semibold">Akun Penampung Sementara</label>
                            <select class="form-control" id="default_suspense_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_suspense_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai jika jurnal belum lengkap (misal akun belum diatur). Nanti bisa dipindahkan.
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-semibold">Akun Laba Ditahan</label>
                            <select class="form-control" id="default_retained_earning_coa_id">
                                <option value="">-- pilih akun --</option>
                                @foreach ($coaSelectable as $coa)
                                    <option value="{{ $coa->id }}"
                                        {{ optional($setting)->default_retained_earning_coa_id == $coa->id ? 'selected' : '' }}>
                                        {{ $coa->code_account_id }} - {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Dipakai untuk proses penutupan laba rugi (jika modul closing dipakai).
                            </small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- KANAN: PENOMORAN --}}
            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong>Penomoran Jurnal</strong>
                        {{-- <div class="text-muted small mt-1">
                            Atur format nomor jurnal agar rapi dan konsisten.
                        </div> --}}
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-semibold">Kode Awalan Jurnal</label>
                            <input type="text" class="form-control" id="journal_prefix"
                                value="{{ optional($setting)->journal_prefix ?? 'JR' }}" maxlength="10">
                            <small class="text-muted">Contoh: JR, JNL, GPT</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Nomor Urut Terakhir</label>
                            <input type="number" class="form-control" id="journal_running_number"
                                value="{{ optional($setting)->journal_running_number ?? 1 }}" min="1">
                            <small class="text-muted">
                                Nomor terakhir (akan bertambah otomatis saat jurnal baru dibuat).
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-semibold">Bulan Awal Tahun Buku</label>
                            <select class="form-control" id="fiscal_year_start_month">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}"
                                        {{ (optional($setting)->fiscal_year_start_month ?? 1) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">
                                Umumnya di Indonesia mulai dari Januari (1), kecuali perusahaan pakai tahun buku berbeda.
                            </small>
                        </div>

                        <hr>

                        <div class="p-3 bg-light rounded">
                            <div class="font-weight-semibold">Contoh Nomor Jurnal</div>
                            <div class="text-muted small">Preview berdasarkan pengaturan saat ini</div>
                            <div class="h5 mt-1 mb-0" id="previewJournalNo"></div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Minimal isi: <b>Akun Piutang</b> dan <b>Akun Pendapatan</b> agar jurnal invoice tidak error.
                            </li>
                            <li>Jika invoice ada pajak, isi juga <b>Akun Utang Pajak</b>.</li>
                            <li>Akun yang bisa dipilih hanya akun <b>bukan grup</b> (akun yang bisa diposting).</li>
                            <li><b>Sumber Dana</b> akan dipilih saat transaksi pembayaran (bukan dipatok satu akun saja).
                            </li>
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
