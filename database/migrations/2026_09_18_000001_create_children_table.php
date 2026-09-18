<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id('child_id');
            // unique: satu responden hanya punya satu data anak
            $table->foreignId('respondent_id')->unique()
                ->constrained('respondents', 'respondent_id')->cascadeOnDelete();
            $table->string('name');
            $table->date('birth_date');
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->unsignedTinyInteger('child_order'); // anak ke-berapa
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
