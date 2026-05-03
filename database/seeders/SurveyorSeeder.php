<?php

namespace Database\Seeders;

use App\Models\Surveyor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveyorSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $surveyors = [
            [
                'email' => 'surveyor1@spkjalan.com',
                'code' => 'S1',
                'phone' => '081200000001',
            ],
            [
                'email' => 'surveyor2@spkjalan.com',
                'code' => 'S2',
                'phone' => '081200000002',
            ],
            [
                'email' => 'surveyor3@spkjalan.com',
                'code' => 'S3',
                'phone' => '081200000003',
            ],
        ];

        foreach ($surveyors as $item) {
            $user = User::query()
                ->where('email', $item['email'])
                ->where('role', 'surveyor')
                ->first();

            if (! $user) {
                continue;
            }

            Surveyor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'code' => $item['code'],
                    'phone' => $item['phone'],
                    'is_active' => true,
                ]
            );
        }
    }
}
