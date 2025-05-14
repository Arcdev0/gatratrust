<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListProsesFile extends Model
{
    protected $table = 'list_proses_files';

    // Kolom yang bisa diisi secara mass assignment
    protected $fillable = [
        'list_proses_id',
        'nama_file',
    ];

    // Relasi ke ListProses
    public function proses(): BelongsTo
    {
        return $this->belongsTo(ListProses::class, 'list_proses_id');
    }
}
