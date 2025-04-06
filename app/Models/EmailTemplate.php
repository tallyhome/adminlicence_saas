<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'description',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function getVariablesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setVariablesAttribute($value)
    {
        $this->attributes['variables'] = json_encode($value);
    }
}