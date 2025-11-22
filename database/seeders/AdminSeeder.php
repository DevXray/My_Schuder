<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        // Admin 1
        $user1 = User::firstOrCreate(
            ['email' => 'admin@myschuder.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
            ]
        );

        Admin::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'nip' => 'ADM001',
                'phone' => '081234567890',
                'department' => 'IT Management',
            ]
        );

        // Admin 2
        $user2 = User::firstOrCreate(
            ['email' => 'admin2@myschuder.com'],
            [
                'name' => 'Admin Sistem',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
            ]
        );

        Admin::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'nip' => 'ADM002',
                'phone' => '081234567891',
                'department' => 'Academic Affairs',
            ]
        );
    }
}
