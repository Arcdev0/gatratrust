<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'date',
        'customer_name',
        'customer_address',
        'gross_total',
        'discount',
        'down_payment',
        'tax',
        'net_total',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
