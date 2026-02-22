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

        if ($respondent->pre_test_done) {
            return redirect()->route('respondent.home')->with('info', 'Pre-Test sudah dikerjakan.');
        }

        $questions = Question::with('options')->where('type', 'pre')->orderBy('order')->get();
        return view('respondent.pretest', compact('questions', 'respondent'));
    }

    public function submitPreTest(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->pre_test_done) {
            return redirect()->route('respondent.home');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:question_options,id',
        ], [
            'answers.required' => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

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

        if (!$respondent->pre_test_done) {
            return redirect()->route('respondent.home')->with('error', 'Selesaikan Pre-Test terlebih dahulu.');
        }

        if (!$respondent->material_done) {
            return redirect()->route('respondent.home')->with('error', 'Baca semua materi terlebih dahulu.');
        }

        if ($respondent->post_test_done) {
            return redirect()->route('respondent.home')->with('info', 'Post-Test sudah dikerjakan.');
        }

        $questions = Question::with('options')->where('type', 'post')->orderBy('order')->get();
        return view('respondent.posttest', compact('questions', 'respondent'));
    }

    public function submitPostTest(Request $request)
    {
        $respondent = Respondent::find(session('respondent_id'));

        if ($respondent->post_test_done) {
            return redirect()->route('respondent.home');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:question_options,id',
        ], [
            'answers.required' => 'Semua soal wajib dijawab.',
            'answers.*.required' => 'Semua soal wajib dijawab.',
        ]);

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
        session(['autosave_' . $request->type => $request->answers]);
        return response()->json(['status' => 'saved']);
    }
}