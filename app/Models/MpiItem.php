<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MpiItem extends Model
{
    use HasFactory;

    protected $table = 'mpi_items';

    protected $fillable = [
        'mpi_test_id',
        'nama_jurulas',
        'foto_jurulas',
        'foto_ktp',
        'proses_las',
        'foto_sebelum',
        'foto_during',
        'foto_hasil',
        'foto_sebelum_mpi',
        'foto_setelah_mpi',
    ];

    protected $casts = [
        // jika nanti butuh cast tertentu, tambahkan di sini
    ];

    /**
     * Relasi ke parent test/run
     */
    public function test()
    {
        return $this->belongsTo(MpiTest::class, 'mpi_test_id');
    }

    /**
     * Relasi ke materials (one-to-many)
     */
    public function materials()
    {
        return $this->hasMany(MpiItemMaterial::class, 'mpi_item_id');
    }

    /**
     * Relasi ke posisi (one-to-many)
     */
    public function posisis()
    {
        return $this->hasMany(MpiItemPosisi::class, 'mpi_item_id');
    }

    // --- Accessor helpers untuk URL foto (Storage::url) ---
    public function getFotoJurulasUrlAttribute()
    {
        return $this->foto_jurulas ? Storage::url($this->foto_jurulas) : null;
    }

    public function getFotoKtpUrlAttribute()
    {
        return $this->foto_ktp ? Storage::url($this->foto_ktp) : null;
    }

    public function getFotoSebelumUrlAttribute()
    {
        return $this->foto_sebelum ? Storage::url($this->foto_sebelum) : null;
    }

    public function getFotoDuringUrlAttribute()
    {
        return $this->foto_during ? Storage::url($this->foto_during) : null;
    }

    public function getFotoHasilUrlAttribute()
    {
        return $this->foto_hasil ? Storage::url($this->foto_hasil) : null;
    }

    public function getFotoSebelumMpiUrlAttribute()
    {
        return $this->foto_sebelum_mpi ? Storage::url($this->foto_sebelum_mpi) : null;
    }

    public function getFotoSetelahMpiUrlAttribute()
    {
        return $this->foto_setelah_mpi ? Storage::url($this->foto_setelah_mpi) : null;
    }
}
