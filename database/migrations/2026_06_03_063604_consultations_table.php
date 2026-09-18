<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respondent_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->enum('status', ['pending', 'answered'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};