<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Material;

class HomeController extends Controller
{
    public function index()
    {
        $respondent     = Respondent::find(session('respondent_id'));
        $totalMaterials = Material::count();
        $readMaterials  = $respondent->materialReads()->count();
        $materialDone   = $respondent->hasFinishedMaterials();

        return view('respondent.home', compact(
            'respondent',
            'totalMaterials',
            'readMaterials',
            'materialDone'
        ));
    }
}