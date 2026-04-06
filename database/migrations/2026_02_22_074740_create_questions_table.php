<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->text('question_text');
    $table->enum('type', ['pre', 'post']);
    $table->enum('question_format', ['multiple_choice', 'likert'])->default('multiple_choice'); // tambah ini
    $table->boolean('is_favourable')->nullable(); // tambah ini
    $table->integer('order')->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};