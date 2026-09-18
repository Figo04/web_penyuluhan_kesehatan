<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialComment extends Model
{
    protected $primaryKey = 'material_comment_id';

    protected $fillable = [
        'material_id',
        'respondent_id',
        'comment_text',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class, 'respondent_id', 'respondent_id');
    }

    public function hasReply(): bool
    {
        return !is_null($this->admin_reply);
    }
}