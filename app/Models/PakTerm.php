<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'pak_id',
        'description',
        'sort_order',
    ];

    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id');
    }
}
