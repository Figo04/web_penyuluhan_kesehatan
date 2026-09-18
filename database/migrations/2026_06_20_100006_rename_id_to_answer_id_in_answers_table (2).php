<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak ada tabel lain yang FK ke answers, jadi cukup rename
        // kolom PK saja tanpa perlu drop/buat ulang foreign key.
        Schema::table('answers', function (Blueprint $table) {
            $table->renameColumn('id', 'answer_id');
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->renameColumn('answer_id', 'id');
        });
    }
};
