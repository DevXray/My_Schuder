<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run()
    {
        DB::table('dosens')->insert([
            [
                'nama' => 'Dr. Andi Rahman',
                'nidn' => '12345678',
                'email' => 'andi@kampus.ac.id',
                'password' => Hash::make('rahasia123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Prof. Budi Santoso',
                'nidn' => '87654321',
                'email' => 'budi@kampus.ac.id',
                'password' => Hash::make('rahasia123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Ir. Dimas Pratama',
                'nidn' => '44332211',
                'email' => 'dimas@kampus.ac.id',
                'password' => Hash::make('rahasia123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Evi Lestari',
                'nidn' => '55667788',
                'email' => 'evi@kampus.ac.id',
                'password' => Hash::make('rahasia123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Fahmi Rahman',
                'nidn' => '99887766',
                'email' => 'fahmi@kampus.ac.id',
                'password' => Hash::make('rahasia123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}