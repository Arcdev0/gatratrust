<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListProses;

class ProsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listProses = [
            ['nama_proses' => 'Quotation'],
            ['nama_proses' => 'Persetujuan harga'],
            ['nama_proses' => 'Invoice DP'],
            ['nama_proses' => 'Permohonan Third party'],
            ['nama_proses' => 'Pembuatan dokumen'],
            ['nama_proses' => 'Proses pengujian'],
            ['nama_proses' => 'Invoice lunas'],
            ['nama_proses' => 'Berita acara'],
        ];

        foreach ($listProses as $proses) {
            ListProses::updateOrCreate(
                ['nama_proses' => $proses['nama_proses']],
                $proses
            );
        }
    }
}
