<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with('respondent')
            ->latest()
            ->get();

        $pendingCount  = $consultations->where('status', 'pending')->count();
        $answeredCount = $consultations->where('status', 'answered')->count();

        return view('admin.consultations.index', compact('consultations', 'pendingCount', 'answeredCount'));
    }

    public function reply(Request $request, Consultation $consultation)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ], [
            'admin_reply.required' => 'Jawaban tidak boleh kosong.',
        ]);

        $consultation->update([
            'admin_reply' => $request->admin_reply,
            'replied_at'  => now(),
            'status'      => 'answered',
        ]);

        return back()->with('success', 'Jawaban berhasil disimpan.');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return back()->with('success', 'Konsultasi berhasil dihapus.');
    }
}