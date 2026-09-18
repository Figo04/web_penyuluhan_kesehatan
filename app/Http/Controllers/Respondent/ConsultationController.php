<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $respondentId  = session('respondent_id');

        $consultations = Consultation::where('respondent_id', $respondentId)
            ->latest()
            ->get();

        return view('respondent.consultation', compact('consultations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string|max:2000',
        ], [
            'question_text.required' => 'Pertanyaan tidak boleh kosong.',
            'question_text.max'      => 'Pertanyaan maksimal 2000 karakter.',
        ]);

        Consultation::create([
            'respondent_id' => session('respondent_id'),
            'question_text' => $request->question_text,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Pertanyaan berhasil dikirim. Tim dokter akan segera menjawab.');
    }
}