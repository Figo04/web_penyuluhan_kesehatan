<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $primaryKey = 'child_id';

    protected $fillable = [
        'respondent_id', 'name', 'birth_date', 'gender',
        'child_order', 'weight_kg', 'height_cm',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'respondent_id', 'respondent_id');
    }

    public function ageInMonths(): int
    {
        return (int) $this->birth_date->diffInMonths(now());
    }

    /** Contoh: "2 th 3 bln" atau "8 bln" */
    public function ageLabel(): string
    {
        $months = $this->ageInMonths();
        $years  = intdiv($months, 12);
        return $years > 0
            ? $years . ' th ' . ($months % 12) . ' bln'
            : $months . ' bln';
    }
}
