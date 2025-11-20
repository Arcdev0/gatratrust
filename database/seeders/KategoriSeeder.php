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
        $rows = [
            [
                'code' => 'A',
                'name' => 'Honorarium',
                'max_percentage' => 58,
                'order' => 1,
            ],
            [
                'code' => 'B',
                'name' => 'Operational',
                'max_percentage' => 6,
                'order' => 2,
            ],
            [
                'code' => 'C',
                'name' => 'Consumable',
                'max_percentage' => 5,
                'order' => 3,
            ],
            [
                'code' => 'D',
                'name' => 'Building',
                'max_percentage' => 7,
                'order' => 4,
            ],
            [
                'code' => 'E',
                'name' => 'Karyawan',
                'max_percentage' => 14,
                'order' => 4,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('categories')->updateOrInsert(
                ['code' => $row['code']], // unique key to match
                array_merge($row, [
                    'updated_at' => now(),
                    // only set created_at when inserting — updateOrInsert akan
                    // men-set created_at only for insert if you include it here,
                    // but using now() for both is fine:
                    'created_at' => now(),
                ])
            );
        }
    }
}
