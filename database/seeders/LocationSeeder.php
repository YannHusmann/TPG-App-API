<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('locationUser')->insert([
            ['loc_use_id' => 1, 'loc_latitude' => 46.2044, 'loc_longitude' => 6.1432],
            ['loc_use_id' => 2, 'loc_latitude' => 46.2075, 'loc_longitude' => 6.1457],
        ]);
    }
}
