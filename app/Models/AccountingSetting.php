<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingSetting extends Model
{
    protected $table = 'accounting_settings';
    public $timestamps = true;

    protected $fillable = [
        'default_cash_coa_id',
        'default_bank_coa_id',
        'default_suspense_coa_id',
        'default_retained_earning_coa_id',
        'journal_prefix',
        'journal_running_number',
        'fiscal_year_start_month',
    ];

    protected $casts = [
        'journal_running_number' => 'integer',
        'fiscal_year_start_month' => 'integer',
    ];

    public function defaultCash(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_cash_coa_id');
    }

    public function defaultBank(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_bank_coa_id');
    }

    public function defaultSuspense(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_suspense_coa_id');
    }

    public function retainedEarning(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_retained_earning_coa_id');
    }
}
