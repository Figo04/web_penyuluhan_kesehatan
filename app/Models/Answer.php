<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $primaryKey = 'answer_id';

    protected $fillable = [
        'respondent_id', 'question_id',
        'question_option_id', 'test_type'
    ];

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'respondent_id', 'respondent_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id', 'question_option_id');
    }
}