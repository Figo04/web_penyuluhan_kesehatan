<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // ─── Opsi Likert tetap (tidak berubah) ──────────────────────────────────
    const LIKERT_OPTIONS = [
        ['label' => 'STS', 'option_text' => 'Sangat Tidak Setuju'],
        ['label' => 'TS',  'option_text' => 'Tidak Setuju'],
        ['label' => 'R',   'option_text' => 'Ragu-ragu / Netral'],
        ['label' => 'S',   'option_text' => 'Setuju'],
        ['label' => 'SS',  'option_text' => 'Sangat Setuju'],
    ];

    // Skor Likert berdasarkan is_favourable dan posisi (indeks 0=STS … 4=SS)
    //   Favourable  : STS=1, TS=2, R=3, S=4, SS=5
    //   Unfavourable: STS=5, TS=4, R=3, S=2, SS=1
    const LIKERT_SCORES_FAV   = [1, 2, 3, 4, 5];
    const LIKERT_SCORES_UNFAV = [5, 4, 3, 2, 1];

    // ────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $preQuestions  = Question::with('options')->where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::with('options')->where('type', 'post')->orderBy('order')->get();

        return view('admin.questions.index', compact('preQuestions', 'postQuestions'));
    }

    public function create()
    {
        return view('admin.questions.create');
    }

    public function store(Request $request)
    {
        [$format, $category] = $this->validateQuestion($request);

        // Simpan soal
        $question = Question::create([
            'question_text'   => $request->question_text,
            'type'            => $request->type,
            'category'        => $category,
            'order'           => $request->order,
            'question_format' => $format,
            'is_favourable'   => $format === 'likert' ? $request->boolean('is_favourable') : null,
        ]);

        if ($format === 'multiple_choice') {
            $this->saveMcOptions($question, $request->options, $this->correctIndex($request, $category));
        } else {
            $this->saveLikertOptions($question, $question->is_favourable);
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit(Question $soal)
    {
        $soal->load('options');
        return view('admin.questions.edit', ['question' => $soal]);
    }

    public function update(Request $request, Question $soal)
    {
        [$format, $category] = $this->validateQuestion($request);

        // Update soal
        $soal->update([
            'question_text'   => $request->question_text,
            'type'            => $request->type,
            'category'        => $category,
            'order'           => $request->order,
            'question_format' => $format,
            'is_favourable'   => $format === 'likert' ? $request->boolean('is_favourable') : null,
        ]);

        // Hapus semua opsi lama, lalu buat ulang (paling aman)
        $soal->options()->delete();

        if ($format === 'multiple_choice') {
            $this->saveMcOptions($soal, $request->options, $this->correctIndex($request, $category));
        } else {
            $this->saveLikertOptions($soal, $request->boolean('is_favourable'));
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(Question $soal)
    {
        $soal->options()->delete();
        $soal->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus!');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Validasi form soal (dipakai store & update).
     * Soal kondisi anak hanya untuk pre-test dan tidak punya jawaban benar.
     * @return array{0: string, 1: string} [format, category]
     */
    private function validateQuestion(Request $request): array
    {
        $format   = $request->input('question_format', 'multiple_choice');
        $category = $request->input('type') === 'pre' && $request->input('category') === 'kondisi_anak'
            ? 'kondisi_anak'
            : 'umum';

        $rules = [
            'question_text'   => 'required|string',
            'type'            => 'required|in:pre,post',
            'category'        => 'nullable|in:umum,kondisi_anak',
            'order'           => 'required|integer|min:1',
            'question_format' => 'required|in:multiple_choice,likert',
        ];

        $messages = [
            'question_text.required'   => 'Teks soal wajib diisi.',
            'correct_option.required'  => 'Pilih salah satu jawaban yang benar.',
            'options.*.text.required'  => 'Semua pilihan jawaban wajib diisi.',
        ];

        if ($format === 'multiple_choice') {
            $rules['options']        = 'required|array|min:2|max:5';
            $rules['options.*.text'] = 'required|string';
            $rules['correct_option'] = $category === 'kondisi_anak' ? 'nullable' : 'required|integer|min:0';
        } else {
            $rules['is_favourable']  = 'required|boolean';
        }

        $request->validate($rules, $messages);

        return [$format, $category];
    }

    /** Indeks jawaban benar; -1 = tidak ada (soal kondisi anak) */
    private function correctIndex(Request $request, string $category): int
    {
        return $category === 'kondisi_anak' ? -1 : (int) $request->correct_option;
    }

    /**
     * Simpan pilihan jawaban Multiple Choice
     * $options = array of ['text' => '...']
     * $correctIndex = indeks (0-based) yang benar
     */
    private function saveMcOptions(Question $question, array $options, int $correctIndex): void
    {
        $labels = ['A', 'B', 'C', 'D', 'E'];
        foreach ($options as $index => $option) {
            $isCorrect = ($index === $correctIndex);
            QuestionOption::create([
                'question_id' => $question->question_id,
                'label'       => $labels[$index] ?? chr(65 + $index),
                'option_text' => $option['text'],
                'is_correct'  => $isCorrect,
                'score'       => $isCorrect ? 10 : 0,
            ]);
        }
    }

    /**
     * Simpan pilihan jawaban Likert (selalu 5 opsi, otomatis)
     * Urutan: STS(0), TS(1), R(2), S(3), SS(4)
     */
    private function saveLikertOptions(Question $question, bool $isFavourable): void
    {
        $scores = $isFavourable ? self::LIKERT_SCORES_FAV : self::LIKERT_SCORES_UNFAV;

        foreach (self::LIKERT_OPTIONS as $index => $opt) {
            QuestionOption::create([
                'question_id' => $question->question_id,
                'label'       => $opt['label'],
                'option_text' => $opt['option_text'],
                'is_correct'  => false, // Likert tidak punya "benar/salah"
                'score'       => $scores[$index],
            ]);
        }
    }
}