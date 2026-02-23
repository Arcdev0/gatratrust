<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SPK {{ $spk->nomor }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            margin: 0;
            font-size: 11px;
        }

        @page {
            margin: 160px 32px 100px 32px;
        }

        .header-page {
            position: fixed;
            top: -140px;
            left: 0;
            right: 0;
            height: 130px;
            border-bottom: 2px solid #222;
        }

        .footer-page {
            position: fixed;
            bottom: -85px;
            left: 0;
            right: 0;
            height: 75px;
            border-top: 1px solid #222;
            font-size: 10px;
            color: #444;
        }

        .table-head,
        .table-content {
            width: 100%;
            border-collapse: collapse;
        }

        .table-content td,
        .table-content th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .title-bar {
            margin-top: 4px;
            background: #0c6401;
            color: #fff;
            text-align: center;
            font-weight: bold;
            padding: 6px;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="header-page">
        <table class="table-head">
            <tr>
                <td style="width: 90px;">
                    @php
                        $path = public_path('template/img/LOGO_Gatra1.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $base64 = file_exists($path)
                            ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
                            : '';
                    @endphp
                    <img src="{{ $base64 }}" alt="Logo" style="width: 78px; height: auto;">
                </td>
                <td>
                    <div style="font-size: 15px; font-weight: bold; color: #0c6401;">PT. GATRA PERDANA TRUSTRUE</div>
                    <div style="font-size: 10px;">Calibration Test, Consultant, General Supplier, &amp; IT Consultant for your Business</div>
                    <div style="font-size: 10px;">Kawasan Komplek Ruko Golden BCI Blok T3 No. 12 Bengkong Laut, Kota Batam</div>
                </td>
            </tr>
        </table>
        <div class="title-bar">SURAT PERINTAH KERJA (SPK/SPD)</div>
    </div>

    <div class="footer-page">
        <table style="width: 100%; margin-top: 6px; border-collapse: collapse;">
            <tr>
                <td style="border: none; width: 60%;">Dokumen ini dicetak otomatis oleh sistem Gatratrust.</td>
                <td style="border: none; width: 40%;" class="text-right">Halaman <span class="page"></span></td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 6px; border-collapse: collapse;">
            <tr>
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; width: 40%; text-center;">
                    <div>{{ $spk->ditugaskan_oleh_jabatan }}</div>
                    <br><br><br>
                    <div><strong>{{ $spk->ditugaskan_oleh_nama }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table-content">
        <tr>
            <th style="width: 32%;">Nomor SPK</th>
            <td>{{ $spk->nomor }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ optional($spk->tanggal)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Nama Pegawai</th>
            <td>{{ $spk->pegawai_nama }}</td>
        </tr>
        <tr>
            <th>Jabatan / Divisi</th>
            <td>{{ $spk->pegawai_jabatan }}{{ $spk->pegawai_divisi ? ' / ' . $spk->pegawai_divisi : '' }}</td>
        </tr>
        <tr>
            <th>NIK / ID Pegawai</th>
            <td>{{ $spk->pegawai_nik_id ?: '-' }}</td>
        </tr>
        <tr>
            <th>Tujuan Dinas</th>
            <td>{{ $spk->tujuan_dinas }}</td>
        </tr>
        <tr>
            <th>Lokasi Perusahaan Tujuan</th>
            <td>{{ $spk->lokasi_perusahaan_tujuan ?: '-' }}</td>
        </tr>
        <tr>
            <th>Alamat Lokasi</th>
            <td>{{ $spk->alamat_lokasi ?: '-' }}</td>
        </tr>
        <tr>
            <th>Maksud / Ruang Lingkup</th>
            <td>{{ $spk->maksud_ruang_lingkup ?: '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Perjalanan</th>
            <td>{{ optional($spk->tanggal_berangkat)->format('d-m-Y') }} s/d {{ optional($spk->tanggal_kembali)->format('d-m-Y') }}
                ({{ $spk->lama_perjalanan }} hari)</td>
        </tr>
        <tr>
            <th>Moda Transportasi</th>
            <td>{{ ucfirst($spk->moda_transportasi) }}</td>
        </tr>
        <tr>
            <th>Sumber Biaya</th>
            <td>{{ $spk->sumber_biaya ?: '-' }} / {{ ucfirst($spk->sumber_biaya_opsi) }}</td>
        </tr>
    </table>
</body>

</html>
