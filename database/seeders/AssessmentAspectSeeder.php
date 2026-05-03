<?php

namespace Database\Seeders;

use App\Models\AssessmentAspect;
use App\Models\SubCriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentAspectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $subCriteriaMap = SubCriteria::query()
            ->whereIn('code', ['K1.1', 'K1.2', 'K1.3', 'K2.1', 'K2.2', 'K2.3', 'K3.1', 'K3.2', 'K3.3', 'K3.4', 'K4.1', 'K4.2', 'K4.3', 'K4.4', 'K4.5'])
            ->pluck('id', 'code');

        $aspects = [
            // K1.1 - Kemiringan
            ['sub_criteria_code' => 'K1.1', 'name' => '>5%', 'value' => 4, 'order' => 1],
            ['sub_criteria_code' => 'K1.1', 'name' => '3-5%', 'value' => 3, 'order' => 2],
            ['sub_criteria_code' => 'K1.1', 'name' => 'Cekung', 'value' => 2, 'order' => 3],
            ['sub_criteria_code' => 'K1.1', 'name' => 'Rata', 'value' => 1, 'order' => 4],

            // K1.2 - % Penurunan
            ['sub_criteria_code' => 'K1.2', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K1.2', 'name' => '<10% luas', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K1.2', 'name' => '10-30% luas', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K1.2', 'name' => '>30% luas', 'value' => 4, 'order' => 4],

            // K1.3 - Erosi Permukaan
            ['sub_criteria_code' => 'K1.3', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K1.3', 'name' => '<10% luas', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K1.3', 'name' => '10-30% luas', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K1.3', 'name' => '>30% luas', 'value' => 4, 'order' => 4],

            // K2.1 - Ukuran Terbanyak
            ['sub_criteria_code' => 'K2.1', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K2.1', 'name' => '<1 cm', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K2.1', 'name' => '1-5 cm', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K2.1', 'name' => '>5 cm', 'value' => 4, 'order' => 4],
            ['sub_criteria_code' => 'K2.1', 'name' => 'tidak tentu', 'value' => 5, 'order' => 5],

            // K2.2 - Tebal Lapisan
            ['sub_criteria_code' => 'K2.2', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K2.2', 'name' => '<5 cm', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K2.2', 'name' => '5-10 cm', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K2.2', 'name' => '10-20 cm', 'value' => 4, 'order' => 4],
            ['sub_criteria_code' => 'K2.2', 'name' => '>20 cm', 'value' => 5, 'order' => 5],

            // K2.3 - Distribusi Kerikil
            ['sub_criteria_code' => 'K2.3', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K2.3', 'name' => 'Rata', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K2.3', 'name' => 'Tidak rata', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K2.3', 'name' => 'Gundukan memanjang', 'value' => 4, 'order' => 4],

            // K3.1 - Jumlah Lubang
            ['sub_criteria_code' => 'K3.1', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K3.1', 'name' => '<2/200m', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K3.1', 'name' => '3-10/200m', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K3.1', 'name' => '>10/200m', 'value' => 4, 'order' => 4],

            // K3.2 - Ukuran Lubang
            ['sub_criteria_code' => 'K3.2', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K3.2', 'name' => 'Kecil-dangkal', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K3.2', 'name' => 'Kecil-dalam', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K3.2', 'name' => 'Besar-dangkal', 'value' => 4, 'order' => 4],
            ['sub_criteria_code' => 'K3.2', 'name' => 'Besar-dalam', 'value' => 5, 'order' => 5],

            // K3.3 - Bekas Roda
            ['sub_criteria_code' => 'K3.3', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K3.3', 'name' => '<1 cm dalam', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K3.3', 'name' => '1-3 cm dalam', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K3.3', 'name' => '>3 cm dalam', 'value' => 4, 'order' => 4],

            // K3.4 - Bergelombang
            ['sub_criteria_code' => 'K3.4', 'name' => 'Tidak ada', 'value' => 1, 'order' => 1],
            ['sub_criteria_code' => 'K3.4', 'name' => '<10% luas', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K3.4', 'name' => '10-30% luas', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K3.4', 'name' => '>30% luas', 'value' => 4, 'order' => 4],

            // K4.1 - Kondisi Bahu
            ['sub_criteria_code' => 'K4.1', 'name' => 'Baik/Rata', 'value' => 4, 'order' => 1],
            ['sub_criteria_code' => 'K4.1', 'name' => 'Bekas roda/erosi ringan', 'value' => 3, 'order' => 2],
            ['sub_criteria_code' => 'K4.1', 'name' => 'Berat', 'value' => 2, 'order' => 3],
            ['sub_criteria_code' => 'K4.1', 'name' => 'tidak ada', 'value' => 1, 'order' => 4],

            // K4.2 - Permukaan Bahu
            ['sub_criteria_code' => 'K4.2', 'name' => 'Rata dengan jalan', 'value' => 5, 'order' => 1],
            ['sub_criteria_code' => 'K4.2', 'name' => 'di atas permukaan jalan', 'value' => 4, 'order' => 2],
            ['sub_criteria_code' => 'K4.2', 'name' => 'di bawah', 'value' => 3, 'order' => 3],
            ['sub_criteria_code' => 'K4.2', 'name' => '>10 cm bawah', 'value' => 2, 'order' => 4],
            ['sub_criteria_code' => 'K4.2', 'name' => 'tidak ada', 'value' => 1, 'order' => 5],

            // K4.3 - Kondisi Saluran Samping
            ['sub_criteria_code' => 'K4.3', 'name' => 'Bersih', 'value' => 4, 'order' => 1],
            ['sub_criteria_code' => 'K4.3', 'name' => 'erosi', 'value' => 3, 'order' => 2],
            ['sub_criteria_code' => 'K4.3', 'name' => 'tersumbat', 'value' => 2, 'order' => 3],
            ['sub_criteria_code' => 'K4.3', 'name' => 'tidak ada', 'value' => 1, 'order' => 4],

            // K4.4 - Kerusakan Lereng
            ['sub_criteria_code' => 'K4.4', 'name' => 'Tidak ada', 'value' => 4, 'order' => 1],
            ['sub_criteria_code' => 'K4.4', 'name' => 'longsor', 'value' => 1, 'order' => 2],

            // K4.5 - Trotoar
            ['sub_criteria_code' => 'K4.5', 'name' => 'Baik/aman', 'value' => 3, 'order' => 1],
            ['sub_criteria_code' => 'K4.5', 'name' => 'tidak ada', 'value' => 2, 'order' => 2],
            ['sub_criteria_code' => 'K4.5', 'name' => 'berbahaya', 'value' => 1, 'order' => 3],
        ];

        foreach ($aspects as $item) {
            $subCriteriaId = $subCriteriaMap[$item['sub_criteria_code']] ?? null;

            if (! $subCriteriaId) {
                continue;
            }

            AssessmentAspect::updateOrCreate(
                [
                    'sub_criteria_id' => $subCriteriaId,
                    'name' => $item['name'],
                ],
                [
                    'value' => $item['value'],
                    'order' => $item['order'],
                ]
            );
        }
    }
}
