<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds. Creates 10 Profiles
     */
    public function run(): void
    {
        Profile::factory()
            ->count(10)
            ->create();
    }
}