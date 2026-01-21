<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = [
        'accounting_setting_id',
        'coa_id',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(AccountingSetting::class, 'accounting_setting_id');
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
