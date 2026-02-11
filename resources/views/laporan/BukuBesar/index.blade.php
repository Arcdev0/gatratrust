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
                    <div class="bb-card-header">
                        Daftar Akun
                    </div>

                    <div class="p-2">
                        <input type="text" id="searchAccount" class="form-control form-control-sm mb-2"
                            placeholder="Cari akun...">
                    </div>

                    <div class="account-list" id="accountList">

                        @foreach ($coas as $c)
                            <div class="account-item" data-id="{{ $c->id }}" data-code="{{ $c->code_account_id }}"
                                data-name="{{ $c->name }}">

                                <div class="account-code">
                                    {{ $c->code_account_id }}
                                </div>
                                <div class="account-name">
                                    {{ $c->name }}
                                </div>

                                <div class="account-saldo text-right mt-1">
                                    Rp {{ number_format($c->ending_balance ?? 0, 2, ',', '.') }}
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

                        <div>
                            <input type="date" id="start" class="form-control form-control-sm d-inline-block"
                                style="width:140px;">
                            <input type="date" id="end" class="form-control form-control-sm d-inline-block"
                                style="width:140px;">
                            <button class="btn btn-sm btn-primary" id="btnReload">Tampilkan</button>
                        </div>

                    </div>

                    <div class="p-3">

                        <div class="mb-3">
                            <span class="bb-kpi">Total Debit: <strong id="kpiDebit">0</strong></span>
                            <span class="bb-kpi">Total Kredit: <strong id="kpiCredit">0</strong></span>
                            <span class="bb-kpi">Saldo Akhir: <strong id="kpiSaldo">0</strong></span>
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
                                        <td colspan="6" class="text-center text-muted">
                                            Tidak ada data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="bb-loading text-center" id="bbLoading">
                        Memuat data...
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('script')
    <script>
        let selectedCoaId = null;

        function formatRupiah(n) {
            n = Number(n || 0);
            return n.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
        }

        function loadData() {

            if (!selectedCoaId) return;

            $("#bbLoading").show();

            $.ajax({
                url: "{{ route('laporan.buku-besar.data') }}",
                method: "GET",
                data: {
                    start: $("#start").val(),
                    end: $("#end").val(),
                    coa_ids: [selectedCoaId]
                },
                success: function(res) {

                    $("#bbLoading").hide();

                    let acc = res.accounts[0];
                    let tbody = "";
                    let saldo = 0;

                    if (acc) {

                        $("#kpiDebit").text(formatRupiah(acc.total_debit));
                        $("#kpiCredit").text(formatRupiah(acc.total_credit));
                        $("#kpiSaldo").text(formatRupiah(acc.ending));

                        acc.entries.forEach(function(e) {
                            tbody += `
                        <tr>
                            <td>${e.date ?? ''}</td>
                            <td>${e.journal_no ?? ''}</td>
                            <td>${e.description ?? ''}</td>
                            <td class="text-right">${formatRupiah(e.debit)}</td>
                            <td class="text-right">${formatRupiah(e.credit)}</td>
                            <td class="text-right font-weight-bold">${formatRupiah(e.balance)}</td>
                        </tr>
                    `;
                        });

                    } else {
                        $("#kpiDebit").text(formatRupiah(0));
                        $("#kpiCredit").text(formatRupiah(0));
                        $("#kpiSaldo").text(formatRupiah(0));
                        tbody = `<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>`;
                    }

                    $("#bbTableBody").html(tbody);
                }
            });
        }

        $(document).ready(function() {

            // default bulan ini
            let today = new Date();
            let y = today.getFullYear();
            let m = ("0" + (today.getMonth() + 1)).slice(-2);
            $("#start").val(`${y}-${m}-01`);
            $("#end").val(today.toISOString().split('T')[0]);

            $(".account-item").on("click", function() {

                $(".account-item").removeClass("active");
                $(this).addClass("active");

                selectedCoaId = $(this).data("id");

                $("#selectedAccountTitle").text($(this).data("code") + " - " + $(this).data("name"));
                $("#selectedAccountSub").text("Mutasi akun berdasarkan periode");

                loadData();
            });

            $("#btnReload").on("click", function() {
                loadData();
            });

            $("#searchAccount").on("keyup", function() {
                let val = $(this).val().toLowerCase();
                $(".account-item").filter(function() {
                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(val) > -1
                    );
                });
            });

        });
    </script>
@endsection
