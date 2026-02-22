<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $preQuestions = Question::with('options')->where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::with('options')->where('type', 'post')->orderBy('order')->get();
        return view('admin.questions.index', compact('preQuestions', 'postQuestions'));
    }

    public function create()
    {
        return view('admin.questions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:pre,post',
            'order'         => 'required|integer',
            'options'       => 'required|array|min:2',
            'options.*.label' => 'required|string',
            'options.*.option_text' => 'required|string',
        ]);

        $question = Question::create([
            'question_text' => $request->question_text,
            'type'          => $request->type,
            'order'         => $request->order,
        ]);

        foreach ($request->options as $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'label'       => $option['label'],
                'option_text' => $option['option_text'],
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Question $question)
    {
        $question->load('options');
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:pre,post',
            'order'         => 'required|integer',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'type'          => $request->type,
            'order'         => $request->order,
        ]);

        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil diupdate.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil dihapus.');
    }
}