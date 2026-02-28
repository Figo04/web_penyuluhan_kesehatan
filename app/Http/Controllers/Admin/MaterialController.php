<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::orderBy('order')->get();
        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:video,artikel,pdf',
            'content'     => 'required|string',
            'duration'    => 'nullable|integer',
            'order'       => 'required|integer',
        ]);

        Material::create($request->all());
        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    // ✅ Ganti $material → $materi agar cocok dengan route {materi}
    public function edit(Material $materi)
    {
        return view('admin.materials.edit', ['material' => $materi]);
    }

    public function update(Request $request, Material $materi)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:video,artikel,pdf',
            'content'     => 'required|string',
            'duration'    => 'nullable|integer',
            'order'       => 'required|integer',
        ]);

        $materi->update($request->all());
        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil diupdate.');
    }

    public function destroy(Material $materi)
    {
        $materi->delete();
        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}