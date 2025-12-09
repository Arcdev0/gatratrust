<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PAK {{ $pak->pak_number }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Times New Roman", Times, serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        @page {
            margin-top: 230px;
            /* ✅ lebih BESAR dari tinggi header */
            margin-bottom: 80px;
            margin-left: 20px;
            margin-right: 20px;
        }

        .header-page {
            position: fixed;
            top: -230px;
            /* ✅ sama dengan margin-top, tapi negatif */
            left: 0;
            right: 0;
            height: 200px;
            /* ✅ tinggi header real (kurang dari 230)  */
            background: white;
            z-index: 100;
        }


        /* Biar konten mepet ke margin atas halaman (tanpa gap ekstra) */
        .content {
            margin-top: 0;
        }

        .header {
            width: 100%;
            padding-bottom: 4px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 2px 4px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .header-text h1 {
            margin: 0;
            font-size: 16px;
            color: #0C6401;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-text p {
            margin: 1px 0;
            font-size: 9px;
        }

        /* Bar hijau judul dokumen */
        .doc-title-bar {
            margin-top: 4px;
            padding: 6px 8px;
            background-color: #0C6401;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            border-radius: 3px;
        }

        /* Kotak info PAK di header */
        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9px;
        }

        .info-box td {
            border: 0.5px solid #0C6401;
            padding: 3px 4px;
        }

        /* Footer tetap di semua halaman */
        .footer {
            position: fixed;
            bottom: -100px;
            left: 0;
            right: 0;
            height: 80px;
            /* harus sama dengan @page margin-bottom */
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            padding: 5px 0;
            background-color: white;
            z-index: 100;
        }

        .footer img {
            height: 15px;
            margin-right: 5px;
            vertical-align: middle;
            padding-top: 7px;
        }

        .pagenum:before {
            content: counter(page);
        }

        /* Tabel umum */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
            font-size: 11px;
        }

        .no-border td {
            border: none;
        }

        h2 {
            margin: 6px 0 4px 0;
            font-size: 16px;
        }

        /* dst: CSS items-table tetap seperti punya Tuan */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .items-table th,
        .items-table td {
            border: 0.5px solid #000;
            padding: 3px 4px;
        }

        .items-table thead th {
            background: #0b6a3c;
            color: #fff;
            text-align: center;
        }

        .row-category td {
            background: #cde9d6;
            font-weight: bold;
        }

        .row-subtotal td {
            background: #f1f1f1;
        }

        .row-grand td {
            background: #dbdbdb;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge-ok {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            border-radius: 3px;
            background: #28a745;
            color: #fff;
        }

        .badge-over {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            border-radius: 3px;
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- ===== HEADER ===== -->
    <div class="header-page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width:130px;">
                        @php
                            $path = public_path('template/img/LOGO_Gatra1.png');
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $base64 = file_exists($path)
                                ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
                                : '';
                        @endphp
                        <img src="{{ $base64 }}" class="logo" />
                    </td>
                    <td>
                        <div class="header-text">
                            <h1>PT. GATRA PERDANA TRUSTRUE</h1>
                            <p>Calibration Test, Consultant, General Supplier, &amp; IT Consultant for your Business
                            </p>
                            <p>Kawasan Komplek Ruko Golden BCI Blok T3 No. 12 Bengkong Laut,
                                Kec. Bengkong, Kota Batam</p>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="doc-title-bar">
                Proposal Anggaran Kerja (PAK)
            </div>

            {{-- Info PAK di header biar terlihat profesional --}}
            <table class="info-box">
                <tr>
                    <td style="width:60%;">
                        <strong>PAK Number</strong> : {{ $pak->pak_number }}<br>
                        <strong>Project Name</strong> : {{ $pak->pak_name }}<br>
                        <strong>Location</strong> :
                        {{ $pak->location === 'dalam_kota' ? 'Batam (Dalam Kota)' : 'Luar Batam' }}
                    </td>
                    <td style="width:40%;">
                        <strong>Date</strong> :
                        {{ \Carbon\Carbon::parse($pak->date)->format('d M Y') }}<br>
                        <strong>Employees</strong> :
                        @if ($pak->karyawans->count() > 0)
                            <ul style="margin: 3px 0 0 15px; padding: 0;">
                                @foreach ($pak->karyawans as $karyawan)
                                    <li style="font-size: 9px; margin: 0; padding: 0;">
                                        {{ $karyawan->nama_lengkap }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- ===== END HEADER ===== -->

    <!-- Footer -->
    <div class="footer">
        <img src="{{ public_path('template/img/LOGO_Gatra1.png') }}" alt="Logo">
        PT. GATRA PERDANA TRUSTRUE | www.gatraperdanatrustrue.com | Page <span class="pagenum"></span>
    </div>


    <div class="content">

        <h5>DETAIL ITEMS</h5>

        @php
            $projectValue = $pak->pak_value; // nilai total project
            $grandTotal = 0; // total keseluruhan kategori

            // group items berdasarkan category_id
            $groupedItems = $pak->items->groupBy('category_id');
        @endphp

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:30px;">NO</th>
                    <th>Operational Needs</th>
                    <th>Description</th>
                    <th style="width:50px;">Qty</th>
                    <th style="width:90px;">Unit Cost</th>
                    <th style="width:90px;">Total Cost</th>
                    <th style="width:90px;">MAX COST</th>
                    <th style="width:40px;">%</td>
                    <th style="width:60px;">Status</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($groupedItems as $catId => $rows)
                    @php
                        // Ambil kategori dari database (TANPA RELASI)
                        $cat =
                            $categories[$catId] ??
                            (object) [
                                'code' => '-',
                                'name' => 'Uncategorized',
                                'max_percentage' => 0,
                            ];

                        // perhitungan batas maksimal kategori
                        $allowed = $projectValue * ($cat->max_percentage / 100);

                        // perhitungan total per kategori
                        $sectionTotal = $rows->sum('total_cost');

                        // persen pakai nilai project
                        $percent = $projectValue > 0 ? ($sectionTotal / $projectValue) * 100 : 0;

                        // status total kategori
                        $sectionStatus = $sectionTotal > $allowed ? 'OVER' : 'OK';

                        // tambahkan ke grand total
                        $grandTotal += $sectionTotal;
                    @endphp

                    {{-- HEADER KATEGORI --}}
                    <tr class="row-category">
                        <td class="text-center"><strong>{{ $cat->code }}</strong></td>
                        <td colspan="8">
                            <strong>{{ strtoupper($cat->name) }}</strong>
                            (Max {{ $cat->max_percentage }}%)
                        </td>
                    </tr>

                    {{-- DETAIL ITEMS DALAM KATEGORI --}}
                    @foreach ($rows as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">
                                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                            </td>

                            {{-- Kosong di baris item --}}
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach

                    {{-- SUBTOTAL PER KATEGORI --}}
                    <tr class="row-subtotal">
                        <td colspan="5" class="text-right">
                            <strong>TOTAL {{ $cat->code }} (Max {{ $cat->max_percentage }}%)</strong>
                        </td>

                        <td class="text-right">
                            <strong>Rp {{ number_format($sectionTotal, 0, ',', '.') }}</strong>
                        </td>

                        <td class="text-right">
                            <strong>Rp {{ number_format($allowed, 0, ',', '.') }}</strong>
                        </td>

                        <td class="text-center">
                            <strong>{{ number_format($percent, 0) }}%</strong>
                        </td>

                        <td class="text-center">
                            <span class="{{ $sectionStatus == 'OK' ? 'badge-ok' : 'badge-over' }}">
                                {{ $sectionStatus }}
                            </span>
                        </td>
                    </tr>
                @endforeach

                {{-- GRAND TOTAL, PAJAK, & PROFIT --}}
                @php
                    // nilai kontrak / project value dari tabel paks
                    $projectValue = $pak->pak_value;

                    // total pengeluaran semua kategori (dari seluruh section)
                    $totalCost = $grandTotal;

                    // persen pengeluaran terhadap nilai project
                    $grandPercent = $projectValue > 0 ? ($totalCost / $projectValue) * 100 : 0;

                    // persentase pajak dari tabel paks
                    $pphPercent = $pak->pph_23 ?? 0;
                    $ppnPercent = $pak->ppn ?? 0;

                    // nilai pajak (dihitung dari nilai project / kontrak)
                    $pphAmount = $projectValue * ($pphPercent / 100);
                    $ppnAmount = $projectValue * ($ppnPercent / 100);

                    // total beban biaya (pengeluaran + pajak)
                    $grandWithTax = $totalCost + $pphAmount + $ppnAmount;

                    // PROFIT = nilai project - semua pengeluaran - pajak
                    $profit = $projectValue - $grandWithTax;
                    $profitPercent = $projectValue > 0 ? ($profit / $projectValue) * 100 : 0;

                    $profitLabel = $profit >= 0 ? 'OK' : 'OVER';
                    $profitClass = $profit >= 0 ? 'badge-ok' : 'badge-over';
                @endphp



                {{-- NILAI PROJECT / KONTRAK --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>PROJECT VALUE / NILAI KONTRAK (Rp)</strong></td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($projectValue, 0, ',', '.') }}</strong>
                    </td>
                    <td colspan="3"></td>
                </tr>

                {{-- TOTAL PENGELUARAN (DARI ITEMS) --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>Total Pengeluaran (Rp)</strong></td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($totalCost, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>Pengeluaran (%)</strong></td>
                    <td colspan="2" class="text-center">
                        <strong>{{ number_format($grandPercent, 0) }}%</strong>
                    </td>
                </tr>

                {{-- PPh 23 --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right">
                        <strong>PPh 23
                            ({{ rtrim(rtrim(number_format($pphPercent, 2, ',', '.'), '0'), ',') }}%)</strong>
                    </td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($pphAmount, 0, ',', '.') }}</strong>
                    </td>
                    <td colspan="3"></td>
                </tr>

                {{-- PPN --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right">
                        <strong>PPN ({{ rtrim(rtrim(number_format($ppnPercent, 2, ',', '.'), '0'), ',') }}%)</strong>
                    </td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($ppnAmount, 0, ',', '.') }}</strong>
                    </td>
                    <td colspan="3"></td>
                </tr>

                {{-- GRAND TOTAL PENGELUARAN + PAJAK --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>Grand Total Pengeluaran + Pajak (Rp)</strong></td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($grandWithTax, 0, ',', '.') }}</strong>
                    </td>
                    <td colspan="3"></td>
                </tr>

                {{-- PROFIT / LOSS --}}
                <tr class="row-grand">
                    <td colspan="5" class="text-right"><strong>PROFIT (Rp)</strong></td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($profit, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>Profit (%)</strong></td>
                    <td class="text-center">
                        <strong>{{ number_format($profitPercent, 0) }}%</strong>
                    </td>
                    <td class="text-center">
                        <span class="{{ $profitClass }}">
                            {{ $profitLabel }}
                        </span>
                    </td>
                </tr>



            </tbody>
        </table>
    </div>




</body>


</html>
