<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Material;
use App\Models\MaterialRead;

class MaterialController extends Controller
{
    public function index()
    {
        $respondent = Respondent::find(session('respondent_id'));

        if (!$respondent->pre_test_done) {
            return redirect()->route('respondent.home')->with('error', 'Selesaikan Pre-Test terlebih dahulu.');
        }

        $materials = Material::orderBy('order')->get();
        $readMaterialIds = $respondent->materialReads()->pluck('material_id')->toArray();
return view('respondent.material', compact('materials', 'readMaterialIds', 'respondent'));

    }

    public function markAsRead(Material $material)
    {
        $respondent = Respondent::find(session('respondent_id'));

        MaterialRead::firstOrCreate(
            ['respondent_id' => $respondent->id, 'material_id' => $material->id],
            ['read_at' => now()]
        );

        $totalMaterials = Material::count();
        $readCount = $respondent->materialReads()->count();

        if ($readCount >= $totalMaterials && $totalMaterials > 0) {
            $respondent->update(['material_done' => true]);
        }

        return response()->json(['status' => 'ok', 'material_done' => $respondent->fresh()->material_done]);
    }
}