<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Location;

class DashboardController extends Controller
{
    public function index()
    {
        // Existing stats
        $totalRespondents = Respondent::count();
        $preTestDone = Respondent::where('pre_test_done', true)->count();
        $postTestDone = Respondent::where('post_test_done', true)->count();
        $completed = Respondent::where('pre_test_done', true)->where('post_test_done', true)->count();
        $recentRespondents = Respondent::with('location')->latest()->take(10)->get();

        // New stats for charts
        $neitherTest = Respondent::where('pre_test_done', false)->where('post_test_done', false)->count();
        $preOnly = Respondent::where('pre_test_done', true)->where('post_test_done', false)->count();
        $postOnly = Respondent::where('pre_test_done', false)->where('post_test_done', true)->count();
        $bothTest = $completed; // same as above
        
        // Material completion
        $materialDone = Respondent::where('material_done', true)->count();
        $materialNotDone = $totalRespondents - $materialDone;
        
        // Gender distribution
        $genderMale = Respondent::where('gender', 'laki-laki')->count();
        $genderFemale = Respondent::where('gender', 'perempuan')->count();
        
        // Age distribution (groups)
        $ageGroups = [
            '17-25' => Respondent::whereBetween('age', [17, 25])->count(),
            '26-35' => Respondent::whereBetween('age', [26, 35])->count(),
            '36-45' => Respondent::whereBetween('age', [36, 45])->count(),
            '46-55' => Respondent::whereBetween('age', [46, 55])->count(),
            '56+'   => Respondent::where('age', '>=', 56)->count(),
        ];
        
        // Location distribution (top 5)
        $locationStats = Respondent::with('location')
            ->selectRaw('location_id, locations.name as location_name, count(*) as count')
            ->join('locations', 'respondents.location_id', '=', 'locations.id')
            ->groupBy('location_id', 'locations.name')
            ->orderByRaw('count DESC')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->location_name ?? 'Tidak Diketahui',
                    'count' => (int) $item->count
                ];
            });

        return view('admin.dashboard', compact(
            'totalRespondents', 'preTestDone', 'postTestDone', 'completed', 'recentRespondents',
            'neitherTest', 'preOnly', 'postOnly', 'bothTest',
            'materialDone', 'materialNotDone',
            'genderMale', 'genderFemale',
            'ageGroups',
            'locationStats'
        ));
    }
}