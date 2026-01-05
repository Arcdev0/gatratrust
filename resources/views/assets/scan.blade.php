<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Asset Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CDN (ikut style kamu) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #025222;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 10px;
        }

        .doc-container {
            max-width: 900px;
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

        .doc-subtitle {
            color: #6c757d;
            margin-top: 6px;
            font-size: 0.95rem;
        }

        .doc-table {
            width: 100%;
            margin-bottom: 20px;
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

        .badge-status {
            background: #e7f6ee;
            color: #0f5132;
            border: 1px solid #b7e4c7;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.85rem;
        }

        .qr-box {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-top: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
            text-align: center;
        }

        .qr-box img {
            max-width: 260px;
            width: 100%;
            height: auto;
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .asset-image {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .info-box {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .action-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
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

            .info-box {
                padding: 12px;
                font-size: 0.9rem;
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
        }
    </style>
</head>

<body>
    <div class="doc-container">
        <div class="doc-header">
            <img src="{{ asset('template/img/Logo_gatra.png') }}" alt="Logo">
            <h3>Informasi Asset</h3>
            <div class="doc-subtitle">Hasil verifikasi QR Code Asset - PT. GATRA PERDANA TRUSTTRUE</div>
        </div>

        <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:10px;">
            <div>
                <div class="fw-bold" style="font-size:1.15rem;">{{ $asset->nama }}</div>
                <div class="text-muted">No Asset: <strong>{{ $asset->no_asset }}</strong></div>
            </div>
            <div>
                <span class="badge-status">Terdeteksi</span>
            </div>
        </div>

        <table class="doc-table mt-3">
            <tr>
                <td><strong>Lokasi</strong></td>
                <td>: {{ $asset->lokasi }}</td>
            </tr>
            <tr>
                <td><strong>Merek</strong></td>
                <td>: {{ $asset->merek ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>No Seri</strong></td>
                <td>: {{ $asset->no_seri ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah</strong></td>
                <td>: {{ $asset->jumlah }}</td>
            </tr>
            <tr>
                <td><strong>Remark</strong></td>
                <td>:
                    @php
                        $r = $asset->remark;
                        $label = $r ? strtoupper(str_replace('_', ' ', $r)) : '-';

                        $cls = 'text-bg-secondary';
                        if ($r === 'baik') {
                            $cls = 'text-bg-success';
                        }
                        if ($r === 'perlu_perbaikan') {
                            $cls = 'text-bg-warning';
                        }
                        if ($r === 'rusak') {
                            $cls = 'text-bg-danger';
                        }
                        if ($r === 'hilang') {
                            $cls = 'text-bg-dark';
                        }
                    @endphp

                    @if ($r)
                        <span class="badge rounded-pill {{ $cls }}">{{ $label }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>

        </table>

        <div class="row g-3">
            <div class="col">
                <div class="qr-box" style="text-align:center;">
                    <div class="fw-bold mb-2">Gambar Asset</div>

                    @if (!empty($asset->url_gambar))
                        <a href="{{ $asset->url_gambar }}" target="_blank" rel="noopener">
                            <img src="{{ $asset->url_gambar }}" alt="Gambar Asset" class="asset-image">
                        </a>
                        <div class="text-muted small mt-2">Tap gambar untuk memperbesar</div>
                    @else
                        <div class="text-muted">Tidak ada gambar</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-box">
            QR Code ini terdaftar pada sistem <strong>GatraTrust</strong>.
            Jika terdapat ketidaksesuaian data asset atau perubahan lokasi, silakan hubungi admin.
            <br><br>
            Dicatat pada: <strong>{{ $asset->created_at?->format('d F Y, H:i') }}</strong>
        </div>
    </div>
</body>

</html>
