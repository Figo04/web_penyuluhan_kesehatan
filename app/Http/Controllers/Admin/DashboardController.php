<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Respondent;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRespondents = Respondent::count();
        $preTestDone = Respondent::where('pre_test_done', true)->count();
        $postTestDone = Respondent::where('post_test_done', true)->count();
        $completed = Respondent::where('pre_test_done', true)->where('post_test_done', true)->count();
        $recentRespondents = Respondent::with('location')->latest()->take(10)->get();

        $charts = [
            'scores'      => ['Pre-Test' => $this->averageScore('pre'), 'Post-Test' => $this->averageScore('post')],
            'locations'   => $this->respondentsPerLocation(),
            'motherAges'  => $this->bucket(Respondent::pluck('age'), [
                '< 20' => [0, 19], '20–24' => [20, 24], '25–29' => [25, 29],
                '30–34' => [30, 34], '35–39' => [35, 39], '≥ 40' => [40, PHP_INT_MAX],
            ]),
            'childAges'   => $this->bucket(Child::all()->map->ageInMonths(), [
                '0–5 bln' => [0, 5], '6–11 bln' => [6, 11], '12–23 bln' => [12, 23],
                '24–35 bln' => [24, 35], '36–59 bln' => [36, 59], '≥ 60 bln' => [60, PHP_INT_MAX],
            ]),
        ];

        return view('admin.dashboard', compact(
            'totalRespondents', 'preTestDone', 'postTestDone', 'completed', 'recentRespondents', 'charts'
        ));
    }

    /**
     * Rata-rata total skor per responden yang sudah menyelesaikan test.
     * Sama dengan TestController::calculateScore: opsi pilihan ganda salah
     * ber-skor 0, jadi cukup SUM(score); soal kondisi anak tidak dihitung.
     */
    private function averageScore(string $testType): float
    {
        $totals = DB::table('answers')
            ->join('questions', 'questions.question_id', '=', 'answers.question_id')
            ->join('question_options', 'question_options.question_option_id', '=', 'answers.question_option_id')
            ->join('respondents', 'respondents.respondent_id', '=', 'answers.respondent_id')
            ->where('answers.test_type', $testType)
            ->where('questions.category', '!=', 'kondisi_anak')
            ->where("respondents.{$testType}_test_done", true)
            ->groupBy('answers.respondent_id')
            ->selectRaw('SUM(question_options.score) AS total')
            ->pluck('total');

        return round((float) $totals->avg(), 1);
    }

    private function respondentsPerLocation(): array
    {
        return Respondent::with('location')->get()
            ->groupBy(fn ($r) => $r->location->name ?? 'Tanpa Lokasi')
            ->map->count()
            ->sortDesc()
            ->all();
    }

    /** Hitung jumlah nilai di setiap rentang [min, max] (inklusif) */
    private function bucket($values, array $ranges): array
    {
        return array_map(
            fn ($r) => $values->filter(fn ($v) => $v >= $r[0] && $v <= $r[1])->count(),
            $ranges
        );
    }
}
