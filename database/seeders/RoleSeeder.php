<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // ✅ Truncate table to reset IDs
        Role::truncate();
        
        // ✅ Insert roles with specific IDs
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Super user dengan akses penuh ke semua fitur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'dosen',
                'display_name' => 'Dosen',
                'description' => 'Pengajar yang dapat mengelola materi dan tugas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'mahasiswa',
                'display_name' => 'Mahasiswa',
                'description' => 'Siswa yang dapat mengakses materi dan mengerjakan tugas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        // ✅ Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Roles seeded successfully with IDs: 1=admin, 2=dosen, 3=mahasiswa');
    }
}