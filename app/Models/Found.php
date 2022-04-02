<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $user_id
 * @property mixed $license_id
 */
class Found extends Model
{
    use HasFactory;

    /**
     * Relationship between found and user
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship between found and license
     */
    public function license(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Relationship between found and property
     */
    public function properties(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Property::class,'propertiable');
    }

    /**
     * Return found models related to a lost model
     */
    public function scopeRelatedToLost($query, Lost $lost){
        $commonPropertyTypes = PropertyType::where([
            ['license_id' , $lost->license_id],
            ['show_to_loser' , true],
            ['show_to_finder' , true]
        ])->get();

        $result = $query;
        foreach($commonPropertyTypes as $propertyType){
            $lostPropertyValue = $lost
                ->properties()
                ->where('property_type_id', $propertyType->id)
                ->value('value');

            $result = $result->whereHas('properties', function(Builder $query) use ($lostPropertyValue){
                $query->where('value', $lostPropertyValue);
            });
        }

        return $result;
    }
}
