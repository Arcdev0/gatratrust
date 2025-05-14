<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Kerjaan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('kerjaans')->insert([
            [
                'nama_kerjaan' => 'Sertifikasi WPS',
            ],
            [
                'nama_kerjaan' => 'Sertifikasi WQT',
            ],
            [
                'nama_kerjaan' => 'Sertifikasi Material',
            ],
            [
                'nama_kerjaan' => 'Pengujian Material',
            ],
            [
                'nama_kerjaan' => 'Sertifikasi Kalibrasi',
            ],
        ]);
    }
}
