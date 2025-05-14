<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kerjaan;
use App\Models\KerjaanListProses;

class KerjaanListProsesSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            'Sertifikasi WPS' => [1, 2, 3, 4, 5, 6, 5, 7, 8],
            'Sertifikasi WQT' => [1, 2, 3, 4, 5, 6, 7, 8],
            'Sertifikasi Material' => [1, 2, 3, 4, 5, 6, 7, 8],
            'Pengujian Material' => [1, 2, 4, 5, 6, 7, 8],
            'Sertifikasi Kalibrasi' => [1, 2, 6, 7, 5, 7],
        ];

        foreach ($mappings as $kerjaanName => $prosesIds) {
            $kerjaan = Kerjaan::where('nama_kerjaan', $kerjaanName)->first();

            if (!$kerjaan) continue;

            $urutan = 1;
            foreach ($prosesIds as $prosesId) {
                KerjaanListProses::updateOrCreate(
                    [
                        'kerjaan_id' => $kerjaan->id,
                        'list_proses_id' => $prosesId,
                        'urutan' => $urutan,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $urutan++;
            }
        }
    }
}
