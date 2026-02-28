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
        $request->validate([
            'question_text'    => 'required|string',
            'type'             => 'required|in:pre,post',
            'order'            => 'required|integer|min:1',
            'options'          => 'required|array|size:4',
            'options.*.text'   => 'required|string',
            'correct_option'   => 'required|integer|between:0,3',
        ], [
            'question_text.required' => 'Teks soal wajib diisi.',
            'options.*.text.required' => 'Semua pilihan jawaban wajib diisi.',
            'correct_option.required' => 'Pilih salah satu jawaban yang benar.',
        ]);

        // Simpan soal
        $question = Question::create([
            'question_text' => $request->question_text,
            'type'          => $request->type,
            'order'         => $request->order,
        ]);

        // Simpan 4 pilihan jawaban
        $labels = ['A', 'B', 'C', 'D'];
        foreach ($request->options as $index => $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'label'       => $labels[$index],
                'option_text' => $option['text'],
                'is_correct'  => ($index == $request->correct_option) ? 1 : 0,
            ]);
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
        $request->validate([
            'question_text'    => 'required|string',
            'type'             => 'required|in:pre,post',
            'order'            => 'required|integer|min:1',
            'options'          => 'required|array|size:4',
            'options.*.text'   => 'required|string',
            'correct_option'   => 'required|integer|between:0,3',
        ], [
            'question_text.required'  => 'Teks soal wajib diisi.',
            'options.*.text.required' => 'Semua pilihan jawaban wajib diisi.',
            'correct_option.required' => 'Pilih salah satu jawaban yang benar.',
        ]);

        // Update soal
        $soal->update([
            'question_text' => $request->question_text,
            'type'          => $request->type,
            'order'         => $request->order,
        ]);

        // Update pilihan jawaban
        $labels  = ['A', 'B', 'C', 'D'];
        $options = $soal->options->sortBy('label')->values();

        foreach ($request->options as $index => $optionData) {
            if (isset($options[$index])) {
                $options[$index]->update([
                    'label'       => $labels[$index],
                    'option_text' => $optionData['text'],
                    'is_correct'  => ($index == $request->correct_option) ? 1 : 0,
                ]);
            } else {
                QuestionOption::create([
                    'question_id' => $soal->id,
                    'label'       => $labels[$index],
                    'option_text' => $optionData['text'],
                    'is_correct'  => ($index == $request->correct_option) ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(Question $soal)
    {
        // Hapus options dulu (cascade), lalu soal
        $soal->options()->delete();
        $soal->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus!');
    }
}