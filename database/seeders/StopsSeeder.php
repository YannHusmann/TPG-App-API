<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StopsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('stops')->insert([
            ['sto_id' => 'A1', 'sto_name' => 'Gare Cornavin', 'sto_municipality' => 'Lancy', 'sto_country' => 'CH', 'sto_latitude' => 46.2044, 'sto_longitude' => 6.1432],
            ['sto_id' => 'B1', 'sto_name' => 'Bel-Air', 'sto_municipality' => 'Genève', 'sto_country' => 'CH', 'sto_latitude' => 46.2075, 'sto_longitude' => 6.1457],
        ]);
    }
}
