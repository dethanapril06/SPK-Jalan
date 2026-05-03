<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\SubCriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCriteriaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $criteriaMap = Criteria::query()
            ->whereIn('code', ['K1', 'K2', 'K3', 'K4'])
            ->pluck('id', 'code');

        $subCriteria = [
            ['criteria_code' => 'K1', 'code' => 'K1.1', 'name' => 'Kemiringan', 'order' => 1],
            ['criteria_code' => 'K1', 'code' => 'K1.2', 'name' => '% Penurunan', 'order' => 2],
            ['criteria_code' => 'K1', 'code' => 'K1.3', 'name' => 'Erosi Permukaan', 'order' => 3],

            ['criteria_code' => 'K2', 'code' => 'K2.1', 'name' => 'Ukuran Terbanyak', 'order' => 1],
            ['criteria_code' => 'K2', 'code' => 'K2.2', 'name' => 'Tebal Lapisan', 'order' => 2],
            ['criteria_code' => 'K2', 'code' => 'K2.3', 'name' => 'Distribusi Kerikil', 'order' => 3],

            ['criteria_code' => 'K3', 'code' => 'K3.1', 'name' => 'Jumlah Lubang', 'order' => 1],
            ['criteria_code' => 'K3', 'code' => 'K3.2', 'name' => 'Ukuran Lubang', 'order' => 2],
            ['criteria_code' => 'K3', 'code' => 'K3.3', 'name' => 'Bekas Roda', 'order' => 3],
            ['criteria_code' => 'K3', 'code' => 'K3.4', 'name' => 'Bergelombang', 'order' => 4],

            ['criteria_code' => 'K4', 'code' => 'K4.1', 'name' => 'Kondisi Bahu', 'order' => 1],
            ['criteria_code' => 'K4', 'code' => 'K4.2', 'name' => 'Permukaan Bahu', 'order' => 2],
            ['criteria_code' => 'K4', 'code' => 'K4.3', 'name' => 'Kondisi Saluran Samping', 'order' => 3],
            ['criteria_code' => 'K4', 'code' => 'K4.4', 'name' => 'Kerusakan Lereng', 'order' => 4],
            ['criteria_code' => 'K4', 'code' => 'K4.5', 'name' => 'Trotoar', 'order' => 5],
        ];

        foreach ($subCriteria as $item) {
            $criteriaId = $criteriaMap[$item['criteria_code']] ?? null;

            if (! $criteriaId) {
                continue;
            }

            SubCriteria::updateOrCreate(
                ['code' => $item['code']],
                [
                    'criteria_id' => $criteriaId,
                    'name' => $item['name'],
                    'order' => $item['order'],
                ]
            );
        }
    }
}
