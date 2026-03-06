<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'pak_id',
        'description',
        'responsible_pt_gpt',
        'responsible_client',
        'sort_order',
    ];

    protected $casts = [
        'responsible_pt_gpt' => 'boolean',
        'responsible_client' => 'boolean',
    ];

    public function pak()
    {
        return $this->belongsTo(Pak::class, 'pak_id');
    }
}
