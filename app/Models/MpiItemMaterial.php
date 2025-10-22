<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpiItemMaterial extends Model
{
    use HasFactory;

    protected $table = 'mpi_items_material';

    protected $fillable = [
        'mpi_item_id',
        'nama_material',
    ];

    public function item()
    {
        return $this->belongsTo(MpiItem::class, 'mpi_item_id');
    }
}
