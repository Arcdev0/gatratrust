<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SPK {{ $spk->nomor }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            font-size: 11px;
            color: #000;
        }

        @page {
            margin-top: 200px;
            margin-bottom: 95px;
            margin-left: 28px;
            margin-right: 28px;
        }

        .header-page {
            position: fixed;
            top: -185px;
            left: 0;
            right: 0;
            height: 165px;
            border-bottom: 1px solid #2f2f2f;
        }

        .footer-page {
            position: fixed;
            bottom: -85px;
            left: 0;
            right: 0;
            height: 75px;
            border-top: 1px solid #2f2f2f;
            font-size: 9.5px;
            color: #333;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: top;
        }

        .title {
            margin-top: 8px;
            border: 1px solid #1f1f1f;
            text-align: center;
            font-weight: bold;
            padding: 8px 6px;
            font-size: 13px;
            letter-spacing: .3px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .meta td {
            border: 1px solid #1f1f1f;
            padding: 4px 6px;
            font-size: 10px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table td,
        .content-table th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .content-table th {
            background: #f2f2f2;
            text-align: left;
            width: 34%;
        }

        .section-title {
            margin: 14px 0 8px;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #222;
            padding-bottom: 2px;
        }

        .check-row {
            margin-top: 6px;
        }

        .check-item {
            display: inline-block;
            margin-right: 16px;
        }

        .checked {
            font-weight: bold;
        }

        .page:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <div class="header-page">
        <table class="header-table">
            <tr>
                <td style="width: 86px;">
                    @php
                        $path = public_path('template/img/LOGO_Gatra1.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $base64 = file_exists($path)
                            ? 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path))
                            : '';
                    @endphp
                    <img src="{{ $base64 }}" alt="Logo" style="width: 76px;">
                </td>
                <td>
                    <div style="font-size: 16px; font-weight: bold; color:#0c6401;">PT. GATRA PERDANA TRUSTRUE</div>
                    <div style="font-size: 10px;">Calibration Test, Consultant, General Supplier, &amp; IT Consultant</div>
                    <div style="font-size: 10px;">Komp. Ruko Golden BCI Blok T3 No.12 Bengkong Laut, Kota Batam</div>
                </td>
            </tr>
        </table>

        <div class="title">SURAT PERINTAH KERJA / SURAT PERJALANAN DINAS</div>

        <table class="meta">
            <tr>
                <td style="width: 50%;"><strong>Nomor</strong>: {{ $spk->nomor }}</td>
                <td style="width: 50%;"><strong>Tanggal</strong>: {{ optional($spk->tanggal)->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer-page">
        <table style="width:100%; border-collapse: collapse; margin-top: 5px;">
            <tr>
                <td style="border:none; width:60%;">Dokumen SPK/SPD - PT. Gatra Perdana Trustrue</td>
                <td style="border:none; width:40%; text-align:right;">Halaman <span class="page"></span></td>
            </tr>
        </table>
    </div>

    <div class="section-title">I. Data Pegawai</div>
    <table class="content-table">
        <tr>
            <th>Nama</th>
            <td>{{ $spk->pegawai_nama }}</td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td>{{ $spk->pegawai_jabatan }}</td>
        </tr>
        <tr>
            <th>Divisi</th>
            <td>{{ $spk->pegawai_divisi ?: '-' }}</td>
        </tr>
        <tr>
            <th>NIK / ID Pegawai</th>
            <td>{{ $spk->pegawai_nik_id ?: '-' }}</td>
        </tr>
    </table>

    <div class="section-title">II. Data Perjalanan Dinas</div>
    <table class="content-table">
        <tr>
            <th>Tujuan Dinas</th>
            <td>{{ $spk->tujuan_dinas }}</td>
        </tr>
        <tr>
            <th>Perusahaan Tujuan</th>
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
            <th>Tanggal Berangkat</th>
            <td>{{ optional($spk->tanggal_berangkat)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Kembali</th>
            <td>{{ optional($spk->tanggal_kembali)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Lama Perjalanan</th>
            <td>{{ $spk->lama_perjalanan }} hari</td>
        </tr>
        <tr>
            <th>Moda Transportasi</th>
            <td>
                <div class="check-row">
                    <span class="check-item {{ $spk->moda_transportasi === 'darat' ? 'checked' : '' }}">{{ $spk->moda_transportasi === 'darat' ? '☑' : '☐' }} Darat</span>
                    <span class="check-item {{ $spk->moda_transportasi === 'laut' ? 'checked' : '' }}">{{ $spk->moda_transportasi === 'laut' ? '☑' : '☐' }} Laut</span>
                    <span class="check-item {{ $spk->moda_transportasi === 'udara' ? 'checked' : '' }}">{{ $spk->moda_transportasi === 'udara' ? '☑' : '☐' }} Udara</span>
                </div>
            </td>
        </tr>
        <tr>
            <th>Sumber Biaya</th>
            <td>{{ $spk->sumber_biaya ?: '-' }}</td>
        </tr>
        <tr>
            <th>Opsi Sumber Biaya</th>
            <td>
                <div class="check-row">
                    <span class="check-item {{ $spk->sumber_biaya_opsi === 'perusahaan' ? 'checked' : '' }}">{{ $spk->sumber_biaya_opsi === 'perusahaan' ? '☑' : '☐' }} Perusahaan</span>
                    <span class="check-item {{ $spk->sumber_biaya_opsi === 'project' ? 'checked' : '' }}">{{ $spk->sumber_biaya_opsi === 'project' ? '☑' : '☐' }} Project</span>
                    <span class="check-item {{ $spk->sumber_biaya_opsi === 'lainnya' ? 'checked' : '' }}">{{ $spk->sumber_biaya_opsi === 'lainnya' ? '☑' : '☐' }} Lainnya</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">III. Persetujuan Penugasan</div>
    <table class="content-table">
        <tr>
            <th>Ditugaskan Oleh</th>
            <td>{{ $spk->ditugaskan_oleh_nama }}</td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td>{{ $spk->ditugaskan_oleh_jabatan }}</td>
        </tr>
        <tr>
            <th>Tanda Tangan</th>
            <td style="height: 70px; vertical-align: bottom;"><strong>( {{ $spk->ditugaskan_oleh_nama }} )</strong></td>
        </tr>
    </table>
</body>

</html>
