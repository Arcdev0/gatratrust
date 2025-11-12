<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            [
                'kode'      => 'A',
                'nama'      => 'Honorarium',
                'max_cost'  => 70,   // max 70% sesuai dari gambar
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode'      => 'B',
                'nama'      => 'Operational',
                'max_cost'  => 10, // max 10% dari gambar
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode'      => 'C',
                'nama'      => 'Consumable',
                'max_cost'  => 5,   // max 5% dari gambar
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
