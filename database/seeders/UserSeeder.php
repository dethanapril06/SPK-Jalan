<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin SPK Jalan',
                'email' => 'admin@spkjalan.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kepala Dinas',
                'email' => 'kepaladinas@spkjalan.com',
                'password' => bcrypt('password'),
                'role' => 'kepala_dinas',
            ],
            [
                'name' => 'Surveyor 1',
                'email' => 'surveyor1@spkjalan.com',
                'password' => bcrypt('password'),
                'role' => 'surveyor',
            ],
            [
                'name' => 'Surveyor 2',
                'email' => 'surveyor2@spkjalan.com',
                'password' => bcrypt('password'),
                'role' => 'surveyor',
            ],
            [
                'name' => 'Surveyor 3',
                'email' => 'surveyor3@spkjalan.com',
                'password' => bcrypt('password'),
                'role' => 'surveyor',
            ],
        ];

        foreach ($users as $item) {
            User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => $item['password'],
                    'role' => $item['role'],
                ]
            );
        }
    }
}
