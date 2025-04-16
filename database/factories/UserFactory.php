<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'use_username' => $this->faker->userName,
            'use_email' => $this->faker->unique()->safeEmail,
            'use_password' => Hash::make('password'), 
            'use_role' => 'user', // ou 'admin' selon le test
        ];
    }
}
