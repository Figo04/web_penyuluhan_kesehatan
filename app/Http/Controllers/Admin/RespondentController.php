<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;

class RespondentController extends Controller
{
    public function index()
    {
        $respondents = Respondent::with('location')->latest()->get();
        return view('admin.respondents', compact('respondents'));
    }

    public function hasilTest()
    {
        $respondents = Respondent::with(['answers.question', 'answers.option'])->get();
        $preQuestions = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        return view('admin.hasil-test', compact('respondents', 'preQuestions', 'postQuestions'));
    }
}