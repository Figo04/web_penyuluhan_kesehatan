<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class ConsultationController extends Controller
{
    public function index()
    {
        $doctors = Doctor::orderBy('name')->get();
        return view('respondent.consultation', compact('doctors'));
    }
}