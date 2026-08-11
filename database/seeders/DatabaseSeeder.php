<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Seed dalam urutan yang benar
        $this->call([
            RoleSeeder::class,      // 1. Buat roles dulu
            UserSeeder::class,      // 2. Buat users dengan role
            JadwalSeeder::class,    // 3. Buat jadwal
            MateriSeeder::class,    // 4. Buat materi
            TugasSeeder::class,     // 5. Buat tugas
            DosenSeeder::class,     // 6. Buat data dosen tambahan (jika ada)
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Admin: admin@schuder.ac.id / admin123');
        $this->command->info('Dosen: budi.santoso@schuder.ac.id / dosen123');
        $this->command->info('Mahasiswa: mahasiswa1@student.schuder.ac.id / mahasiswa123');
    }
}
