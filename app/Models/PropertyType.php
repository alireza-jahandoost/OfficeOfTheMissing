<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $propertyType)
 * @method static first()
 */
class PropertyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value_type',
        'hint',
        'license_id',
        'show_to_finder',
        'show_to_loser',
    ];

    /**
     * scope to get found and global properties
     */
    public function scopeExceptShowToLoser($query){
        return $query->where('show_to_finder', true);
    }

    /**
     * scope to get lost and global properties
     */
    public function scopeExceptShowToFinder($query){
        return $query->where('show_to_loser', true);
    }

    /**
     * Relationship between property type and license
     */
    public function license(){
        return $this->belongsTo(License::class);
    }

    /**
     * Relationship between property and property type
     */
    public function properties(){
        return $this->hasMany(Property::class);
    }
}
