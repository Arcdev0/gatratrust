<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KaryawanData extends Model
{
    use HasFactory;

    protected $table = 'karyawan_data';

    protected $fillable = [
        'no_karyawan',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_lengkap',
        'jabatan_id',
        'status',
        'nomor_telepon',
        'email',
        'nomor_identitas',
        'status_perkawinan',
        'kewarganegaraan',
        'agama',
        'pekerjaan',
        'doh',
        'foto'
    ];

    public function sertifikatInhouse()
    {
        return $this->hasMany(SertifikatInhouse::class, 'karyawan_id');
    }

    public function sertifikatExternal()
    {
        return $this->hasMany(SertifikatExternal::class, 'karyawan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
}
