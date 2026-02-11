<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_payment',
        'invoice_id',
        'payment_date',
        'amount_paid',
        'wallet_coa_id',
        'note',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function walletCoa()
    {
        return $this->belongsTo(Coa::class, 'wallet_coa_id');
    }
}
