<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SPK {{ $spk->nomor }}</title>
    <style>
        @page {
            margin: 30px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .divider {
            border-top: 2px solid #000;
            margin-bottom: 14px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
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
    <div class="title">SURAT PERINTAH KERJA (SPK)</div>
    <div class="divider"></div>

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
</body>

</html>
