<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_type_id',
        'value',
    ];

    /**
     * Relationship between property and property type
     */
    public function propertyType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Relationship between property and (lost and found)
     */
    public function propertiable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
