<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $fillable = [
        'no_jurnal',
        'tipe_jurnal',
        'tanggal',     // ditambahkan
        'deskripsi',
        'total',
        'debit',       // ditambahkan
        'credit'       // ditambahkan
    ];

    public function files()
    {
        return $this->hasMany(AccountingFile::class);
    }
}

