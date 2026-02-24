<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;
use Illuminate\Http\Request;

class RespondentController extends Controller
{
    public function index(Request $request)
    {
        $query = Respondent::with('location')->latest();

        // Filter pencarian by nama atau kode akses
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('access_code', 'like', "%{$search}%");
            });
        }

        // paginate(25) = aman untuk 150 data (6 halaman)
        // withQueryString() = agar filter pencarian tidak hilang saat pindah halaman
        $respondents = $query->paginate(25)->withQueryString();

        return view('admin.respondents', compact('respondents'));
    }

    public function hasilTest()
    {
        // Tetap pakai get() karena hasilTest tidak pakai pagination
        // answers.question & answers.option keduanya diload agar tidak error di view
        $respondents   = Respondent::with(['answers.question', 'answers.option'])->get();
        $preQuestions  = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        return view('admin.hasil-test', compact('respondents', 'preQuestions', 'postQuestions'));
    }
}