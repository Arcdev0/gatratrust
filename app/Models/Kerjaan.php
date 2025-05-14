<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kerjaan extends Model
{
     use HasFactory;

    protected $fillable = ['nama_kerjaan'];


     public function prosesList() {
        return $this->belongsToMany(ListProses::class, 'kerjaan_list_proses')
                    ->withPivot('urutan')
                    ->orderBy('kerjaan_list_proses.urutan');
    }
}
