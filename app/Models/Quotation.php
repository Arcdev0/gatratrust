<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotation';

    protected $fillable = [
        'pak_id',
        'quo_no',
        'date',
        'customer_name',
        'customer_address',
        'attention',
        'your_reference',
        'terms',
        'job_no',
        'rev',
        'total_amount',
        'discount',
        'sub_total',
        'payment_terms',
        'bank_account',
        'tax_included',
        'status_id',
        'rejected_reason',
        'signature_token ',
    ];

    // Relasi ke item quotation
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    // Relasi ke scope of work
    public function scopes()
    {
        return $this->hasMany(QuotationScope::class);
    }

    public function terms()
    {
        return $this->hasMany(QuotationTerm::class, 'quotation_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    protected $casts = [
        'date' => 'date',
    ];

    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id');
    }
}
