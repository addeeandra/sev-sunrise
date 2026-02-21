<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('demo')->insert([
            'code' => 'P-2048',
            'name' => 'Barang 2048-bit',
            'price' => fake()->numerify(str_repeat('#', 617)),
        ]);

        DB::table('demo')->insert([
            'code' => 'P-4096',
            'name' => 'Barang 4096-bit',
            'price' => fake()->numerify(str_repeat('#', 1233)),
        ]);
    }
}
