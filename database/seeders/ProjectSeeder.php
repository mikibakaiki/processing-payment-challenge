<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds. Creates 10 Projects.
     */
    public function run(): void
    {
        Project::factory()
            ->count(10)
            ->create();
    }
}