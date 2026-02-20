@extends('layout.app')

@section('title', 'Buku Besar')

@section('content')
    <style>
        .bb-wrapper {
            display: flex;
            gap: 15px;
        }

        .bb-left {
            width: 320px;
        }

        .bb-right {
            flex: 1;
        }

        .bb-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .bb-card-header {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            background: #f8f9fa;
        }

        .account-list {
            max-height: 550px;
            overflow-y: auto;
        }

        .account-item {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
        }

        .account-item:hover {
            background: #f1f8ff;
        }

        .account-item.active {
            background: #dbeeff;
            border-left: 4px solid #007bff;
        }

        .account-code {
            font-weight: 700;
            font-size: 14px;
        }

        .account-name {
            font-size: 12px;
            color: #6c757d;
        }

        .account-saldo {
            font-weight: 700;
            font-size: 13px;
        }

        .bb-kpi {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            background: #fff;
            font-size: 12px;
            margin-right: 8px;
        }

        .bb-kpi strong {
            font-weight: 700;
        }

        .bb-table thead th {
            background: #f8f9fa;
            font-size: 12px;
            font-weight: 600;
        }

        .bb-loading {
            display: none;
            padding: 10px;
            background: #fff3cd;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
        }

        .row-begin {
            background: #f8f9fa;
            font-weight: 600;
        }

        .row-end {
            background: #eafaf1;
            font-weight: 700;
        }

        .row-total {
            background: #fcfcfc;
        }

        .row-acc-header {
            background: #cfe5ff;
            font-weight: 700;
        }
    </style>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h4 class="font-weight-bold text-primary">Buku Besar</h4>
            </div>
        </div>

        <div class="bb-wrapper">

            {{-- LEFT PANEL --}}
            <div class="bb-left">
                <div class="bb-card">
                    <div class="bb-card-header">Daftar Akun</div>

                    <div class="p-2">
                        <input type="text" id="searchAccount" class="form-control form-control-sm mb-2"
                            placeholder="Cari akun...">
                        <div class="d-flex" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btnSelectAll">Pilih
                                Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                id="btnClearAll">Kosongkan</button>
                        </div>
                    </div>

                    <div class="account-list" id="accountList">
                        @foreach ($coas as $c)
                            <div class="account-item" data-id="{{ $c->id }}" data-code="{{ $c->code_account_id }}"
                                data-name="{{ $c->name }}">
                                <div class="d-flex align-items-start">
                                    <input type="checkbox" class="mr-2 mt-1 account-check" value="{{ $c->id }}"
                                        checked>
                                    <div style="flex:1">
                                        <div class="account-code">{{ $c->code_account_id }}</div>
                                        <div class="account-name">{{ $c->name }}</div>
                                        <div class="account-saldo text-right mt-1">
                                            Rp {{ number_format($c->ending_balance ?? 0, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- RIGHT PANEL --}}
            <div class="bb-right">
                <div class="bb-card">

                    <div class="bb-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="selectedAccountTitle">Pilih akun terlebih dahulu</strong>
                            <div class="text-muted small" id="selectedAccountSub"></div>
                        </div>

                        <div class="d-flex align-items-center" style="gap:8px;">
                            <input type="date" id="start" class="form-control form-control-sm" style="width:140px;">
                            <input type="date" id="end" class="form-control form-control-sm" style="width:140px;">
                            <button class="btn btn-sm btn-primary" id="btnReload">Tampilkan</button>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="mb-3">
                            <span class="bb-kpi">Total Debit: <strong id="kpiDebit">0</strong></span>
                            <span class="bb-kpi">Total Kredit: <strong id="kpiCredit">0</strong></span>
                            <span class="bb-kpi">Saldo Akhir (sum): <strong id="kpiSaldo">0</strong></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bb-table">
                                <thead>
                                    <tr>
                                        <th width="120">Tanggal</th>
                                        <th width="140">No Jurnal</th>
                                        <th>Deskripsi</th>
                                        <th width="130" class="text-right">Debit</th>
                                        <th width="130" class="text-right">Kredit</th>
                                        <th width="130" class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="bbTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bb-loading text-center" id="bbLoading">Memuat data...</div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        let selectedCoaIds = [];

        function formatRupiah(n) {
            n = Number(n || 0);
            return n.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
        }

        function getSelectedIds() {
            return $(".account-check:checked").map(function() {
                return Number($(this).val());
            }).get();
        }

        function setKPI(debit, credit, saldo) {
            $("#kpiDebit").text(formatRupiah(debit));
            $("#kpiCredit").text(formatRupiah(credit));
            $("#kpiSaldo").text(formatRupiah(saldo));
        }

        function updateHeaderSelected() {
            selectedCoaIds = getSelectedIds();
            $("#selectedAccountTitle").text(
                selectedCoaIds.length ? `Akun terpilih: ${selectedCoaIds.length}` : "Pilih akun terlebih dahulu"
            );
            $("#selectedAccountSub").text("Mutasi akun berdasarkan periode + saldo awal/akhir");
        }

        function loadData() {
            updateHeaderSelected();

            if (!selectedCoaIds.length) {
                $("#bbTableBody").html(
                    `<tr><td colspan="6" class="text-center text-muted">Tidak ada akun dipilih</td></tr>`);
                setKPI(0, 0, 0);
                return;
            }

            $("#bbLoading").show();

            $.ajax({
                url: "{{ route('laporan.buku-besar.data') }}",
                method: "GET",
                data: {
                    start: $("#start").val(),
                    end: $("#end").val(),
                    "coa_ids[]": selectedCoaIds // ✅ penting: kirim array yang bener
                },
                success: function(res) {
                    $("#bbLoading").hide();

                    const grandDebit = res.summary?.grand_debit ?? 0;
                    const grandCredit = res.summary?.grand_credit ?? 0;

                    // saldo akhir sum = jumlah ending_balance seluruh akun (opsional)
                    let sumEnding = 0;
                    (res.accounts || []).forEach(a => sumEnding += Number(a.ending_balance || 0));
                    setKPI(grandDebit, grandCredit, sumEnding);

                    let tbody = "";

                    if (res.accounts && res.accounts.length) {
                        res.accounts.forEach(function(acc) {

                            // HEADER AKUN
                            tbody += `
                            <tr class="row-acc-header">
                                <td colspan="6">
                                    ${acc.coa_code} - ${acc.coa_name}
                                    <span class="text-muted ml-2">
                                        (Mutasi Debit: ${formatRupiah(acc.total_debit)} |
                                         Mutasi Kredit: ${formatRupiah(acc.total_credit)} |
                                         Ending: ${formatRupiah(acc.ending_balance)})
                                    </span>
                                </td>
                            </tr>
                        `;

                            // SALDO AWAL
                            tbody += `
                            <tr class="row-begin">
                                <td colspan="5">Saldo Awal (Beginning Balance)</td>
                                <td class="text-right">${formatRupiah(acc.beginning_balance)}</td>
                            </tr>
                        `;

                            // ENTRIES
                            const entries = acc.entries || [];
                            entries.forEach(function(e) {
                                tbody += `
                                <tr>
                                    <td>${e.date ?? ''}</td>
                                    <td>${e.journal_no ?? ''}</td>
                                    <td>${e.description ?? '-'}</td>
                                    <td class="text-right">${formatRupiah(e.debit)}</td>
                                    <td class="text-right">${formatRupiah(e.credit)}</td>
                                    <td class="text-right font-weight-bold">${formatRupiah(e.balance)}</td>
                                </tr>
                            `;
                            });

                            // TOTAL MUTASI
                            tbody += `
                            <tr class="row-total">
                                <td colspan="3" class="text-right"><strong>Total Mutasi</strong></td>
                                <td class="text-right"><strong>${formatRupiah(acc.total_debit)}</strong></td>
                                <td class="text-right"><strong>${formatRupiah(acc.total_credit)}</strong></td>
                                <td></td>
                            </tr>
                        `;

                            // SALDO AKHIR
                            tbody += `
                            <tr class="row-end">
                                <td colspan="5">Saldo Akhir (Ending Balance)</td>
                                <td class="text-right">${formatRupiah(acc.ending_balance)}</td>
                            </tr>
                        `;

                        });
                    } else {
                        tbody = `<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>`;
                        setKPI(0, 0, 0);
                    }

                    $("#bbTableBody").html(tbody);
                },
                error: function(xhr) {
                    $("#bbLoading").hide();
                    console.log("AJAX error:", xhr);
                    $("#bbTableBody").html(
                        `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>`);
                }
            });
        }

        function selectAllAccounts(checked) {
            $(".account-check").prop("checked", checked).trigger("change");
        }

        // =========================
        // INIT
        // =========================
        $(document).ready(function() {

            // default tanggal bulan ini
            let today = new Date();
            let y = today.getFullYear();
            let m = ("0" + (today.getMonth() + 1)).slice(-2);
            $("#start").val(`${y}-${m}-01`);
            $("#end").val(today.toISOString().split('T')[0]);

            // klik item: toggle checkbox (multi pilih)
            $(".account-item").on("click", function(e) {
                if ($(e.target).hasClass("account-check")) return;
                let cb = $(this).find(".account-check");
                cb.prop("checked", !cb.prop("checked")).trigger("change");
            });

            // highlight active kalau dicentang
            $(document).on("change", ".account-check", function() {
                $(this).closest(".account-item").toggleClass("active", $(this).is(":checked"));
            });

            // tombol tampilkan (manual reload)
            $("#btnReload").on("click", function() {
                loadData();
            });

            // tombol pilih semua / kosongkan
            $("#btnSelectAll").on("click", function() {
                selectAllAccounts(true);
                loadData();
            });

            $("#btnClearAll").on("click", function() {
                selectAllAccounts(false);
                loadData();
            });

            // search akun
            $("#searchAccount").on("keyup", function() {
                let val = $(this).val().toLowerCase();
                $(".account-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                });
            });

            // set initial active class + load pertama
            $(".account-check").each(function() {
                $(this).closest(".account-item").toggleClass("active", $(this).is(":checked"));
            });

            // Klik baris akun → toggle checkbox
            $(document).on("click", ".account-item", function(e) {
                if ($(e.target).hasClass("account-check")) return;

                let cb = $(this).find(".account-check");
                cb.prop("checked", !cb.prop("checked")).trigger("change");
            });

            // Saat checkbox berubah → highlight + reload data
            $(document).on("change", ".account-check", function() {
                $(this).closest(".account-item")
                    .toggleClass("active", $(this).is(":checked"));

                loadData(); // ✅ AUTO RELOAD
            });

            loadData();
        });
    </script>
@endsection
