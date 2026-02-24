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
        $respondent = Respondent::find(session('respondent_id'));

        $questions = Question::with('options')
            ->where('type', 'pre')
            ->orderBy('order')
            ->get();

        // Ambil jawaban yang sudah tersimpan, di-keyBy question_id
        $answers = Answer::where('respondent_id', $respondent->id)
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

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|exists:question_options,id',
        ], [
            'answers.required'   => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

        // Hapus jawaban lama (dari autosave) sebelum simpan final
        Answer::where('respondent_id', $respondent->id)
            ->where('test_type', 'pre')
            ->delete();

        foreach ($request->answers as $question_id => $option_id) {
            Answer::create([
                'respondent_id'      => $respondent->id,
                'question_id'        => $question_id,
                'question_option_id' => $option_id,
                'test_type'          => 'pre',
            ]);
        }

        $respondent->update([
            'pre_test_done' => true,
            'pre_test_at'   => now(),
        ]);

        return redirect()->route('respondent.home')->with('success', 'Pre-Test berhasil dikumpulkan!');
    }

    public function showPostTest()
    {
        $respondent = Respondent::find(session('respondent_id'));

        $questions = Question::with('options')
            ->where('type', 'post')
            ->orderBy('order')
            ->get();

        // Ambil jawaban yang sudah tersimpan, di-keyBy question_id
        $answers = Answer::where('respondent_id', $respondent->id)
            ->where('test_type', 'post')
            ->get()
            ->keyBy('question_id');

        return view('respondent.posttest', compact('questions', 'respondent', 'answers'));
    }

    public function submitPostTest(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->post_test_done) {
            return redirect()->route('respondent.home');
        }

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|exists:question_options,id',
        ], [
            'answers.required'   => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

        // Hapus jawaban lama (dari autosave) sebelum simpan final
        Answer::where('respondent_id', $respondent->id)
            ->where('test_type', 'post')
            ->delete();

        foreach ($request->answers as $question_id => $option_id) {
            Answer::create([
                'respondent_id'      => $respondent->id,
                'question_id'        => $question_id,
                'question_option_id' => $option_id,
                'test_type'          => 'post',
            ]);
        }

        $respondent->update([
            'post_test_done' => true,
            'post_test_at'   => now(),
        ]);

        return redirect()->route('respondent.home')->with('success', 'Post-Test berhasil dikumpulkan! Terima kasih.');
    }

    public function autoSave(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if (!$respondent) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $testType = $request->test_type; // 'pre' atau 'post'

        // Cek apakah test sudah final disubmit
        if ($testType === 'pre' && $respondent->pre_test_done) {
            return response()->json(['status' => 'already_done']);
        }
        if ($testType === 'post' && $respondent->post_test_done) {
            return response()->json(['status' => 'already_done']);
        }

        // Simpan atau update jawaban autosave
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
}