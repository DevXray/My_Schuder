<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriSeeder extends Seeder
{
    public function run()
    {
        DB::table('materis')->insert([
            [
                'dosen_id' => 16,
                'judul' => 'Algoritma & Struktur Data',
                'deskripsi' => 'Pelajari konsep dasar algoritma, kompleksitas waktu, dan berbagai struktur data fundamental.',
                'kategori' => 'pemrograman',
                'jumlah_modul' => 12,
                'durasi_jam' => 8,
                'progress' => 75,
                'status' => 'progress',
                'icon' => 'fa-code',
                'warna' => 'blue',
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 17,
                'judul' => 'Pemrograman Web Lanjut',
                'deskripsi' => 'Kuasai JavaScript ES6+, React, Node.js dan framework modern untuk web development.',
                'kategori' => 'pemrograman',
                'jumlah_modul' => 15,
                'durasi_jam' => 10,
                'progress' => 90,
                'status' => 'progress',
                'icon' => 'fa-globe',
                'warna' => 'orange',
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 18,
                'judul' => 'Basis Data & SQL',
                'deskripsi' => 'Pelajari desain database, normalisasi, query optimization, dan manajemen database.',
                'kategori' => 'database',
                'jumlah_modul' => 10,
                'durasi_jam' => 7,
                'progress' => 60,
                'status' => 'progress',
                'icon' => 'fa-database',
                'warna' => 'blue',
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 19,
                'judul' => 'Matematika Diskrit',
                'deskripsi' => 'Teori graf, kombinatorik, logika matematika, dan aplikasinya dalam ilmu komputer.',
                'kategori' => 'matematika',
                'jumlah_modul' => 8,
                'durasi_jam' => 6,
                'progress' => 100,
                'status' => 'completed',
                'icon' => 'fa-calculator',
                'warna' => 'green',
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 20,
                'judul' => 'Jaringan Komputer',
                'deskripsi' => 'Konsep jaringan, protokol TCP/IP, keamanan jaringan, dan administrasi sistem.',
                'kategori' => 'jaringan',
                'jumlah_modul' => 11,
                'durasi_jam' => 9,
                'progress' => 0,
                'status' => 'new',
                'icon' => 'fa-network-wired',
                'warna' => 'blue',
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}