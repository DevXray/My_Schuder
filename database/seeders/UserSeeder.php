<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@schuder.ac.id',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // Admin
            'email_verified_at' => now(),
        ]);

        Admin::create([
            'user_id' => $admin->id,
            'nip' => 'ADM001',
            'phone' => '081234567890',
        ]);

        $this->command->info('✅ Admin user created: admin@schuder.ac.id / admin123');

        // 2. Create Dosen Users
        $dosen1 = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi.santoso@schuder.ac.id',
            'password' => Hash::make('dosen123'),
            'role_id' => 2, // Dosen
            'email_verified_at' => now(),
        ]);

        Dosen::create([
            'user_id' => $dosen1->id,
            'nip' => 'DSN001',
            'phone' => '081234567891',
            'bidang_keahlian' => 'Pemrograman Web',
        ]);

        $dosen2 = User::create([
            'name' => 'Dr. Siti Nurhaliza',
            'email' => 'siti.nurhaliza@schuder.ac.id',
            'password' => Hash::make('dosen123'),
            'role_id' => 2, // Dosen
            'email_verified_at' => now(),
        ]);

        Dosen::create([
            'user_id' => $dosen2->id,
            'nip' => 'DSN002',
            'phone' => '081234567892',
            'bidang_keahlian' => 'Database Management',
        ]);

        $this->command->info('✅ 2 Dosen users created: dosen123');

        // 3. Create Mahasiswa Users
        for ($i = 1; $i <= 5; $i++) {
            $mahasiswa = User::create([
                'name' => "Mahasiswa $i",
                'email' => "mahasiswa$i@student.schuder.ac.id",
                'password' => Hash::make('mahasiswa123'),
                'role_id' => 3, // Mahasiswa
                'email_verified_at' => now(),
            ]);

            Mahasiswa::create([
                'user_id' => $mahasiswa->id,
                'nim' => 'MHS' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone' => '0812345678' . (92 + $i),
                'jurusan' => $i % 2 == 0 ? 'Teknik Informatika' : 'Sistem Informasi',
                'angkatan' => 2024,
            ]);
        }

        $this->command->info('✅ 5 Mahasiswa users created: mahasiswa123');
    }
}
