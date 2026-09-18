<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $primaryKey = 'material_id';

    protected $fillable = [
        'title',
        'description',
        'type',       // video | pdf | ppt | artikel
        'content',    // URL Google Drive / YouTube
        'duration',
        'order',
    ];

    public function reads()
    {
        return $this->hasMany(MaterialRead::class, 'material_id', 'material_id');
    }

    public function comments()
    {
        return $this->hasMany(MaterialComment::class, 'material_id', 'material_id')->latest();
    }
}