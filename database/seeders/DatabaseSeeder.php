// database/seeders/DatabaseSeeder.php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ FIRST: Seed roles
        $this->call([
            RoleSeeder::class,
        ]);

        // ✅ Refresh role cache
        $adminRole = Role::where('name', 'admin')->first();
        $dosenRole = Role::where('name', 'dosen')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        // ✅ Create admin user dengan ROLE_ID yang benar
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // ✅ PASTIKAN role_id admin = 1
        $admin->role_id = $adminRole->id;
        $admin->save();

        // ✅ Create test users
        $dosen = User::firstOrCreate(
            ['email' => 'dosen@example.com'],
            [
                'name' => 'Test Dosen',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $dosen->role_id = $dosenRole->id;
        $dosen->save();

        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@example.com'],
            [
                'name' => 'Test Mahasiswa',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $mahasiswa->role_id = $mahasiswaRole->id;
        $mahasiswa->save();

        // Then seed other data
        $this->call([
            DosenSeeder::class,
            MateriSeeder::class,
            TugasSeeder::class,
        ]);
        
        // ✅ Log hasil
        $this->command->info('✅ Admin user role: ' . $admin->getRoleName());
        $this->command->info('✅ Dosen user role: ' . $dosen->getRoleName());
        $this->command->info('✅ Mahasiswa user role: ' . $mahasiswa->getRoleName());
    }
}