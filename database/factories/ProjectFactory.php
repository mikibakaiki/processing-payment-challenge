<?php

namespace Database\Factories;

use App\Models\Promoter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        // Generate test data for the Project. The max wallet amount is 100000.
        return [
            'name' => $this->faker->words(3, true),
            'wallet_balance' => $this->faker->randomFloat(2, 0, 100000),
            'promoter_id' => function () {
                return Promoter::factory()->create()->id;
            },
        ];
    }
}