<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'use_username' => 'Yann Husmann',
                'use_email' => 'yann.husmann@gmail.com',
                'use_password' => Hash::make('Password123'),
            ],
        ]);
    }
}
