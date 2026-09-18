<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // umum = soal pengetahuan/sikap (dinilai)
            // kondisi_anak = kuesioner kondisi anak di pre-test (tidak dinilai)
            $table->enum('category', ['umum', 'kondisi_anak'])->default('umum')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
