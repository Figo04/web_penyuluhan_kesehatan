<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK constraint di answers yang menunjuk ke question_options.id
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['question_option_id']);
        });

        // 2. Rename PK question_options.id -> question_option_id
        Schema::table('question_options', function (Blueprint $table) {
            $table->renameColumn('id', 'question_option_id');
        });

        // 3. Buat ulang FK constraint, sekarang menunjuk ke question_options.question_option_id
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('question_option_id')
                ->references('question_option_id')->on('question_options')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['question_option_id']);
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->renameColumn('question_option_id', 'id');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('question_option_id')
                ->references('id')->on('question_options')
                ->cascadeOnDelete();
        });
    }
};
