<?php

namespace Database\Seeders;

use App\Models\Criteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'K1',
                'name' => 'Permukaan Kerasa',
                'weight' => 0.30,
                'order' => 1,
            ],
            [
                'code' => 'K2',
                'name' => 'Kerikil/Batu',
                'weight' => 0.20,
                'order' => 2,
            ],
            [
                'code' => 'K3',
                'name' => 'Kerusakan Lain',
                'weight' => 0.30,
                'order' => 3,
            ],
            [
                'code' => 'K4',
                'name' => 'Bahu, Saluran Samping dan lain-lain',
                'weight' => 0.20,
                'order' => 4,
            ],
        ];

        foreach ($criteria as $item) {
            Criteria::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'weight' => $item['weight'],
                    'order' => $item['order'],
                ]
            );
        }
    }
}
