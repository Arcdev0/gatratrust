<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiFile extends Model
{
    use HasFactory;

    protected $table = 'administrasi_files';

    // Kolom yang boleh diisi
    protected $fillable = [
        'project_id',
        'file_name',
        'file_path',
        'uploaded_at',
    ];

    protected $dates = [
        'uploaded_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(ProjectTbl::class);
    }
}
