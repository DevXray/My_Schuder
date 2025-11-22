<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matkul;
use App\Models\Dosen;

class MatkulSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = Dosen::take(5)->get();

        if ($dosens->isEmpty()) {
            echo "⚠️ No Dosen found. Run DosenSeeder first.\n";
            return;
        }

        $matkuls = [
            [
                'kode_mk' => 'IF101',
                'nama_mk' => 'Algoritma & Pemrograman',
                'dosen_id' => $dosens[0]->id,
                'sks' => 4,
                'semester' => 1,
                'deskripsi' => 'Mempelajari dasar-dasar algoritma dan pemrograman komputer',
                'kategori' => 'pemrograman',
                'status' => 'aktif',
                'icon' => 'fa-code',
                'warna' => 'blue',
            ],
            [
                'kode_mk' => 'IF201',
                'nama_mk' => 'Struktur Data',
                'dosen_id' => $dosens[1]->id,
                'sks' => 3,
                'semester' => 2,
                'deskripsi' => 'Mempelajari berbagai struktur data dan implementasinya',
                'kategori' => 'pemrograman',
                'status' => 'aktif',
                'icon' => 'fa-sitemap',
                'warna' => 'orange',
            ],
            [
                'kode_mk' => 'IF301',
                'nama_mk' => 'Basis Data',
                'dosen_id' => $dosens[2]->id,
                'sks' => 3,
                'semester' => 3,
                'deskripsi' => 'Mempelajari konsep dan implementasi basis data',
                'kategori' => 'database',
                'status' => 'aktif',
                'icon' => 'fa-database',
                'warna' => 'green',
            ],
            [
                'kode_mk' => 'IF401',
                'nama_mk' => 'Jaringan Komputer',
                'dosen_id' => $dosens[3]->id,
                'sks' => 3,
                'semester' => 4,
                'deskripsi' => 'Mempelajari konsep dan teknologi jaringan komputer',
                'kategori' => 'jaringan',
                'status' => 'aktif',
                'icon' => 'fa-network-wired',
                'warna' => 'purple',
            ],
            [
                'kode_mk' => 'MAT201',
                'nama_mk' => 'Matematika Diskrit',
                'dosen_id' => $dosens[4]->id,
                'sks' => 3,
                'semester' => 2,
                'deskripsi' => 'Mempelajari logika, himpunan, graf, dan kombinatorik',
                'kategori' => 'matematika',
                'status' => 'aktif',
                'icon' => 'fa-calculator',
                'warna' => 'blue',
            ],
        ];

        foreach ($matkuls as $matkul) {
            Matkul::firstOrCreate(
                ['kode_mk' => $matkul['kode_mk']],
                $matkul
            );
        }
    }
}