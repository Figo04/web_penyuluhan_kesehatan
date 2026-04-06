<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'label',       // A/B/C/D/E (MC) atau SS/S/R/TS/STS (Likert)
        'option_text',
        'is_correct',  // hanya relevan untuk multiple_choice
        'score',       // skor per pilihan (MC: 10 jika benar, 0 jika salah | Likert: 1–5)
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}