<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'example'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($variable) {
            if (!str_starts_with($variable->name, '{')) {
                $variable->name = '{' . $variable->name;
            }
            if (!str_ends_with($variable->name, '}')) {
                $variable->name .= '}';
            }
        });
    }
} 