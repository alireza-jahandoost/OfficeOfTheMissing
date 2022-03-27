<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;

    /**
     * Relationship between property type and license
     */
    public function license(){
        return $this->belongsTo(License::class);
    }
}
