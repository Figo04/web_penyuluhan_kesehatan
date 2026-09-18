<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respondent extends Model
{
    protected $primaryKey = 'respondent_id';

    protected $fillable = [
        'access_code', 'name', 'age', 'gender',
        'marital_status', 'occupation', 'total_children',
        'medical_history', 'location_id', 'pre_test_done',
        'material_done', 'post_test_done', 'pre_test_at', 'post_test_at'
    ];

    protected $casts = [
        'medical_history' => 'array',
        'pre_test_done'   => 'boolean',
        'material_done'   => 'boolean',
        'post_test_done'  => 'boolean',
        'pre_test_at'     => 'datetime',
        'post_test_at'    => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function child()
    {
        return $this->hasOne(Child::class, 'respondent_id', 'respondent_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'respondent_id', 'respondent_id');
    }

    public function materialReads()
    {
        return $this->hasMany(MaterialRead::class, 'respondent_id', 'respondent_id');
    }

    /** Materi dianggap selesai kalau semua materi sudah dibaca */
    public function hasFinishedMaterials(): bool
    {
        $total = Material::count();
        return $total > 0 && $this->materialReads()->count() >= $total;
    }
}