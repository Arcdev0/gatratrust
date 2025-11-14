<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakItem extends Model
{
    use HasFactory;

    protected $table = 'pak_items';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'pak_id',
        'category',
        'operational_needs',
        'description',
        'unit_qty',
        'unit_cost',
        'total_cost',
        'max_cost',
        'percent',
        'status',
    ];

    protected $casts = [
        'unit_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'max_cost' => 'decimal:2',
    ];

    /**
     * Relasi ke Pak
     */
    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id', 'id');
    }
}