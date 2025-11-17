<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakItem extends Model
{
    use HasFactory;

    protected $table = 'pak_items';
    protected $primaryKey = 'id';

    // kolom yang boleh di-mass-assignment
    protected $fillable = [
        'pak_id',
        'category_id',
        'name',
        'description',
        'unit',
        'quantity',
        'unit_cost',
        'total_cost',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_cost'  => 'integer',
        'total_cost' => 'integer',
        'category_id'=> 'integer',
        'pak_id'     => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Relasi ke Pak
     */
    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id', 'id');
    }
}
