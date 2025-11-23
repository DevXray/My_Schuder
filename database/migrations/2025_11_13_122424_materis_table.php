<?php
// database/migrations/xxxx_update_materis_table_add_mata_kuliah.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            // Add mata_kuliah_id
            $table->foreignId('mata_kuliah_id')
                  ->after('dosen_id')
                  ->nullable()
                  ->constrained('mata_kuliahs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropColumn('mata_kuliah_id');
        });
    }
};