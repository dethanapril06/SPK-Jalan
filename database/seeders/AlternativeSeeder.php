<?php

namespace Database\Seeders;

use App\Models\Alternative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlternativeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $alternatives = [
            [
                'code' => 'A1',
                'name' => 'Ruas Jalan Merdeka',
                'location' => 'Kecamatan Utara',
                'description' => 'Penghubung antar kelurahan dengan lalu lintas sedang.',
                'order' => 1,
            ],
            [
                'code' => 'A2',
                'name' => 'Ruas Jalan Sudirman',
                'location' => 'Kecamatan Tengah',
                'description' => 'Akses utama menuju pusat layanan publik.',
                'order' => 2,
            ],
            [
                'code' => 'A3',
                'name' => 'Ruas Jalan Diponegoro',
                'location' => 'Kecamatan Timur',
                'description' => 'Jalur distribusi untuk kawasan permukiman padat.',
                'order' => 3,
            ],
            [
                'code' => 'A4',
                'name' => 'Ruas Jalan Veteran',
                'location' => 'Kecamatan Barat',
                'description' => 'Koneksi antar desa dengan beban kendaraan campuran.',
                'order' => 4,
            ],
            [
                'code' => 'A5',
                'name' => 'Ruas Jalan Ahmad Yani',
                'location' => 'Kecamatan Selatan',
                'description' => 'Akses menuju area pendidikan dan kesehatan.',
                'order' => 5,
            ],
        ];

        foreach ($alternatives as $item) {
            Alternative::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'location' => $item['location'],
                    'description' => $item['description'],
                    'order' => $item['order'],
                ]
            );
        }
    }
}
