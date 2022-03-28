<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @property mixed $founds
 * @property mixed $losts
 */
class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     *  Relationship between license and property type
     */
    public function propertyTypes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PropertyType::class);
    }

    /**
     * Relationship between license and found
     */
    public function founds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Found::class);
    }

    /**
     * Relationship between license and lost
     */
    public function losts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lost::class);
    }
}
