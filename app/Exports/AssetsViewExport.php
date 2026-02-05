<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssetsViewExport implements FromView, WithTitle
{
    public function view(): View
    {
        $assets = Asset::query()
            ->select([
                'id',
                'no_asset',
                'nama',
                'merek',
                'no_seri',
                'lokasi',
                'jumlah',
                'harga',
                'total',
                'url_gambar',
                'faktur_pembelian',
                'tahun_dibeli',
                'remark',
                'kode_barcode',
                'created_at',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('exports.assets', [
            'assets'   => $assets,
            'sumQty'   => (int) $assets->sum('jumlah'),
            'sumHarga' => (float) $assets->sum('harga'),
            'sumTotal' => (float) $assets->sum('total'),
        ]);
    }

    public function title(): string
    {
        return 'Data Asset';
    }
}
