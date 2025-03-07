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
                'use_username' => 'User1',
                'use_email' => 'user1@example.com',
                'use_password' => Hash::make('password123'),
            ],
            [
                'use_username' => 'User2',
                'use_email' => 'user2@example.com',
                'use_password' => Hash::make('password123'),
            ],
        ]);
    }
}
