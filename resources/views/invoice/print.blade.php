<!DOCTYPE html>
<html>

<head>
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 12pt;
        }

        .page {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: white;
        }

        .field {
            position: absolute;
        }

        .small-text {
            font-size: 10pt;
        }

        .align-right {
            text-align: right;
            width: 40mm;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="page">
        <!-- Invoice To -->
        <div class="field" style="top: 37mm; left: 23mm">
            <span style="font-weight: bold">{{ $invoice->customer_name }}</span><br />
            <span class="small-text">{{ $invoice->customer_address }}</span>
        </div>

        <!-- Invoice No -->
        <div class="field" style="top: 38mm; left: 150mm; font-weight: bold">
            {{ $invoice->invoice_no }}
        </div>

        <!-- Date -->
        <div class="field" style="top: 46mm; left: 150mm">
            {{ \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}
        </div>

        <!-- Deskripsi -->
        <div class="field" style="top: 80mm; left: 10mm; width: 140mm">
            {!! $invoice->description !!}
        </div>

        <!-- Jumlah -->
        <div class="field align-right" style="top: 102mm; left: 160mm;">
            {{ $formatRupiah($invoice->gross_total) }}
        </div>

        <!-- Gross Total -->
        <div class="field align-right" style="top: 193mm; left: 160mm;">
            {{ $formatRupiah($invoice->gross_total) }}
        </div>

        <!-- Discount -->
        <div class="field align-right" style="top: 200mm; left: 160mm;">
            {{ $formatRupiah($invoice->discount) }}
        </div>

        <!-- Down Payment -->
        <div class="field align-right" style="top: 208mm; left: 160mm;">
            {{ $formatRupiah($invoice->down_payment) }}
        </div>

        <!-- Tax -->
        <div class="field align-right" style="top: 215mm; left: 160mm;">
            {{ $formatRupiah($invoice->tax) }}
        </div>

        <!-- Net Total -->
        <div class="field align-right" style="top: 223mm; left: 160mm; font-weight: bold">
            {{ $formatRupiah($invoice->net_total) }}
        </div>
    </div>
</body>

</html>
