<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpiItemPosisi extends Model
{
    use HasFactory;

    protected $table = 'mpi_items_posisi';

    protected $fillable = [
        'mpi_item_id',
        'nama_posisi',
    ];

    public function item()
    {
        return $this->belongsTo(MpiItem::class, 'mpi_item_id');
    }
}
