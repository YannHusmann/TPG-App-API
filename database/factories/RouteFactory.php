<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition()
    {
        return [
            'rou_code' => $this->faker->randomElement(['1', '12', '25', 'A', 'B']),
        ];
    }
}
