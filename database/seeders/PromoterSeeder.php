<?php

namespace Database\Seeders;

use App\Models\Promoter;
use Illuminate\Database\Seeder;

class PromoterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promoter::factory()
            ->count(10)
            ->create();
    }
}