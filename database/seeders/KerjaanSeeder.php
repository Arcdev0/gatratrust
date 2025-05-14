<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kerjaan;

class KerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kerjaans = [
            ['nama_kerjaan' => 'Sertifikasi WPS'],
            ['nama_kerjaan' => 'Sertifikasi WQT'],
            ['nama_kerjaan' => 'Sertifikasi Material'],
            ['nama_kerjaan' => 'Pengujian Material'],
            ['nama_kerjaan' => 'Sertifikasi Kalibrasi'],
        ];

        foreach ($kerjaans as $kerjaan) {
            Kerjaan::updateOrCreate(
                ['nama_kerjaan' => $kerjaan['nama_kerjaan']],
                $kerjaan
            );
        }
    }
}
