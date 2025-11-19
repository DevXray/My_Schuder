<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dosen_id');
            $table->unsignedBigInteger('materi_id');
            $table->string('judul');
            $table->text('deskripsi');
            $table->date('tanggal_diberikan');
            $table->date('deadline');
            $table->integer('bobot')->default(0); // Bobot dalam persen (0-100)
            $table->enum('status', ['pending', 'submitted', 'graded'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('nilai')->nullable(); // Nilai 0-100
            $table->string('grade')->nullable(); // A, B+, B, dll
            $table->text('feedback')->nullable(); // Feedback dari dosen
            $table->string('file_soal')->nullable(); // Path file soal
            $table->string('file_jawaban')->nullable(); // Path file jawaban mahasiswa
            $table->timestamp('waktu_pengumpulan')->nullable();
            $table->boolean('tepat_waktu')->default(true);
            $table->timestamps();

            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
            $table->foreign('materi_id')->references('id')->on('materis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};