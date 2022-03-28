<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $propertyType)
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
     * Relationship between property type and license
     */
    public function license(){
        return $this->belongsTo(License::class);
    }
}
