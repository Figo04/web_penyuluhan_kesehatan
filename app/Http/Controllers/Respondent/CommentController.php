<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Material $material)
    {
        $request->validate([
            'comment_text' => 'required|string|max:1000',
        ], [
            'comment_text.required' => 'Komentar tidak boleh kosong.',
            'comment_text.max'      => 'Komentar maksimal 1000 karakter.',
        ]);

        MaterialComment::create([
            'material_id'  => $material->material_id,
            'respondent_id' => session('respondent_id'),
            'comment_text'  => $request->comment_text,
        ]);

        return back()->with('success', 'Komentar berhasil dikirim.');
    }

    public function destroy(MaterialComment $comment)
    {
        // Hanya pemilik komentar yang boleh hapus
        if ($comment->respondent_id !== session('respondent_id')) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}