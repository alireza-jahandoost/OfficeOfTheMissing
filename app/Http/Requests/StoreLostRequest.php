<?php

namespace App\Http\Requests;

use App\Models\PropertyType;
use Illuminate\Foundation\Http\FormRequest;

class StoreLostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $license = request()->route('license');
        $rules = $license->propertyTypes()->exceptShowToFinder()->get()->reduce(function($carry, $propertyType){
            $carry["property_type$propertyType->id"] = 'array';
            $carry["property_type$propertyType->id.value"] = $propertyType->value_type === 'text' ?
                'required|string|max:100' :
                'required|image|max:2000';
            return $carry;
        }, []);

        return $rules;
    }
}
