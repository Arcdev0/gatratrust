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

        .lr-table td {
            padding: 6px 10px;
            font-size: 13px;
        }

        .lr-section {
            background: #f1f3f5;
            font-weight: 700;
        }

        .lr-total {
            font-weight: 700;
        }

        .lr-highlight {
            background: #e9ecef;
            font-weight: 800;
        }

        .lr-profit {
            font-weight: 900;
            font-size: 14px;
        }

        .text-neg {
            color: #dc3545;
        }

        .text-pos {
            color: #28a745;
        }

        .lr-loading {
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
                <h4 class="font-weight-bold text-primary">Laporan Laba Rugi</h4>
                <div class="text-muted small">Format Bertingkat</div>
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
            <div class="lr-loading text-center" id="lrLoading">Memuat data...</div>
        </div>

        {{-- LAPORAN --}}
        <div class="lr-card">
            <div class="lr-head">
                <span>Income Statement</span>
            </div>

            <div class="p-3 table-responsive">
                <table class="table table-sm lr-table">

                    <tbody>

                        {{-- PENDAPATAN --}}
                        <tr class="lr-section">
                            <td colspan="2">PENDAPATAN</td>
                        </tr>
                    <tbody id="tbodyRevenue"></tbody>

                    <tr class="lr-total">
                        <td>Jumlah Pendapatan</td>
                        <td class="text-right" id="totalRevenue">0</td>
                    </tr>

                    {{-- HPP --}}
                    <tr class="lr-section">
                        <td colspan="2">HARGA POKOK PENJUALAN</td>
                    </tr>
                    <tbody id="tbodyHpp"></tbody>

                    <tr class="lr-total">
                        <td>Jumlah HPP</td>
                        <td class="text-right" id="totalHpp">0</td>
                    </tr>

                    {{-- LABA KOTOR --}}
                    <tr class="lr-highlight">
                        <td>LABA KOTOR</td>
                        <td class="text-right" id="grossProfit">0</td>
                    </tr>

                    {{-- BIAYA OPERASIONAL --}}
                    <tr class="lr-section">
                        <td colspan="2">BIAYA OPERASIONAL</td>
                    </tr>
                    <tbody id="tbodyOperational"></tbody>

                    <tr class="lr-total">
                        <td>Jumlah Biaya Operasional</td>
                        <td class="text-right" id="totalOperational">0</td>
                    </tr>

                    {{-- LABA BERSIH --}}
                    <tr class="table-success lr-profit">
                        <td>LABA BERSIH</td>
                        <td class="text-right" id="netProfit">0</td>
                    </tr>

                    </tbody>

                </table>
            </div>
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

        function renderDetail(tbodyId, items) {

            if (!items || !items.length) {
                $(tbodyId).html(`
            <tr>
                <td class="pl-4 text-muted">Tidak ada data</td>
                <td></td>
            </tr>
        `);
                return;
            }

            let html = "";

            items.forEach(function(it) {
                html += `
            <tr>
                <td class="pl-4">${it.coa_code} - ${it.coa_name}</td>
                <td class="text-right">${rupiah(it.amount)}</td>
            </tr>
        `;
            });

            $(tbodyId).html(html);
        }

        function loadLabaRugi() {

            $("#lrLoading").show();

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
                    const hpp = res.hpp || [];
                    const operational = res.operational || [];
                    const sum = res.summary || {};

                    renderDetail("#tbodyRevenue", revenue);
                    renderDetail("#tbodyHpp", hpp);
                    renderDetail("#tbodyOperational", operational);

                    $("#totalRevenue").text(rupiah(sum.total_revenue || 0));
                    $("#totalHpp").text(rupiah(sum.total_hpp || 0));
                    $("#grossProfit").text(rupiah(sum.gross_profit || 0));
                    $("#totalOperational").text(rupiah(sum.total_operational || 0));

                    const net = Number(sum.net_profit || 0);

                    $("#netProfit")
                        .text(rupiah(net))
                        .removeClass("text-danger text-success")
                        .addClass(net < 0 ? "text-danger" : "text-success");
                },
                error: function(xhr) {
                    $("#lrLoading").hide();
                    alert(xhr.responseJSON?.message || "Gagal memuat laporan.");
                }
            });
        }

        $(document).ready(function() {

            let today = new Date();
            let y = today.getFullYear();
            let m = ("0" + (today.getMonth() + 1)).slice(-2);

            $("#start").val(`${y}-${m}-01`);
            $("#end").val(today.toISOString().split('T')[0]);

            $("#btnLoad").on("click", loadLabaRugi);

            loadLabaRugi();
        });
    </script>

@endsection
