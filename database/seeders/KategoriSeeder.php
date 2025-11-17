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
        DB::table('categories')->updateOrInsert([
            [
                'code'      => 'A',
                'name'      => 'Honorarium',
                'max_percentage'  => 70,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code'      => 'B',
                'name'      => 'Operational',
                'max_percentage'  => 10,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code'      => 'C',
                'name'      => 'Consumable',
                'max_percentage'  => 5,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
