<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the serial keys associated with the project.
     */
    public function serialKeys(): HasMany
    {
        return $this->hasMany(SerialKey::class);
    }

    /**
     * Get the active serial keys count for this project.
     */
    public function activeKeysCount(): int
    {
        return $this->serialKeys()->where('status', 'active')->count();
    }
}