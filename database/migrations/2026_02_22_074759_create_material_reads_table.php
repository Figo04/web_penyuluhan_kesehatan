<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom read_at jika belum ada
        if (Schema::hasTable('material_reads') && !Schema::hasColumn('material_reads', 'read_at')) {
            Schema::table('material_reads', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('respondent_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('material_reads', 'read_at')) {
            Schema::table('material_reads', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }
    }
};