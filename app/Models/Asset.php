<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'no_asset',
        'nama',
        'merek',
        'no_seri',
        'lokasi',
        'jumlah',
        'harga',
        'total',
        'url_gambar',
        'url_barcode',
        'kode_barcode',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga'  => 'decimal:2',
        'total'  => 'decimal:2',
    ];

    // Auto hitung total setiap save
    protected static function booted()
    {
        static::saving(function ($asset) {
            $jumlah = (int) ($asset->jumlah ?? 0);
            $harga  = (float) ($asset->harga ?? 0);
            $asset->total = $jumlah * $harga;
        });
    }
}
