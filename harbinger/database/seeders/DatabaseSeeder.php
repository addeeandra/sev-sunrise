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
        DB::connectUsing('pgsql', [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'username' => 'ludwig',
            'password' => 'some-random-password-8891',
            'database' => 'harbingerdb'
        ]);
    }
}
