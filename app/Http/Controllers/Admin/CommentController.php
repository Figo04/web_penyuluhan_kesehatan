<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        // Ambil semua materi beserta jumlah komentar & komentar pending (belum dibalas)
        $materials = Material::withCount([
            'comments',
            'comments as pending_count' => fn ($q) => $q->whereNull('admin_reply'),
        ])
        ->orderByDesc('pending_count')
        ->orderBy('title')
        ->get();

        return view('admin.comments.index', compact('materials'));
    }

    public function show(Material $material)
    {
        $comments = MaterialComment::with('respondent')
            ->where('material_id', $material->material_id)
            ->latest()
            ->get();

        return view('admin.comments.show', compact('material', 'comments'));
    }

    public function reply(Request $request, MaterialComment $comment)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ], [
            'admin_reply.required' => 'Balasan tidak boleh kosong.',
        ]);

        $comment->update([
            'admin_reply' => $request->admin_reply,
            'replied_at'  => now(),
        ]);

        return back()->with('success', 'Balasan berhasil disimpan.');
    }

    public function destroy(MaterialComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}