<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sto_id' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'sto_name' => $this->faker->streetName,
            'sto_municipality' => $this->faker->city,
            'sto_country' => $this->faker->countryCode,
            'sto_latitude' => $this->faker->latitude(46.1, 46.3), 
            'sto_longitude' => $this->faker->longitude(6.1, 6.3),
            'sto_actif' => 'Y',
        ];
    }
}
