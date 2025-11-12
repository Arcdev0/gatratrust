<?php

namespace App\Models;

use Database\Seeders\KategoriSeeder;
use Illuminate\Database\Eloquent\Model;

class PakItem extends Model
{
    protected $table = 'pak_items';
    protected $primaryKey = 'pak_id';
    
    protected $fillable = [
        'pak_id',
        'category_id',
        'name',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'remarks'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id', 'pak_id');
    }

    public function category()
    {
        return $this->belongsTo(KategoriSeeder::class, 'category_id', 'kode');
    }
}