@extends('layout.app')

@section('title', 'Neraca')




@section('content')


    <style>
        .nr-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .nr-head {
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
            margin-bottom: 8px;
        }

        .kpi strong {
            font-weight: 700;
        }

        .nr-table thead th {
            background: #f8f9fa;
            font-size: 12px;
            font-weight: 600;
        }

        .nr-loading {
            display: none;
            padding: 10px 15px;
            background: #fff3cd;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
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
                <h4 class="font-weight-bold text-primary">Neraca</h4>
                <div class="text-muted small">Posisi keuangan (Aset = Liabilitas + Ekuitas)</div>
            </div>
        </div>

        {{-- FILTER + KPI --}}
        <div class="nr-card mb-3">
            <div class="nr-head">
                <span>Filter Tanggal</span>
                <div>
                    <input type="date" id="date" class="form-control form-control-sm d-inline-block"
                        style="width:160px;">
                    <button class="btn btn-sm btn-primary" id="btnLoad">Tampilkan</button>
                </div>
            </div>

            <div class="p-3">
                <div class="kpi">Total Aset: <strong id="kpiAssets">Rp 0,00</strong></div>
                <div class="kpi">Total Liabilitas + Ekuitas: <strong id="kpiLE">Rp 0,00</strong></div>
                <div class="kpi">Selisih: <strong id="kpiDiff">Rp 0,00</strong></div>

                <div class="mt-2 d-none" id="balanceAlert"></div>
            </div>

            <div class="nr-loading text-center" id="nrLoading">Memuat data...</div>
        </div>

        {{-- 2 PANEL --}}
        <div class="row">

            {{-- ASET --}}
            <div class="col-lg-6 mb-3">
                <div class="nr-card">
                    <div class="nr-head">
                        <span>ASET</span>
                        <small class="text-muted">Akun normal DEBIT</small>
                    </div>
                    <div class="p-3 table-responsive">
                        <table class="table table-sm table-bordered nr-table">
                            <thead>
                                <tr>
                                    <th width="120">Kode</th>
                                    <th>Nama Akun</th>
                                    <th width="160" class="text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAssets">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total Aset</th>
                                    <th class="text-right" id="tfootAssets">Rp 0,00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- LIABILITAS + EKUITAS --}}
            <div class="col-lg-6 mb-3">
                <div class="nr-card">
                    <div class="nr-head">
                        <span>LIABILITAS + EKUITAS</span>
                        <small class="text-muted">Akun normal CREDIT</small>
                    </div>
                    <div class="p-3 table-responsive">
                        <table class="table table-sm table-bordered nr-table">
                            <thead>
                                <tr>
                                    <th width="120">Kode</th>
                                    <th>Nama Akun</th>
                                    <th width="160" class="text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyLE">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total Liabilitas + Ekuitas</th>
                                    <th class="text-right" id="tfootLE">Rp 0,00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- WARNING default_posisi tidak kebaca --}}
        <div class="alert alert-warning d-none" id="unknownBox">
            <b>Perhatian:</b> Ada akun yang `default_posisi`-nya tidak terklasifikasi (bukan DEBIT/CREDIT).
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

        function renderRows(tbodySelector, items) {
            if (!items || !items.length) {
                $(tbodySelector).html(`<tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>`);
                return;
            }
            let html = '';
            items.forEach(function(it) {
                html += `
            <tr>
                <td>${escapeHtml(it.coa_code)}</td>
                <td>${escapeHtml(it.coa_name)}</td>
                <td class="text-right font-weight-bold">${rupiah(it.amount)}</td>
            </tr>
        `;
            });
            $(tbodySelector).html(html);
        }

        function setBalanceAlert(isBalanced, diff) {
            const $box = $("#balanceAlert");
            $box.removeClass("d-none alert-success alert-danger");

            if (isBalanced) {
                $box.addClass("alert alert-success")
                    .html(`<b>Seimbang.</b> Aset sama dengan Liabilitas + Ekuitas.`);
            } else {
                $box.addClass("alert alert-danger")
                    .html(`<b>Tidak Seimbang.</b> Selisih: <b>${rupiah(diff)}</b>. Cek jurnal atau mapping akun.`);
            }
        }

        function loadNeraca() {
            $("#nrLoading").show();
            $("#unknownBox").addClass("d-none");

            $.ajax({
                url: "{{ route('laporan.neraca.data') }}",
                method: "GET",
                dataType: "json",
                data: {
                    date: $("#date").val() || ''
                },
                success: function(res) {
                    $("#nrLoading").hide();

                    const assets = res.assets || [];
                    const le = res.liabilities_equity || [];
                    const sum = res.summary || {};

                    renderRows("#tbodyAssets", assets);
                    renderRows("#tbodyLE", le);

                    $("#kpiAssets").text(rupiah(sum.total_assets || 0));
                    $("#kpiLE").text(rupiah(sum.total_liabilities_equity || 0));
                    $("#kpiDiff").text(rupiah(sum.difference || 0));

                    $("#tfootAssets").text(rupiah(sum.total_assets || 0));
                    $("#tfootLE").text(rupiah(sum.total_liabilities_equity || 0));

                    setBalanceAlert(!!sum.is_balanced, sum.difference || 0);

                    // unknown posisi
                    const unknown = res.unknown || [];
                    if (unknown.length) {
                        let text = unknown.map(u =>
                            `${u.coa_code} - ${u.coa_name} (posisi: ${u.posisi || '-'})`).join(", ");
                        $("#unknownText").text(text);
                        $("#unknownBox").removeClass("d-none");
                    }
                },
                error: function(xhr) {
                    $("#nrLoading").hide();
                    alert(xhr.responseJSON?.message || "Gagal memuat neraca.");
                }
            });
        }

        $(document).ready(function() {
            // default tanggal hari ini
            const today = new Date();
            $("#date").val(today.toISOString().split('T')[0]);

            $("#btnLoad").on("click", loadNeraca);

            // auto load
            loadNeraca();
        });
    </script>
@endsection
