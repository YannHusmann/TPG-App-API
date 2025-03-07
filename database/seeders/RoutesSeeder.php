<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('routes')->insert([
            ['rou_code' => 'T1', 'rou_name' => 'Tram 1', 'rou_type' => 'tram'],
            ['rou_code' => 'B2', 'rou_name' => 'Bus 2', 'rou_type' => 'bus'],
        ]);
    }
}
