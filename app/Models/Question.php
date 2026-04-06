<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question_text',
        'type',           // pre | post
        'order',
        'question_format', // multiple_choice | likert
        'is_favourable',  // true/false (hanya untuk likert)
    ];

    protected $casts = [
        'is_favourable' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('label');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
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