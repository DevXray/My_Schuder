<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ FIRST: Seed roles (required for users)
        $this->call([
            RoleSeeder::class,
        ]);

        // ✅ Get role IDs
        $adminRole = Role::where('name', 'admin')->first();
        $dosenRole = Role::where('name', 'dosen')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        // ✅ Create test admin user
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin User',
                    'password' => bcrypt('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // ✅ Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $mahasiswaRole?->id,
        ]);

        // ✅ Then seed other data
        $this->call([
            DosenSeeder::class,
            MateriSeeder::class,
            TugasSeeder::class,
        ]);
    }
}