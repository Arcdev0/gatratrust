<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingSetting extends Model
{
    protected $table = 'accounting_settings';
    public $timestamps = true;

    protected $fillable = [

        'default_ar_coa_id',
        'default_ap_coa_id',
        'default_sales_coa_id',
        'default_tax_payable_coa_id',


        'default_expense_coa_id',
        'default_expense_honorarium_coa_id',
        'default_expense_operational_coa_id',
        'default_expense_consumable_coa_id',
        'default_expense_building_coa_id',
        'default_expense_other_coa_id',


        'default_suspense_coa_id',
        'default_retained_earning_coa_id',

        'journal_prefix',
        'journal_running_number',
        'fiscal_year_start_month',
    ];

    protected $casts = [
        // 'default_wallet_id' => 'integer',
        'journal_running_number' => 'integer',
        'fiscal_year_start_month' => 'integer',
    ];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'accounting_setting_id');
    }

    public function defaultAR(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_ar_coa_id');
    }

    public function defaultSales(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_sales_coa_id');
    }

    public function defaultTaxPayable(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_tax_payable_coa_id');
    }

    public function defaultExpense(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_coa_id');
    }

    public function defaultSuspense(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_suspense_coa_id');
    }

    public function retainedEarning(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_retained_earning_coa_id');
    }

    public function defaultExpenseHonorarium(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_honorarium_coa_id');
    }
    public function defaultExpenseOperational(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_operational_coa_id');
    }
    public function defaultExpenseConsumable(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_consumable_coa_id');
    }
    public function defaultExpenseBuilding(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_building_coa_id');
    }
    public function defaultExpenseOther(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'default_expense_other_coa_id');
    }
}
