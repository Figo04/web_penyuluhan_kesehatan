<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Respondent;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Answer;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function showPreTest()
    {
        return $this->showTest('pre');
    }

    public function submitPreTest(Request $request)
    {
        return $this->submitTest($request, 'pre');
    }

    public function showPostTest()
    {
        return $this->showTest('post');
    }

    public function submitPostTest(Request $request)
    {
        return $this->submitTest($request, 'post');
    }

    public function autoSave(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if (!$respondent) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $testType = $request->test_type;

        if (!in_array($testType, ['pre', 'post'], true)) {
            return response()->json(['status' => 'error', 'message' => 'Tipe test tidak valid'], 422);
        }

        if ($respondent->{$testType . '_test_done'}) {
            return response()->json(['status' => 'already_done']);
        }

        if ($this->stageBlocked($respondent, $testType)) {
            return response()->json(['status' => 'locked'], 403);
        }

        // Autosave tidak boleh jadi celah menitipkan jawaban milik soal lain.
        if (!$this->answersBelongToQuestions([$request->question_id => $request->question_option_id], $testType)) {
            return response()->json(['status' => 'error', 'message' => 'Jawaban tidak valid'], 422);
        }

        Answer::updateOrCreate(
            [
                'respondent_id' => $respondent->id,
                'question_id'   => $request->question_id,
                'test_type'     => $testType,
            ],
            [
                'question_option_id' => $request->question_option_id,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    // ─── Alur bersama pre & post ────────────────────────────────────────────

    private function showTest(string $testType)
    {
        $respondent = Respondent::find(session('respondent_id'));

        // Yang sudah menyelesaikan test ini selalu boleh melihat halamannya,
        // termasuk data lama yang flag material_done-nya belum sempat terisi.
        if (!$respondent->{$testType . '_test_done'}
            && $redirect = $this->stageBlocked($respondent, $testType)) {
            return $redirect;
        }

        $questions = Question::with('options')
            ->where('type', $testType)
            ->orderBy('order')
            ->get();

        $answers = Answer::where('respondent_id', $respondent->id)
            ->where('test_type', $testType)
            ->get()
            ->keyBy('question_id');

        return view("respondent.{$testType}test", compact('questions', 'respondent', 'answers'));
    }

    private function submitTest(Request $request, string $testType)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->{$testType . '_test_done'}) {
            return redirect()->route('respondent.home');
        }

        if ($redirect = $this->stageBlocked($respondent, $testType)) {
            return $redirect;
        }

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|exists:question_options,id',
        ], [
            'answers.required'   => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

        if (!$this->answersBelongToQuestions($request->answers, $testType)) {
            return back()->withErrors([
                'answers' => 'Jawaban tidak sesuai dengan soalnya. Muat ulang halaman lalu coba lagi.',
            ]);
        }

        Answer::where('respondent_id', $respondent->id)
            ->where('test_type', $testType)
            ->delete();

        foreach ($request->answers as $question_id => $option_id) {
            Answer::create([
                'respondent_id'      => $respondent->id,
                'question_id'        => $question_id,
                'question_option_id' => $option_id,
                'test_type'          => $testType,
            ]);
        }

        $respondent->update([
            $testType . '_test_done' => true,
            $testType . '_test_at'   => now(),
        ]);

        $result = $this->calculateScore($respondent->id, $testType);

        return redirect()->route("respondent.{$testType}test")
            ->with('show_result', true)
            ->with('result', $result);
    }

    /**
     * Urutan tahap ditegakkan di server, bukan cuma di Blade.
     * Pre-test bebas; post-test menuntut pre-test dan materi selesai.
     * Mengembalikan RedirectResponse bila terkunci, null bila boleh lanjut.
     */
    private function stageBlocked(?Respondent $respondent, string $testType)
    {
        if ($testType !== 'post') {
            return null;
        }

        if (!$respondent->pre_test_done) {
            return redirect()->route('respondent.home')
                ->with('error', 'Selesaikan Pre-Test terlebih dahulu.');
        }

        // Materi yang belum diisi admin tidak boleh mengunci responden selamanya.
        if (!$respondent->material_done && Material::count() > 0) {
            return redirect()->route('respondent.material')
                ->with('error', 'Baca semua materi terlebih dahulu sebelum mengerjakan Post-Test.');
        }

        return null;
    }

    /**
     * Pastikan setiap opsi yang dikirim benar-benar milik soal yang bersangkutan,
     * dan soal itu memang bagian dari test ini. Tanpa ini, opsi soal mana pun
     * bisa dikirim sebagai jawaban soal mana pun lewat DevTools.
     */
    private function answersBelongToQuestions(array $answers, string $testType): bool
    {
        $pasangan = QuestionOption::whereIn('question_options.id', $answers)
            ->join('questions', 'questions.id', '=', 'question_options.question_id')
            ->where('questions.type', $testType)
            ->pluck('question_options.question_id', 'question_options.id');

        foreach ($answers as $questionId => $optionId) {
            if (($pasangan[$optionId] ?? null) != $questionId) {
                return false;
            }
        }

        return true;
    }

    private function calculateScore(int $respondentId, string $testType): array
    {
        $answers = Answer::with(['question', 'option'])
            ->where('respondent_id', $respondentId)
            ->where('test_type', $testType)
            ->get();

        $mcTotal     = 0;
        $mcCorrect   = 0;
        $mcScore     = 0;
        $likertTotal = 0;
        $likertScore = 0;

        foreach ($answers as $answer) {
            $format = $answer->question->question_format ?? 'multiple_choice';
            $option = $answer->option;

            if ($format === 'multiple_choice') {
                $mcTotal++;
                if ($option && $option->is_correct) {
                    $mcCorrect++;
                    $mcScore += $option->score ?? 10;
                }
            } elseif ($format === 'likert') {
                $likertTotal++;
                $likertScore += $option->score ?? 0;
            }
        }

        return [
            'mc_total'     => $mcTotal,
            'mc_correct'   => $mcCorrect,
            'mc_wrong'     => $mcTotal - $mcCorrect,
            'mc_score'     => $mcScore,
            'mc_max'       => $mcTotal * 10,
            'likert_total' => $likertTotal,
            'likert_score' => $likertScore,
            'likert_max'   => $likertTotal * 5,
            'has_mc'       => $mcTotal > 0,
            'has_likert'   => $likertTotal > 0,
        ];
    }
}
