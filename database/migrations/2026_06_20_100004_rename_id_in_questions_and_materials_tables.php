<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===================================================================
        // BAGIAN A: questions.id -> question_id
        // FK yang menunjuk ke questions: question_options.question_id, answers.question_id
        // ===================================================================
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('id', 'question_id');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('question_id')->on('questions')
                ->cascadeOnDelete();
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('question_id')->on('questions')
                ->cascadeOnDelete();
        });

        // ===================================================================
        // BAGIAN B: materials.id -> material_id
        // FK yang menunjuk ke materials: material_reads.material_id, material_comments.material_id
        // ===================================================================
        try {
            Schema::table('material_reads', function (Blueprint $table) {
                $table->dropForeign(['material_id']);
            });
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Gagal drop FK material_id di material_reads. '
                . 'Jalankan "SHOW CREATE TABLE material_reads;" untuk cek nama constraint asli, '
                . 'lalu sesuaikan migration ini. Pesan asli: ' . $e->getMessage()
            );
        }
        Schema::table('material_comments', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('id', 'material_id');
        });

        Schema::table('material_reads', function (Blueprint $table) {
            $table->foreign('material_id')
                ->references('material_id')->on('materials')
                ->cascadeOnDelete();
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->foreign('material_id')
                ->references('material_id')->on('materials')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Rollback BAGIAN B dulu (urutan terbalik dari up())
        Schema::table('material_reads', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('material_id', 'id');
        });

        Schema::table('material_reads', function (Blueprint $table) {
            $table->foreign('material_id')
                ->references('id')->on('materials')
                ->cascadeOnDelete();
        });
        Schema::table('material_comments', function (Blueprint $table) {
            $table->foreign('material_id')
                ->references('id')->on('materials')
                ->cascadeOnDelete();
        });

        // Rollback BAGIAN A
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('question_id', 'id');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('id')->on('questions')
                ->cascadeOnDelete();
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('id')->on('questions')
                ->cascadeOnDelete();
        });
    }
};
