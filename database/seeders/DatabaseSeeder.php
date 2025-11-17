<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserAndRoleSeeder::class);
        $this->call([ProsesSeeder::class,]);
        $this->call([KerjaanSeeder::class,]);
        $this->call([KerjaanListProsesSeeder::class,]);
        $this->call([StatusSeeder::class,]);
        $this->call([KategoriSeeder::class,]);
    }
}
