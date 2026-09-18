<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRead extends Model
{
    protected $primaryKey = 'material_read_id';

    protected $fillable = ['respondent_id', 'material_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'respondent_id', 'respondent_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}