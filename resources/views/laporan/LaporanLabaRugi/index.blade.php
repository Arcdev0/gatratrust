@extends('layout.app')

@section('title', 'Laporan Laba Rugi')



@section('content')

    <style>
        .lr-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .lr-head {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kpi {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 22px;
            border: 1px solid #dee2e6;
            background: #fff;
            font-size: 12px;
            margin-right: 8px;
        }

        .kpi strong {
            font-weight: 700;
        }

        .lr-table thead th {
            background: #f8f9fa;
            font-size: 12px;
            font-weight: 600;
        }

        .lr-loading {
            display: none;
            padding: 10px 15px;
            background: #fff3cd;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
        }

        .lr-profit {
            font-size: 18px;
            font-weight: 800;
        }

        .text-neg {
            color: #dc3545;
        }

        .text-pos {
            color: #28a745;
        }
    </style>


    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col">
                <h4 class="font-weight-bold text-primary">Laporan Laba Rugi</h4>
                <div class="text-muted small">Ringkasan pendapatan dan beban pada periode tertentu</div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="lr-card mb-3">
            <div class="lr-head">
                <span>Filter Periode</span>
                <div>
                    <input type="date" id="start" class="form-control form-control-sm d-inline-block"
                        style="width:150px;">
                    <input type="date" id="end" class="form-control form-control-sm d-inline-block"
                        style="width:150px;">
                    <button class="btn btn-sm btn-primary" id="btnLoad">Tampilkan</button>
                </div>
            </div>
            <div class="p-3">
                <span class="kpi">Total Pendapatan: <strong id="kpiRevenue">0</strong></span>
                <span class="kpi">Total Beban: <strong id="kpiExpense">0</strong></span>
                <span class="kpi">Laba Bersih: <strong id="kpiProfit" class="lr-profit">0</strong></span>
            </div>
            <div class="lr-loading text-center" id="lrLoading">Memuat data...</div>
        </div>

        {{-- 2 PANEL --}}
        <div class="row">

            {{-- PENDAPATAN --}}
            <div class="col-lg-6 mb-3">
                <div class="lr-card">
                    <div class="lr-head">
                        <span>Pendapatan</span>
                        <small class="text-muted">Akun posisi CREDIT</small>
                    </div>
                    <div class="p-3 table-responsive">
                        <table class="table table-sm table-bordered lr-table">
                            <thead>
                                <tr>
                                    <th width="120">Kode</th>
                                    <th>Nama Akun</th>
                                    <th width="150" class="text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRevenue">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total Pendapatan</th>
                                    <th class="text-right" id="tfootRevenue">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- BEBAN --}}
            <div class="col-lg-6 mb-3">
                <div class="lr-card">
                    <div class="lr-head">
                        <span>Beban</span>
                        <small class="text-muted">Akun posisi DEBIT</small>
                    </div>
                    <div class="p-3 table-responsive">
                        <table class="table table-sm table-bordered lr-table">
                            <thead>
                                <tr>
                                    <th width="120">Kode</th>
                                    <th>Nama Akun</th>
                                    <th width="150" class="text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyExpense">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total Beban</th>
                                    <th class="text-right" id="tfootExpense">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- OPTIONAL: WARNING kalau ada akun yang default_posisi tidak kebaca --}}
        <div class="alert alert-warning d-none" id="unknownBox">
            <b>Perhatian:</b> Ada akun yang default_posisi-nya tidak terklasifikasi (bukan DEBIT/CREDIT).
            <div class="small mt-2" id="unknownText"></div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        function rupiah(n) {
            n = Number(n || 0);
            return "Rp " + n.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", "&#039;");
        }

        function renderTable(tbodyId, items) {
            if (!items || !items.length) {
                $(tbodyId).html(`<tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>`);
                return;
            }

            let html = "";
            items.forEach(function(it) {
                html += `
            <tr>
                <td>${escapeHtml(it.coa_code)}</td>
                <td>${escapeHtml(it.coa_name)}</td>
                <td class="text-right font-weight-bold">${rupiah(it.amount)}</td>
            </tr>
        `;
            });
            $(tbodyId).html(html);
        }

        function loadLabaRugi() {
            $("#lrLoading").show();
            $("#unknownBox").addClass("d-none");

            $.ajax({
                url: "{{ route('laporan.laba-rugi.data') }}",
                method: "GET",
                dataType: "json",
                data: {
                    start: $("#start").val() || '',
                    end: $("#end").val() || ''
                },
                success: function(res) {
                    $("#lrLoading").hide();

                    const revenue = res.revenue || [];
                    const expense = res.expense || [];
                    const sum = res.summary || {};

                    renderTable("#tbodyRevenue", revenue);
                    renderTable("#tbodyExpense", expense);

                    $("#kpiRevenue").text(rupiah(sum.total_revenue || 0));
                    $("#kpiExpense").text(rupiah(sum.total_expense || 0));

                    const profit = Number(sum.net_profit || 0);
                    $("#kpiProfit").text(rupiah(profit))
                        .removeClass("text-neg text-pos")
                        .addClass(profit < 0 ? "text-neg" : "text-pos");

                    $("#tfootRevenue").text(rupiah(sum.total_revenue || 0));
                    $("#tfootExpense").text(rupiah(sum.total_expense || 0));

                    // kalau ada akun tidak kebaca posisi
                    const unknown = res.unknown || [];
                    if (unknown.length) {
                        let text = unknown.map(u =>
                            `${u.coa_code} - ${u.coa_name} (posisi: ${u.posisi || '-'})`).join(", ");
                        $("#unknownText").text(text);
                        $("#unknownBox").removeClass("d-none");
                    }
                },
                error: function(xhr) {
                    $("#lrLoading").hide();
                    alert(xhr.responseJSON?.message || "Gagal memuat laporan.");
                }
            });
        }

        $(document).ready(function() {
            // default periode bulan ini
            let today = new Date();
            let y = today.getFullYear();
            let m = ("0" + (today.getMonth() + 1)).slice(-2);
            $("#start").val(`${y}-${m}-01`);
            $("#end").val(today.toISOString().split('T')[0]);

            $("#btnLoad").on("click", loadLabaRugi);

            // auto load
            loadLabaRugi();
        });
    </script>
@endsection
