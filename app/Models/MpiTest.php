<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpiTest extends Model
{
    use HasFactory;

    protected $table = 'mpi_tests';

    protected $fillable = [
        'nama_pt',
        'tanggal_running',
        'tanggal_inspection',
        'person',
        'created_by',
    ];

    protected $casts = [
        'tanggal_running' => 'date',
        'tanggal_inspection' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(MpiItem::class, 'mpi_test_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
