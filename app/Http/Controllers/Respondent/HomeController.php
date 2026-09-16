<?php
namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Material;

class HomeController extends Controller
{
    public function index()
    {
        $respondent = Respondent::find(session('respondent_id'));
        $totalMaterials = Material::count();
        $readMaterials = $respondent->materialReads()->count();

        // Kunci tahap dihitung di sini, dengan aturan yang sama persis seperti
        // yang ditegakkan server di MaterialController::index dan
        // TestController::stageBlocked. Blade hanya menggambar hasilnya.
        $materiTerkunci   = !$respondent->pre_test_done;
        $postTestTerkunci = !$respondent->pre_test_done
            || (!$respondent->material_done && $totalMaterials > 0);

        $statusPre    = $respondent->pre_test_done ? 'done' : 'current';
        $statusMateri = $respondent->material_done ? 'done' : ($materiTerkunci ? 'locked' : 'current');
        $statusPost   = $respondent->post_test_done ? 'done' : ($postTestTerkunci ? 'locked' : 'current');

        $tahapSelesai = collect([
            $respondent->pre_test_done,
            $respondent->material_done,
            $respondent->post_test_done,
        ])->filter()->count();

        // Tombol utama mengikuti tahap yang sedang berjalan. null = semua selesai.
        $cta = match (true) {
            !$respondent->pre_test_done => ['label' => 'Mulai Pre-Test', 'url' => route('respondent.pretest')],
            !$respondent->material_done && $totalMaterials > 0 => ['label' => 'Lanjutkan Materi', 'url' => route('respondent.material')],
            !$respondent->post_test_done => ['label' => 'Kerjakan Post-Test', 'url' => route('respondent.posttest')],
            default => null,
        };

        $sapaan = $respondent->gender === 'laki-laki' ? 'Bapak' : 'Ibu';

        return view('respondent.home', compact(
            'respondent', 'totalMaterials', 'readMaterials',
            'materiTerkunci', 'postTestTerkunci',
            'statusPre', 'statusMateri', 'statusPost',
            'tahapSelesai', 'cta', 'sapaan'
        ));
    }
}
