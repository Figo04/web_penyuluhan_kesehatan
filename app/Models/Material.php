<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'title', 'description', 'type',
        'content', 'duration', 'order'
    ];

    public function reads()
    {
        return $this->hasMany(MaterialRead::class);
    }
}