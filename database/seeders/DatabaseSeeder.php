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
        Role::all(); // Force reload

        // ✅ Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => 1, // admin
                'email_verified_at' => now(),
            ]
        );

        // ✅ Create test users
        User::firstOrCreate(
            ['email' => 'dosen@example.com'],
            [
                'name' => 'Test Dosen',
                'password' => Hash::make('password'),
                'role_id' => 2, // dosen
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'mahasiswa@example.com'],
            [
                'name' => 'Test Mahasiswa',
                'password' => Hash::make('password'),
                'role_id' => 3, // mahasiswa
                'email_verified_at' => now(),
            ]
        );

        // ✅ Then seed other data
        $this->call([
            DosenSeeder::class,
            MateriSeeder::class,
            TugasSeeder::class,
        ]);
    }
}