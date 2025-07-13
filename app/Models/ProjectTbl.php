<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTbl extends Model
{
    use HasFactory;

    protected $table = 'projects'; // Explicitly define table name

    protected $fillable = [
        'nama_project',
        'no_project',
        'client_id',
        'kerjaan_id',
        'deskripsi',
        'start',
        'end',
        'created_by'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'created_at',
        'updated_at'
    ];


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function administrasiFiles()
    {
        return $this->hasMany(AdministrasiFile::class);
    }

    public function kerjaan()
    {
        return $this->belongsTo(Kerjaan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
