<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SertifikatExternal extends Model
{
    use HasFactory;

    protected $table = 'sertifikat_external';

    protected $fillable = [
        'karyawan_id',
        'nama_sertifikat',
        'file_sertifikat'
    ];

    public function karyawan()
    {
        return $this->belongsTo(KaryawanData::class, 'karyawan_id');
    }
}
