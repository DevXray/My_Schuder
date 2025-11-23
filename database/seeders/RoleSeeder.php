<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Create default roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Super user dengan akses penuh ke semua fitur',
            ],
            [
                'name' => 'dosen',
                'display_name' => 'Dosen',
                'description' => 'Pengajar yang dapat mengelola materi dan tugas',
            ],
            [
                'name' => 'mahasiswa',
                'display_name' => 'Mahasiswa',
                'description' => 'Siswa yang dapat mengakses materi dan mengerjakan tugas',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                ]
            );
        }

        echo "\n✅ Roles seeded successfully!\n";
    }
}
