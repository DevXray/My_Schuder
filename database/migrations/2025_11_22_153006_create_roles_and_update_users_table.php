<?php
// database/migrations/2025_01_XX_000001_create_roles_and_update_users.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, dosen, mahasiswa
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Tambah role_id ke users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('cascade');
        });

        // 3. Buat tabel admins
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
        });

        // 4. Update tabel dosens (tambah user_id jika belum ada)
        Schema::table('dosens', function (Blueprint $table) {
            if (!Schema::hasColumn('dosens', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });

        // 5. Update tabel mahasiswas (tambah user_id jika belum ada)
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });

        // 6. Buat tabel matkuls (Mata Kuliah)
        Schema::create('matkuls', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk')->unique();
            $table->string('nama_mk');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->integer('sks');
            $table->integer('semester');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['pemrograman', 'matematika', 'database', 'jaringan', 'lainnya']);
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->string('icon')->default('fa-book');
            $table->enum('warna', ['blue', 'orange', 'green', 'purple'])->default('blue');
            $table->timestamps();
        });

        // 7. Update tabel materis
        Schema::table('materis', function (Blueprint $table) {
            // Tambah kolom baru
            $table->foreignId('matkul_id')->nullable()->after('id')->constrained('matkuls')->onDelete('cascade');
            $table->string('judul')->nullable()->change();
            $table->enum('tipe_materi', ['pdf', 'ppt', 'video', 'link', 'doc'])->default('pdf')->after('deskripsi');
            
            // Rename kolom jika perlu
            // Note: Lakukan ini dengan hati-hati, backup data dulu
            // $table->renameColumn('judul', 'judul_materi');
        });

        // 8. Seed roles default
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full system access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'dosen', 'display_name' => 'Dosen', 'description' => 'Can manage courses and students', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mahasiswa', 'display_name' => 'Mahasiswa', 'description' => 'Can view courses and submit assignments', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropForeign(['matkul_id']);
            $table->dropColumn(['matkul_id', 'tipe_materi']);
        });

        Schema::dropIfExists('matkuls');
        
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (Schema::hasColumn('mahasiswas', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('dosens', function (Blueprint $table) {
            if (Schema::hasColumn('dosens', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::dropIfExists('admins');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};