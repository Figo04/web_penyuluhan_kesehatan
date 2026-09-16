<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionOption;
use Database\Seeders\KuesionerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KuesionerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_menghasilkan_20_soal_untuk_pre_dan_post(): void
    {
        $this->seed(KuesionerSeeder::class);

        $this->assertSame(40, Question::count());
        $this->assertSame(20, Question::where('type', 'pre')->count());
        $this->assertSame(20, Question::where('type', 'post')->count());

        foreach (['pre', 'post'] as $tipe) {
            $this->assertSame(10, Question::where('type', $tipe)->where('question_format', 'multiple_choice')->count());
            $this->assertSame(10, Question::where('type', $tipe)->where('question_format', 'likert')->count());
        }

        // 40 soal x 5 pilihan
        $this->assertSame(200, QuestionOption::count());
    }

    public function test_soal_post_identik_dengan_pre(): void
    {
        $this->seed(KuesionerSeeder::class);

        $pre  = Question::where('type', 'pre')->orderBy('order')->pluck('question_text');
        $post = Question::where('type', 'post')->orderBy('order')->pluck('question_text');

        $this->assertEquals($pre->all(), $post->all(),
            'Soal post-test harus sama persis dengan pre-test agar bisa dibandingkan.');
    }

    public function test_setiap_pilihan_ganda_punya_tepat_satu_jawaban_benar(): void
    {
        $this->seed(KuesionerSeeder::class);

        $mc = Question::where('question_format', 'multiple_choice')->with('options')->get();

        foreach ($mc as $soal) {
            $benar = $soal->options->where('is_correct', true);

            $this->assertCount(1, $benar, "Soal '{$soal->question_text}' tidak punya tepat satu jawaban benar.");
            $this->assertSame(10, $benar->first()->score);
            $this->assertCount(5, $soal->options);
            $this->assertSame(0, $soal->options->where('is_correct', false)->sum('score'));
        }
    }

    public function test_kunci_jawaban_sesuai_kuesioner(): void
    {
        $this->seed(KuesionerSeeder::class);

        // Kunci jawaban dari kuesioner.md, soal 1-10 berurutan.
        $kunci = ['B', 'A', 'D', 'B', 'A', 'E', 'C', 'C', 'B', 'C'];

        foreach ($kunci as $i => $label) {
            $soal = Question::where('type', 'pre')->where('order', $i + 1)->with('options')->first();

            $this->assertSame($label, $soal->options->firstWhere('is_correct', true)->label,
                'Kunci jawaban soal nomor ' . ($i + 1) . ' tidak cocok.');
        }
    }

    public function test_skor_likert_terbalik_untuk_pernyataan_unfavourable(): void
    {
        $this->seed(KuesionerSeeder::class);

        $fav = Question::where('question_format', 'likert')->where('is_favourable', true)->with('options')->first();
        $this->assertSame(1, $fav->options->firstWhere('label', 'STS')->score);
        $this->assertSame(5, $fav->options->firstWhere('label', 'SS')->score);

        $unfav = Question::where('question_format', 'likert')->where('is_favourable', false)->with('options')->first();
        $this->assertSame(5, $unfav->options->firstWhere('label', 'STS')->score);
        $this->assertSame(1, $unfav->options->firstWhere('label', 'SS')->score);
    }

    public function test_empat_pernyataan_sikap_bersifat_unfavourable(): void
    {
        $this->seed(KuesionerSeeder::class);

        // Nomor 15, 16, 17, 19 pada kuesioner.md
        $this->assertSame(4, Question::where('type', 'pre')
            ->where('question_format', 'likert')
            ->where('is_favourable', false)
            ->count());
    }

    public function test_dijalankan_dua_kali_tidak_menggandakan(): void
    {
        // Cron berjalan tiap menit, jadi seeder pasti tereksekusi berulang.
        $this->seed(KuesionerSeeder::class);
        $this->seed(KuesionerSeeder::class);

        $this->assertSame(40, Question::count());
        $this->assertSame(200, QuestionOption::count());
    }
}
