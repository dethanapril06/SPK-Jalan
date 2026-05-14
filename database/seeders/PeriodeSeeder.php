<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssessmentPeriod;
use App\Models\User;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'admin')->first();
        $userId = $user->id;

        $periodes = [
            [
                'code' => 'P0001',
                'name' => 'Periode Tahun 2026',
                'year' => '2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'active',
                'created_by_user_id' => $userId,
            ]
        ];

        foreach ($periodes as $item) {
            AssessmentPeriod::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'year' => $item['year'],
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'],
                    'status' => $item['status'],
                    'created_by_user_id' => $item['created_by_user_id'],
                ]
            );
        }
    }
}
