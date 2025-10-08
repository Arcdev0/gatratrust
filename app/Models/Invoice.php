<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'no_ref',
        'invoice_type',
        'date',
        'project_id',
        'customer_name',
        'customer_address',
        'description',
        'gross_total',
        'discount',
        'down_payment',
        'tax',
        'net_total',
        'status'
    ];

    // Relasi ke tabel pembayaran
    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getRemainingAttribute()
    {
        return $this->net_total - $this->total_paid;
    }

    public function getTotalInvoiceAttribute()
    {
        return $this->invoices()->sum('net_total');
    }

    public function getIsLunasAttribute()
    {
        return $this->total_invoice >= $this->total_biaya_project;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }

}
