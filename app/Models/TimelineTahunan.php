<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimelineTahunan extends Model
{
    use HasFactory;

    protected $table = 'timeline_tahunan'; // Nama tabel
    protected $primaryKey = 'id';

    protected $fillable = [
        'tahun',
        'start_date',
        'end_date',
        'description',
        'is_action',
    ];
}