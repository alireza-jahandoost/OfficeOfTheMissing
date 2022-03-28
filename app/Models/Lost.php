<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|mixed|string|null $user_id
 * @property mixed $license_id
 * @property mixed $properties
 */
class Lost extends Model
{
    use HasFactory;

    /**
     * Relationship between lost and user
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship between lost and license
     */
    public function license(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Relationship between lost and property
     */
    public function properties(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Property::class,'propertiable');
    }
}
