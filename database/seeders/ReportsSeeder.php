<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reports')->insert([
            [
                'rep_use_id' => 1,
                'rep_sto_id' => "A1",
                'rep_rou_id' => 1,
                'rep_message' => 'Le tram est en retard.'
            ],
            [
                'rep_use_id' => 2,
                'rep_sto_id' => "B1",
                'rep_rou_id' => 2,
                'rep_message' => 'L\'arrêt est sale.'
            ],
        ]);
    }
}
