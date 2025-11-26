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
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('file_soal')->nullable(); // Path file soal
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