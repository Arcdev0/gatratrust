<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation Approval Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #025222;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 10px;
        }

        .doc-container {
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 25px 20px;
            background-color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .doc-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .doc-header img {
            height: 70px;
            margin-bottom: 15px;
        }

        .doc-header h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            color: #025222;
        }

        .doc-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .doc-table tr {
            border-bottom: 1px solid #f1f1f1;
        }

        .doc-table td {
            padding: 10px 8px;
            vertical-align: top;
        }

        .doc-table tr:last-child {
            border-bottom: none;
        }

        .signature-box {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-top: 25px;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
        }

        .signature-box img {
            height: 65px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .signature-note {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            line-height: 1.5;
        }

        /* Mobile-specific improvements */
        @media (max-width: 576px) {
            body {
                padding: 5px;
            }

            .doc-container {
                margin: 10px auto;
                padding: 20px 15px;
                border-radius: 10px;
            }

            .doc-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }

            .doc-header img {
                height: 60px;
                margin-bottom: 12px;
            }

            .doc-header h3 {
                font-size: 1.2rem;
            }

            .doc-table td {
                padding: 8px 5px;
                font-size: 0.95rem;
            }

            .doc-table td:first-child {
                width: 35%;
                min-width: 120px;
            }

            .signature-box {
                flex-direction: column;
                text-align: center;
                padding: 15px 10px;
            }

            .signature-box img {
                margin-right: 0;
                margin-bottom: 12px;
                height: 55px;
            }

            .signature-note {
                padding: 12px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            .doc-header h3 {
                font-size: 1.1rem;
            }

            .doc-table td {
                font-size: 0.9rem;
                padding: 7px 4px;
            }

            .signature-box,
            .signature-note {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="doc-container">
        <div class="doc-header">
            <img src="{{ asset('template/img/Logo_gatra.png') }}" alt="Logo">
            <h3>Verifikasi Tanda Tangan Elektronik - Invoice</h3>
        </div>

        <table class="doc-table">
            <tr>
                <td><strong>Nomor Dokumen</strong></td>
                <td>: {{ $invoice->invoice_no }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Dokumen</strong></td>
                <td>: {{ \Carbon\Carbon::parse($invoice->date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Customer</strong></td>
                <td>: {{ $invoice->customer_name }}</td>
            </tr>
            <tr>
                <td><strong>Perihal</strong></td>
                <td>: Invoice Approval</td>
            </tr>
            <tr>
                <td><strong>Pengirim</strong></td>
                <td>: {{ $approval['approver_name'] ?? '-' }} | {{ $approval['approver_position'] ?? '-' }}</td>
            </tr>
        </table>

        <div class="signature-box">
            <img src="{{ asset('template/img/Logo_gatra.png') }}" alt="Logo">
            <div>
                Ditandatangani secara elektronik oleh: <br>
                <strong>{{ $approval['approver_name'] ?? '-' }}</strong><br>
                {{ $approval['approver_position'] ?? '-' }}<br>
                PT. GATRA PERDANA TRUSTRUE
            </div>
        </div>

        <div class="signature-note">
            Dokumen ini telah ditandatangani secara elektronik menggunakan sistem Gatrust oleh:
            <strong>{{ $approval['approver_name'] ?? '-' }}</strong>,
            {{ $approval['approver_position'] ?? '-' }} <br>
            pada {{ $approval['approval_date'] ?? '-' }}
        </div>
    </div>
</body>

</html>
