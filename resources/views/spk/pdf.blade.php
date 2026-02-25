<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SPK {{ $spk->nomor }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Times New Roman", Times, serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        @page {
            margin-top: 190px;
            margin-bottom: 80px;
            margin-left: 20px;
            margin-right: 20px;
        }

        .header-page {
            position: fixed;
            top: -190px;
            left: 0;
            right: 0;
            height: 160px;
            background: white;
            z-index: 100;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #080808;
            padding-bottom: 5px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 2px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .header-text h1 {
            margin: 0;
            font-size: 16px;
            color: #0C6401;
        }

        .header-text p {
            margin: 1px 0;
            font-size: 9px;
        }

        .footer {
            position: fixed;
            bottom: -100px;
            left: 0;
            right: 0;
            height: 80px;
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

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 4px 0;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 8px 0;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            font-size: 11px;
        }

        .label-col {
            width: 30%;
            font-weight: bold;
            background: #f5f5f5;
        }

        .check-table td {
            width: 50%;
        }

        .check {
            font-weight: bold;
            margin-right: 6px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header-page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width:130px;">
                        @php
                            $path = public_path('template/img/LOGO_Gatra1.png');
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $base64 = file_exists($path) ? 'data:image/'.$type.';base64,'.base64_encode(file_get_contents($path)) : '';
                        @endphp
                        <img src="{{ $base64 }}" alt="logo" class="logo" />
                    </td>
                    <td>
                        <div class="header-text">
                            <h1>PT. GATRA PERDANA TRUSTRUE</h1>
                            <p>Calibration Test, Consultant, General Supplier, &amp; Digital Agency for your Business</p>
                            <p>Kawasan Komplek Ruko Golden BCI Blok T3 No. 12 Bengkong Laut, Kec. Bengkong, Kota Batam</p>
                        </div>
                    </td>
                </tr>
            </table>

            <hr style="border: 1px solid #0C6401; margin: 4px 0 6px 0;">
            <div class="title">SURAT PERINTAH KERJA (SPK)</div>
        </div>
    </div>

    <div class="footer">
        <img src="{{ public_path('template/img/LOGO_Gatra1.png') }}" alt="Logo">
        PT. GATRA PERDANA TRUSTRUE | www.gatraperdanatrustrue.com | Page <span class="pagenum"></span>
    </div>

    <div class="content">
        <div class="section-title">I. INFORMASI SPK</div>
        <table>
            <tr>
                <td class="label-col">Nomor SPK</td>
                <td>{{ $spk->nomor }}</td>
            </tr>
            <tr>
                <td class="label-col">Tanggal</td>
                <td>{{ optional($spk->tanggal)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <div class="section-title">II. DATA PROJECT</div>
        <table>
            <tr>
                <td class="label-col">Nama Project</td>
                <td>{{ $spk->project?->nama_project ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">No. Project</td>
                <td>{{ $spk->project?->no_project ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Pekerjaan</td>
                <td>{{ $spk->project?->kerjaan?->nama_kerjaan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Client</td>
                <td>{{ $spk->project?->client?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Deskripsi</td>
                <td>{{ $spk->project?->deskripsi ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">III. CAKUPAN DATA PROYEK</div>
        <table class="check-table">
            @php
                $selected = $spk->data_proyek ?? [];
                $chunks = array_chunk($dataProyekOptions, 2, true);
            @endphp
            @foreach ($chunks as $chunk)
                <tr>
                    @foreach ($chunk as $key => $label)
                        <td>
                            <span class="check">{{ in_array($key, $selected, true) ? '✓' : '□' }}</span>
                            {{ $label }}
                        </td>
                    @endforeach
                    @if (count($chunk) === 1)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>

        <div class="section-title">IV. PENGESAHAN</div>
        <table>
            <tr>
                <th class="text-center">Dibuat Oleh</th>
                <th class="text-center">Diketahui Oleh</th>
                <th class="text-center">Disetujui Oleh</th>
            </tr>
            <tr>
                <td style="height:80px;"></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</body>

</html>
