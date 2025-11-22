<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        DB::table('jadwals')->insert([
            [
                'nama_matkul' => 'Pemrograman Web',
                'dosen' => 'Dr. Andi Rahman',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'ruangan' => 'Lab 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_matkul' => 'Sistem Operasi',
                'dosen' => 'Prof. Budi Santoso',
                'hari' => 'Selasa',
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'ruangan' => 'Ruang 202',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_matkul' => 'Struktur Data',
                'dosen' => 'Dr. Citra Dewi',
                'hari' => 'Rabu',
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'ruangan' => 'Ruang 204',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_matkul' => 'Jaringan Komputer',
                'dosen' => 'Ir. Dimas Pratama',
                'hari' => 'Kamis',
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Lab 2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_matkul' => 'Basis Data',
                'dosen' => 'Dr. Evi Lestari',
                'hari' => 'Jumat',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'ruangan' => 'Ruang 205',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
