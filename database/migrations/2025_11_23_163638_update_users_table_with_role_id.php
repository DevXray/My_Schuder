<?php
// database/migrations/xxxx_update_users_table_with_role_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom 'role' string jika ada
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // Tambah kolom role_id sebagai foreign key
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')
                      ->nullable()
                      ->after('email')
                      ->constrained('roles')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            
            // Restore old role column
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])
                  ->default('mahasiswa');
        });
    }
};