<table>
    {{-- Judul --}}
    <tr>
        <td colspan="14" style="font-weight:bold; font-size:14px;">
            DATA ASSET
        </td>
    </tr>
    <tr>
        <td colspan="14">Tanggal Export: {{ now()->format('d-m-Y H:i') }}</td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>

    {{-- Header --}}
    <thead>
        <tr>
            <th style="border:1px solid #000; font-weight:bold;">No</th>
            <th style="border:1px solid #000; font-weight:bold;">No Asset</th>
            <th style="border:1px solid #000; font-weight:bold;">Nama</th>
            <th style="border:1px solid #000; font-weight:bold;">Merek</th>
            <th style="border:1px solid #000; font-weight:bold;">No Seri</th>
            <th style="border:1px solid #000; font-weight:bold;">Lokasi</th>

            <th style="border:1px solid #000; font-weight:bold;">Tahun</th>
            {{-- <th style="border:1px solid #000; font-weight:bold;">Remark</th> --}}
            {{-- <th style="border:1px solid #000; font-weight:bold;">URL Gambar</th> --}}
            {{-- <th style="border:1px solid #000; font-weight:bold;">Faktur</th> --}}
            {{-- <th style="border:1px solid #000; font-weight:bold;">Kode Barcode</th> --}}
            <th style="border:1px solid #000; font-weight:bold;">Jumlah</th>
            <th style="border:1px solid #000; font-weight:bold;">Harga</th>
            <th style="border:1px solid #000; font-weight:bold;">Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($assets as $i => $a)
            <tr>
                <td style="border:1px solid #000;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000;">{{ $a->no_asset }}</td>
                <td style="border:1px solid #000;">{{ $a->nama }}</td>
                <td style="border:1px solid #000;">{{ $a->merek ?? '' }}</td>
                <td style="border:1px solid #000;">{{ $a->no_seri ?? '' }}</td>
                <td style="border:1px solid #000;">{{ $a->lokasi }}</td>


                <td style="border:1px solid #000;">{{ $a->tahun_dibeli ?? '' }}</td>
                {{-- <td style="border:1px solid #000;">
                    {{ $a->remark ? strtoupper(str_replace('_', ' ', $a->remark)) : '-' }}
                </td> --}}

                {{-- <td style="border:1px solid #000;">{{ $a->url_gambar ?? '' }}</td> --}}
                {{-- <td style="border:1px solid #000;">{{ $a->faktur_pembelian ?? '' }}</td> --}}
                {{-- <td style="border:1px solid #000;">{{ $a->kode_barcode ?? '' }}</td> --}}

                <td style="border:1px solid #000; text-align:right;">{{ (int) $a->jumlah }}</td>

                {{-- penting: biarkan angka mentah, jangan number_format() biar excel tetap angka --}}
                <td style="border:1px solid #000; text-align:right;">{{ (float) $a->harga }}</td>
                <td style="border:1px solid #000; text-align:right;">{{ (float) $a->total }}</td>

            </tr>
        @endforeach
    </tbody>

    {{-- Footer Total --}}
    <tfoot>
        <tr>
            <td colspan="7" style="border:1px solid #000; font-weight:bold; text-align:right;">
                TOTAL
            </td>
            {{-- <td colspan="5" style="border:1px solid #000;"></td> --}}
            <td style="border:1px solid #000; font-weight:bold; text-align:right;">
                {{ $sumQty }}
            </td>
            <td style="border:1px solid #000; font-weight:bold; text-align:right;">
                {{ $sumHarga }}
            </td>
            <td style="border:1px solid #000; font-weight:bold; text-align:right;">
                {{ $sumTotal }}
            </td>

        </tr>
    </tfoot>
</table>
