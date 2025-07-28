<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingFile extends Model
{
    protected $fillable = [
        'accounting_id',
        'file_name',
        'file_path'
    ];

    public function accounting()
    {
        return $this->belongsTo(Accounting::class);
    }
}

