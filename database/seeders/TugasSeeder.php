<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TugasSeeder extends Seeder
{
    public function run()
    {
        DB::table('tugas')->insert([
            // Tugas 1 - Deadline Dekat
            [
                'dosen_id' => 16,
                'materi_id' => 19, // Pemrograman Web Lanjut
                'judul' => 'Project Akhir Semester - Aplikasi Web',
                'deskripsi' => 'Buat aplikasi web fullstack menggunakan framework modern (React/Vue + Laravel/Node.js). Aplikasi harus memiliki fitur CRUD, authentication, dan responsive design.',
                'tanggal_diberikan' => Carbon::now()->subDays(11),
                'deadline' => Carbon::now()->addDays(3),
                'bobot' => 30,
                'status' => 'pending',
                'priority' => 'high',
                'nilai' => null,
                'grade' => null,
                'feedback' => null,
                'file_soal' => 'tugas/project-akhir-web.pdf',
                'file_jawaban' => null,
                'waktu_pengumpulan' => null,
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tugas 2 - Pending
            [
                'dosen_id' => 17,
                'materi_id' => 20, // Algoritma & Struktur Data
                'judul' => 'Analisis Kompleksitas Algoritma',
                'deskripsi' => 'Analisis kompleksitas waktu dan ruang untuk berbagai algoritma sorting dan searching. Sertakan grafik perbandingan dan kesimpulan.',
                'tanggal_diberikan' => Carbon::now()->subDays(7),
                'deadline' => Carbon::now()->addDays(6),
                'bobot' => 20,
                'status' => 'pending',
                'priority' => 'medium',
                'nilai' => null,
                'grade' => null,
                'feedback' => null,
                'file_soal' => 'tugas/analisis-algoritma.pdf',
                'file_jawaban' => null,
                'waktu_pengumpulan' => null,
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tugas 3 - Submitted
            [
                'dosen_id' => 18,
                'materi_id' => 21, // Basis Data & SQL
                'judul' => 'Database Design - E-Commerce',
                'deskripsi' => 'Rancang database untuk sistem e-commerce lengkap dengan ERD, normalisasi, dan query optimization.',
                'tanggal_diberikan' => Carbon::now()->subDays(14),
                'deadline' => Carbon::now()->addDays(2),
                'bobot' => 25,
                'status' => 'submitted',
                'priority' => 'low',
                'nilai' => null,
                'grade' => null,
                'feedback' => null,
                'file_soal' => 'tugas/database-ecommerce.pdf',
                'file_jawaban' => 'jawaban/database-design.pdf',
                'waktu_pengumpulan' => Carbon::now()->subDays(4)->setTime(14, 30),
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tugas 4 - Graded (Excellent)
            [
                'dosen_id' => 19,
                'materi_id' => 22, // Pemrograman Web Lanjut
                'judul' => 'Implementasi RESTful API',
                'deskripsi' => 'Implementasi RESTful API dengan authentication, validation, dan dokumentasi lengkap.',
                'tanggal_diberikan' => Carbon::now()->subDays(21),
                'deadline' => Carbon::now()->subDays(7),
                'bobot' => 20,
                'status' => 'graded',
                'priority' => 'low',
                'nilai' => 92,
                'grade' => 'A',
                'feedback' => 'Pekerjaan yang sangat baik! API sudah terstruktur dengan baik, dokumentasi lengkap, dan error handling yang tepat. Pertahankan!',
                'file_soal' => 'tugas/restful-api.pdf',
                'file_jawaban' => 'jawaban/api-implementation.zip',
                'waktu_pengumpulan' => Carbon::now()->subDays(9)->setTime(16, 45),
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tugas 5 - Graded (Good)
            [
                'dosen_id' => 20,
                'materi_id' => 23, // Jaringan Komputer
                'judul' => 'Laporan Praktikum Jaringan',
                'deskripsi' => 'Laporan lengkap hasil praktikum konfigurasi router dan switching.',
                'tanggal_diberikan' => Carbon::now()->subDays(18),
                'deadline' => Carbon::now()->subDays(5),
                'bobot' => 15,
                'status' => 'graded',
                'priority' => 'low',
                'nilai' => 78,
                'grade' => 'B+',
                'feedback' => 'Laporan sudah cukup baik, namun perlu penjelasan lebih detail pada bagian troubleshooting. Tambahkan juga analisis hasil percobaan.',
                'file_soal' => 'tugas/praktikum-jaringan.pdf',
                'file_jawaban' => 'jawaban/laporan-jaringan.pdf',
                'waktu_pengumpulan' => Carbon::now()->subDays(6)->setTime(10, 15),
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tugas 6 - Pending (Medium Priority)
            [
                'dosen_id' => 16,
                'materi_id' => 24, // Matematika Diskrit
                'judul' => 'Pembuktian Teorema Graf',
                'deskripsi' => 'Buktikan teorema-teorema dasar dalam teori graf dan berikan contoh aplikasinya.',
                'tanggal_diberikan' => Carbon::now()->subDays(5),
                'deadline' => Carbon::now()->addDays(10),
                'bobot' => 15,
                'status' => 'pending',
                'priority' => 'medium',
                'nilai' => null,
                'grade' => null,
                'feedback' => null,
                'file_soal' => 'tugas/teorema-graf.pdf',
                'file_jawaban' => null,
                'waktu_pengumpulan' => null,
                'tepat_waktu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}