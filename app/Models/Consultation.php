<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $primaryKey = 'consultation_id';

    protected $fillable = [
        'respondent_id',
        'question_text',
        'admin_reply',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'respondent_id', 'respondent_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAnswered(): bool
    {
        return $this->status === 'answered';
    }
}