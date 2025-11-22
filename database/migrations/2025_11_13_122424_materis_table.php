<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('materis')) {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dosen_id');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['pemrograman', 'matematika', 'database', 'jaringan']);
            $table->integer('jumlah_modul')->default(1);
            $table->integer('durasi_jam')->default(1);
            $table->integer('progress')->default(0);
            $table->enum('status', ['new', 'progress', 'completed'])->default('new');
            $table->string('icon')->default('fa-book');
            $table->enum('warna', ['blue', 'orange', 'green'])->default('blue');
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
        });
    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};