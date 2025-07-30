<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $fillable = ['nama_jabatan'];

    public function syaratJabatan()
    {
        return $this->hasMany(SyaratJabatan::class, 'jabatan_id');
    }

    public function karyawan()
    {
        return $this->hasMany(KaryawanData::class, 'jabatan_id');
    }
}