<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'description',
        'responsible_pt_gpt',
        'responsible_client',
    ];

    // Relasi ke quotation
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
