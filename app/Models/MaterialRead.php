<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRead extends Model
{
    protected $fillable = ['respondent_id', 'material_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}