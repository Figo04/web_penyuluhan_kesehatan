<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialRead;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $respondentId = session('respondent_id');

        $materials = Material::with(['comments.respondent'])
            ->orderBy('order')
            ->get();

        $readMaterialIds = $respondentId
            ? MaterialRead::where('respondent_id', $respondentId)
                ->pluck('material_id')
                ->toArray()
            : [];

        return view('respondent.material', compact('materials', 'readMaterialIds'));
    }

    public function markAsRead(Request $request, Material $material)
    {
        $respondentId = session('respondent_id');

        if (!$respondentId) {
            return response()->json(['status' => 'error', 'message' => 'Session kosong'], 401);
        }

        try {
            MaterialRead::firstOrCreate(
                // Kondisi pencarian — cari record yang sudah ada
                [
                    'material_id'   => $material->material_id,
                    'respondent_id' => $respondentId,
                ],
                // Nilai tambahan saat CREATE (bukan untuk pencarian)
                [
                    'read_at' => now(),
                ]
            );

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}