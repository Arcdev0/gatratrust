<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationTerm extends Model
{
    use HasFactory;

    protected $table = 'quotation_terms';

    protected $fillable = [
        'quotation_id',
        'description',
    ];

    /**
     * Relasi ke Quotation
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
