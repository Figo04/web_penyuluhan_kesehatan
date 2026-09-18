<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop semua FK constraint yang menunjuk ke respondents.id
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });

        // material_reads: tabel ini tidak punya migration create_table yang utuh
        // di codebase (kemungkinan dibuat manual / migration lama hilang).
        // Drop FK dibungkus try/catch supaya migration tidak gagal total
        // kalau nama constraint-nya ternyata tidak mengikuti konvensi Laravel.
        try {
            Schema::table('material_reads', function (Blueprint $table) {
                $table->dropForeign(['respondent_id']);
            });
        } catch (\Throwable $e) {
            // Jika gagal, kemungkinan nama constraint berbeda.
            // Cek manual: SHOW CREATE TABLE material_reads;
            // lalu drop FK tersebut secara manual sebelum lanjut.
            throw new \RuntimeException(
                'Gagal drop FK respondent_id di material_reads. '
                . 'Jalankan "SHOW CREATE TABLE material_reads;" untuk cek nama constraint asli, '
                . 'lalu sesuaikan migration ini. Pesan asli: ' . $e->getMessage()
            );
        }

        Schema::table('material_comments', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });

        // 2. Rename PK respondents.id -> respondents.respondent_id
        Schema::table('respondents', function (Blueprint $table) {
            $table->renameColumn('id', 'respondent_id');
        });

        // 3. Buat ulang semua FK constraint, sekarang menunjuk ke respondents.respondent_id
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('respondent_id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('material_reads', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('respondent_id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('respondent_id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('respondent_id')->on('respondents')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });
        Schema::table('material_reads', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['respondent_id']);
        });

        Schema::table('respondents', function (Blueprint $table) {
            $table->renameColumn('respondent_id', 'id');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('material_reads', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('id')->on('respondents')
                ->cascadeOnDelete();
        });
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreign('respondent_id')
                ->references('id')->on('respondents')
                ->cascadeOnDelete();
        });
    }
};
