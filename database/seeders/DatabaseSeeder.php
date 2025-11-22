<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles dulu
            RoleSeeder::class,
            
            // 2. Dosen & Mahasiswa (existing seeder)
            DosenSeeder::class,
            // MahasiswaSeeder::class, // jika ada
            
            // 3. Update existing users dengan role
            UpdateExistingUsersSeeder::class,
            
            // 4. Admin
            AdminSeeder::class,
            
            // 5. Mata Kuliah
            MatkulSeeder::class,
            
            // 6. Materi & Tugas (existing seeder)
            MateriSeeder::class,
            TugasSeeder::class,
        ]);

        $this->command->info('🎉 All seeders completed successfully!');
    }
}