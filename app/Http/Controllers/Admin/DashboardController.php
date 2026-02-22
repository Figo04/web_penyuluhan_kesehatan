<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRespondents = Respondent::count();
        $preTestDone = Respondent::where('pre_test_done', true)->count();
        $postTestDone = Respondent::where('post_test_done', true)->count();
        $completed = Respondent::where('pre_test_done', true)->where('post_test_done', true)->count();
        $recentRespondents = Respondent::with('location')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalRespondents', 'preTestDone', 'postTestDone', 'completed', 'recentRespondents'
        ));
    }
}