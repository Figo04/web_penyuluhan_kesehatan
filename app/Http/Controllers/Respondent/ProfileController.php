<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Respondent;

class ProfileController extends Controller
{
    public function index()
    {
        $respondent = Respondent::with('location')->find(session('respondent_id'));
        return view('respondent.profile', compact('respondent'));
    }
}