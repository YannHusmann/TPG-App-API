<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rep_use_id' => User::factory(),
            'rep_sto_id' => Stop::factory()->create()->sto_id,
            'rep_rou_id' => null,
            'rep_message' => $this->faker->sentence,
            'rep_status' => 'envoyé',
            'rep_type' => $this->faker->randomElement([
                'vitre cassée',
                'graffiti',
                'éclairage défectueux',
                'mobilier endommagé',
                'panne d’affichage',
                'déchets / saleté'
            ]),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
