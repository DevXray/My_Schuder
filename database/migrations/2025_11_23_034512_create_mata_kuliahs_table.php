<?php
// database/migrations/xxxx_create_mata_kuliahs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk', 20)->unique(); // e.g., TIF101
            $table->string('nama_mk');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->integer('sks')->default(3);
            $table->integer('semester')->default(1);
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['wajib', 'pilihan'])->default('wajib');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliahs');
    }
};