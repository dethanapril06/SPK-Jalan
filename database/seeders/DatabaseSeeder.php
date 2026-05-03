<?php

namespace Database\Seeders;

use Database\Seeders\AlternativeSeeder;
use Database\Seeders\AssessmentAspectSeeder;
use Database\Seeders\CriteriaSeeder;
use Database\Seeders\SubCriteriaSeeder;
use Database\Seeders\SurveyorSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CriteriaSeeder::class,
            SubCriteriaSeeder::class,
            AssessmentAspectSeeder::class,
            AlternativeSeeder::class,
            SurveyorSeeder::class,
        ]);
    }
}
