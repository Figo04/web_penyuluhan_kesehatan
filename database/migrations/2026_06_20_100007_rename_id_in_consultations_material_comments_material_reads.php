<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ketiga tabel ini tidak ada tabel lain yang FK ke PK-nya,
        // jadi cukup rename kolom saja tanpa perlu drop/buat ulang FK.

        Schema::table('consultations', function (Blueprint $table) {
            $table->renameColumn('id', 'consultation_id');
        });

        Schema::table('material_comments', function (Blueprint $table) {
            $table->renameColumn('id', 'material_comment_id');
        });

        Schema::table('material_reads', function (Blueprint $table) {
            $table->renameColumn('id', 'material_read_id');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->renameColumn('consultation_id', 'id');
        });

        Schema::table('material_comments', function (Blueprint $table) {
            $table->renameColumn('material_comment_id', 'id');
        });

        Schema::table('material_reads', function (Blueprint $table) {
            $table->renameColumn('material_read_id', 'id');
        });
    }
};
