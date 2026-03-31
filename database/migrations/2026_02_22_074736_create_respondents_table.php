<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respondents', function (Blueprint $table) {
            $table->id();
            $table->string('access_code')->unique();         // contoh: SEHAT001
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->enum('marital_status', ['belum menikah', 'menikah', 'cerai hidup', 'cerai mati']);
            $table->string('occupation');
            $table->unsignedTinyInteger('total_children')->default(0); // ← tambahan
            $table->json('medical_history')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('pre_test_done')->default(false);
            $table->boolean('material_done')->default(false);
            $table->boolean('post_test_done')->default(false);
            $table->timestamp('pre_test_at')->nullable();
            $table->timestamp('post_test_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respondents');
    }
};