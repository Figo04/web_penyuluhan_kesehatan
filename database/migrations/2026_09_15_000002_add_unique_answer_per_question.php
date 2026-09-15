<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan duplikat lama dulu (sisakan baris terbaru per soal),
        // kalau tidak indeks uniknya akan ditolak.
        $duplikat = DB::table('answers')
            ->selectRaw('respondent_id, question_id, test_type, MAX(id) as keep_id')
            ->groupBy('respondent_id', 'question_id', 'test_type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplikat as $row) {
            DB::table('answers')
                ->where('respondent_id', $row->respondent_id)
                ->where('question_id', $row->question_id)
                ->where('test_type', $row->test_type)
                ->where('id', '!=', $row->keep_id)
                ->delete();
        }

        Schema::table('answers', function (Blueprint $table) {
            // autoSave() memakai updateOrCreate pada kombinasi ini — DB yang
            // menegakkannya, bukan cuma kode, supaya request berbarengan
            // tidak menghasilkan jawaban ganda yang menggandakan skor.
            $table->unique(['respondent_id', 'question_id', 'test_type'], 'answers_unique_per_question');
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropUnique('answers_unique_per_question');
        });
    }
};
