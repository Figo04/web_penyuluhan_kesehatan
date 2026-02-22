<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_text', 'type', 'order'];

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('label');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}