<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function showPreTest()
    {
        $respondent = Respondent::with('child')->find(session('respondent_id'));

        // Data anak wajib diisi sebelum mengerjakan pre-test
        if (!$respondent->pre_test_done && !$respondent->child) {
            return view('respondent.child-form', compact('respondent'));
        }

        // Soal kondisi anak tampil lebih dulu, lalu soal pengetahuan/sikap
        $questions = Question::with('options')
            ->where('type', 'pre')
            ->orderByRaw("category = 'kondisi_anak' DESC")
            ->orderBy('order')
            ->get();

        $answers = Answer::where('respondent_id', $respondent->respondent_id)
            ->where('test_type', 'pre')
            ->get()
            ->keyBy('question_id');

        return view('respondent.pretest', compact('questions', 'respondent', 'answers'));
    }

    public function submitPreTest(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->pre_test_done) {
            return redirect()->route('respondent.home');
        }

        if (!$respondent->child) {
            return redirect()->route('respondent.pretest');
        }

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|exists:question_options,question_option_id',
        ], [
            'answers.required'   => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

        Answer::where('respondent_id', $respondent->respondent_id)
            ->where('test_type', 'pre')
            ->delete();

        foreach ($request->answers as $question_id => $option_id) {
            Answer::create([
                'respondent_id'      => $respondent->respondent_id,
                'question_id'        => $question_id,
                'question_option_id' => $option_id,
                'test_type'          => 'pre',
            ]);
        }

        $respondent->update([
            'pre_test_done' => true,
            'pre_test_at'   => now(),
        ]);

        $result = $this->calculateScore($respondent->respondent_id, 'pre');

        return redirect()->route('respondent.pretest')
            ->with('show_result', true)
            ->with('result', $result);
    }

    public function storeChild(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->pre_test_done) {
            return redirect()->route('respondent.pretest');
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'birth_date'  => 'required|date|before_or_equal:today|after:' . now()->subYears(18)->toDateString(),
            'gender'      => 'required|in:laki-laki,perempuan',
            'child_order' => 'required|integer|min:1|max:20',
            'weight_kg'   => 'required|numeric|min:0.5|max:150',
            'height_cm'   => 'required|numeric|min:20|max:200',
        ], [
            'required'                => 'Semua data anak wajib diisi.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'birth_date.after'        => 'Tanggal lahir tidak valid.',
        ]);

        $respondent->child()->updateOrCreate([], $data);

        return redirect()->route('respondent.pretest');
    }

    public function showPostTest()
    {
        $respondent = Respondent::find(session('respondent_id'));

        $questions = Question::with('options')
            ->where('type', 'post')
            ->orderBy('order')
            ->get();

        $answers = Answer::where('respondent_id', $respondent->respondent_id)
            ->where('test_type', 'post')
            ->get()
            ->keyBy('question_id');

        $materialDone = $respondent->hasFinishedMaterials();

        return view('respondent.posttest', compact('questions', 'respondent', 'answers', 'materialDone'));
    }

    public function submitPostTest(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->post_test_done) {
            return redirect()->route('respondent.home');
        }

        // Post-test hanya boleh setelah pre-test dan semua materi selesai
        if (!$respondent->pre_test_done || !$respondent->hasFinishedMaterials()) {
            return redirect()->route('respondent.posttest');
        }

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|exists:question_options,question_option_id',
        ], [
            'answers.required'   => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

        Answer::where('respondent_id', $respondent->respondent_id)
            ->where('test_type', 'post')
            ->delete();

        foreach ($request->answers as $question_id => $option_id) {
            Answer::create([
                'respondent_id'      => $respondent->respondent_id,
                'question_id'        => $question_id,
                'question_option_id' => $option_id,
                'test_type'          => 'post',
            ]);
        }

        $respondent->update([
            'post_test_done' => true,
            'post_test_at'   => now(),
        ]);

        $result = $this->calculateScore($respondent->respondent_id, 'post');

        return redirect()->route('respondent.posttest')
            ->with('show_result', true)
            ->with('result', $result);
    }

    public function autoSave(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if (!$respondent) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $testType = $request->test_type;

        if ($testType === 'pre' && $respondent->pre_test_done) {
            return response()->json(['status' => 'already_done']);
        }
        if ($testType === 'post' && $respondent->post_test_done) {
            return response()->json(['status' => 'already_done']);
        }
        if ($testType === 'post' && (!$respondent->pre_test_done || !$respondent->hasFinishedMaterials())) {
            return response()->json(['status' => 'locked'], 403);
        }

        Answer::updateOrCreate(
            [
                'respondent_id' => $respondent->respondent_id,
                'question_id'   => $request->question_id,
                'test_type'     => $testType,
            ],
            [
                'question_option_id' => $request->question_option_id,
            ]
        );

        return response()->json(['status' => 'saved']);
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
            // Kuesioner kondisi anak tidak dinilai
            if ($answer->question?->category === 'kondisi_anak') {
                continue;
            }

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