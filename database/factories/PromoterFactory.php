<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromoterFactory extends Factory
{
    public function definition(): array
    {
        // Generate test name and email for the Promoter
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}