<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'tanggal',
        'pegawai_nama',
        'pegawai_jabatan',
        'pegawai_divisi',
        'pegawai_nik_id',
        'tujuan_dinas',
        'lokasi_perusahaan_tujuan',
        'alamat_lokasi',
        'maksud_ruang_lingkup',
        'tanggal_berangkat',
        'tanggal_kembali',
        'lama_perjalanan',
        'sumber_biaya',
        'moda_transportasi',
        'sumber_biaya_opsi',
        'ditugaskan_oleh_nama',
        'ditugaskan_oleh_jabatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_berangkat' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }
}
