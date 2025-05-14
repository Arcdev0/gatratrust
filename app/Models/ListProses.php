<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListProses extends Model
{
     use HasFactory;

    protected $fillable = ['nama_proses'];

      public function files() {
        return $this->hasMany(ListProsesFile::class);
    }

    public function kerjaans() {
        return $this->belongsToMany(Kerjaan::class, 'kerjaan_list_proses');
    }
}
