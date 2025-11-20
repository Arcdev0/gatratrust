<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: "DejaVu Sans";
            font-size: 12px;
        }

        @page {
            margin-top: 260px;
            margin-bottom: 80px;
            margin-left: 25px;
            margin-right: 25px;
        }

        .header-page {
            position: fixed;
            top: -240px;
            left: 0;
            right: 0;
            height: 200px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .no-border td {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .watermark {
            position: fixed;
            top: 5%;
            left: 15%;
            width: 70%;
            opacity: 0.08;
            z-index: -1;
            text-align: center;
            /* transform: rotate(-30deg); */
            font-size: 120px;
            color: #000;
        }
    </style>
</head>

<body>
    @php
        $path = public_path('template/img/LOGO_Gatra1.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $base64 = file_exists($path)
            ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
            : '';
    @endphp

    <div class="watermark">
        <img src="{{ $base64 }}">
    </div>

    {{-- HEADER --}}
    <div class="header-page">
        <table style="width:100%;">
            <tr>
                <td style="width:120px;">

                    <img src="{{ $base64 }}" style="width:80px;">
                </td>
                <td>
                    <h2 style="color:#0C6401; margin:0;">PT. GATRA PERDANA TRUSTRUE</h2>
                    <p style="margin:0; font-size:10px;">
                        Calibration Test, Consultant, General Supplier, & IT Consultant for your Business
                        <br> Golden BCI Blok T3 No.12, Bengkong Laut - Batam
                    </p>
                </td>
            </tr>
        </table>

        <hr style="border:1px solid #0C6401; margin:5px 0;">
        <h2 class="text-center" style="margin:4px 0;">INVOICE</h2>

        <table class="no-border" style="font-size:11px;">
            <tr>
                <td style="width:55%; vertical-align:top;">
                    <strong>Customer:</strong> {{ $invoice->customer_name }} <br>
                    <strong>Address:</strong> {{ $invoice->customer_address }} <br>
                </td>

                <td style="width:45%; vertical-align:top;">
                    <strong>Invoice No:</strong> {{ $invoice->invoice_no }} <br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }} <br>
                    <strong>Ref No:</strong> {{ $invoice->no_ref ?? '-' }} <br>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <img src="{{ public_path('template/img/LOGO_Gatra1.png') }}" alt="Logo">
        PT. GATRA PERDANA TRUSTRUE | www.gatraperdanatrustrue.com | Page <span class="pagenum"></span>
    </div>

    {{-- CONTENT --}}
    <div>

        {{-- <h4>Description</h4>
        <div style="margin-bottom:10px;">
           
        </div> --}}

        <table>
            <tr>
                <th style="width:70%;">Description</th>
                <th class="text-right">Amount (IDR)</th>
            </tr>
            <tr>
                <td> {!! $invoice->description !!}</td>
                <td class="text-right">{{ number_format($invoice->gross_total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table class="no-border" style="margin-top:15px;">
            <tr>
                <td class="text-right" style="width:80%;">Gross Total</td>
                <td class="text-right">{{ number_format($invoice->gross_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right">Discount</td>
                <td class="text-right">{{ number_format($invoice->discount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right">Down Payment</td>
                <td class="text-right">{{ number_format($invoice->down_payment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right">Tax</td>
                <td class="text-right">{{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right" style="font-weight:bold;">Net Total</td>
                <td class="text-right" style="font-weight:bold;">
                    {{ number_format($invoice->net_total, 0, ',', '.') }}
                </td>
            </tr>
        </table>


        <br><br><br><br>
        <table width="100%" class="no-border" style="font-size: 13px;">
            <tr>
                <!-- KIRI: Payment Info + Thank You -->
                <td width="60%" valign="top">

                    <!-- Payment Info -->
                    <p style="font-weight: bold; margin-bottom: 3px;">Payment Info</p>
                    <p style="margin:0;">Bank Name &nbsp;&nbsp;: MANDIRI</p>
                    <p style="margin:0;">Account No &nbsp;: 109-00-6666010-1</p>
                    <p style="margin:0;">Atas nama &nbsp;: Gatra Perdana Trastrue</p>

                    <br>

                    <!-- Thank You -->
                    <p style="font-weight: bold; margin-bottom: 0;">Thank you for your trust in using our services.</p>
                    <p style="margin-top: 0;">Hopefully this cooperation will continue to be well–established.</p>

                </td>
                <td width="40%"></td>
            </tr>
            <tr>
                <td width="60%"></td>
                <!-- KANAN: Tanda Tangan -->
                <td width="40%" style="text-align: center;">

                    <p style="margin-bottom: 60px;">
                        Batam, {{ \Carbon\Carbon::now()->format('d F Y') }}
                    </p>

                    <!-- Area Tanda Tangan Dummy -->
                    <div
                        style="
                width: 150px;
                height: 80px;
                border: 1px dashed #888;
                margin: 0 auto 10px auto;
                line-height: 80px;
                color: #777;
                font-size: 12px;
            ">
                        Tanda Tangan
                    </div>

                    <!-- Nama Pejabat Dummy -->
                    <span style="font-weight: bold; text-decoration: underline;">
                        Yogi Suranda
                    </span>
                    <br>
                    <span>Finance Manager</span>

                </td>
            </tr>
        </table>

        <br><br>


    </div>

</body>

</html>
