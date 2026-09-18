<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK constraint lama di respondents yang menunjuk ke locations.id
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        // 2. Rename PK locations.id -> locations.location_id
        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('id', 'location_id');
        });

        // 3. Buat ulang FK constraint, sekarang menunjuk ke locations.location_id
        Schema::table('respondents', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('location_id')->on('locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('location_id', 'id');
        });

        Schema::table('respondents', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('id')->on('locations')
                ->nullOnDelete();
        });
    }
};
