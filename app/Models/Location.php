<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'location_id';

    protected $fillable = ['name'];

    public function respondents()
    {
        return $this->hasMany(Respondent::class, 'location_id', 'location_id');
    }
}