<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'question_text',
        'type',           // pre | post
        'category',       // umum | kondisi_anak (kondisi_anak hanya pre-test, tidak dinilai)
        'order',
        'question_format', // multiple_choice | likert
        'is_favourable',  // true/false (hanya untuk likert)
    ];

    protected $casts = [
        'is_favourable' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'question_id')->orderBy('label');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    /**
     * Cek apakah soal ini bertipe Likert
     */
    public function isLikert(): bool
    {
        return $this->question_format === 'likert';
    }

    /**
     * Cek apakah soal ini bertipe Multiple Choice
     */
    public function isMultipleChoice(): bool
    {
        return $this->question_format === 'multiple_choice';
    }
}