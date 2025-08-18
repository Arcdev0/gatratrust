<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quo_no }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            /* biar support simbol/✔ */
            font-size: 12px;
        }

        .header {
            width: 100%;
            border-bottom: 3px solid #008000;
            /* garis bawah hijau */
            padding-bottom: 10px;
            /* margin-bottom: 15px; */
        }

        .header-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            /* pastikan tidak ada garis */
            vertical-align: middle;
        }

        .logo {
            width: 120px;
            /* ukuran logo tetap besar */
            height: auto;
        }

        .header-text h1 {
            margin: 0;
            font-size: 18px;
            color: #008000;
            /* hijau */
        }

        .header-text p {
            margin: 2px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .no-border td {
            border: none;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .info-table .label {
            width: 20%;
            font-weight: bold;
        }

        .info-table .value {
            width: 30%;
        }

        .info-table .right {
            width: 20%;
            font-weight: bold;
        }

        .info-table .right-value {
            width: 30%;
        }
    </style>
</head>

<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:130px;">
                    <?php
                    $path = public_path('template/img/Logo_gatra.png');
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    if (file_exists($path)) {
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        $base64 = '';
                    }
                    ?>
                    <img src="<?php echo $base64; ?>" alt="logo" class="logo" />
                </td>
                <td>
                    <div class="header-text">
                        <h1>PT. GATRA PERDANA TRUSTURE</h1>
                        <p>Calibration Test, Consultant, General Supplier, & Digital Agency for your Business</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h2 style="text-align:center;">QUOTATION</h2>

    <table class="info-table no-border">
        <tr>
            <td class="label">Customer</td>
            <td class="value">{{ $quotation->customer_name }}</td>
            <td class="right">Quo No</td>
            <td class="right-value">{{ $quotation->quo_no }}</td>
        </tr>
        <tr>
            <td class="label">Address</td>
            <td class="value">{{ $quotation->customer_address }}</td>
            <td class="right">Date</td>
            <td class="right-value">{{ \Carbon\Carbon::parse($quotation->date)->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Attn</td>
            <td class="value">{{ $quotation->attention }}</td>
            <td class="right">Rev</td>
            <td class="right-value">{{ $quotation->rev }}</td>
        </tr>
        <tr>
            <td class="label">Your Reference</td>
            <td class="value">{{ $quotation->your_reference ?? '-' }}</td>
            <td class="right">Job No</td>
            <td class="right-value">{{ $quotation->job_no ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Terms</td>
            <td class="value">{{ $quotation->terms ?? '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price (IDR)</th>
                <th>Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="no-border">
        <tr>
            <td class="text-right" style="width:80%;">Total</td>
            <td class="text-right">{{ number_format($quotation->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-right">Discount</td>
            <td class="text-right">{{ number_format($quotation->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-right"><b>Sub Total</b></td>
            <td class="text-right"><b>{{ number_format($quotation->sub_total, 0, ',', '.') }}</b></td>
        </tr>
    </table>

    <h4>Scope of Work</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Description</th>
                <th>PT. GPT</th>
                <th>PT. SLA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->scopes as $i => $scope)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $scope->description }}</td>
                    <td class="text-center">{{ $scope->responsible_pt_gpt ? '✔' : '' }}</td>
                    <td class="text-center">{{ $scope->responsible_client ? '✔' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><b>Terms and Conditions:</b><br>
        a). Payment: 50% before start working.<br>
        Bank: {{ $quotation->bank_account }}<br>
        b). Quotation not including Tax.
    </p>

    <table style="width:100%; margin-top:50px;" class="no-border">
        <br><br>
        <table style="width:100%; margin-top:50px;" class="no-border">
            <tr>
                <td style="width:50%; text-align:left;">
                    <p>Approval,</p>
                    <br><br><br><br>
                    <p><b>{{ $quotation->approval_name ?? '__________________' }}</b></p>
                </td>
                {{-- <td style="width:50%;">
                    <p>Prepared By</p>
                    <br><br><br><br>
                    <p><b>{{ $quotation->prepared_by ?? '__________________' }}</b></p>
                </td> --}}
            </tr>
        </table>
</body>

</html>
