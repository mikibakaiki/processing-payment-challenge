<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        // Generate test name and email for the Profile
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}