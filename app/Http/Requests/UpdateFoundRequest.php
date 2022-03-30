<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return $license->propertyTypes()->exceptShowToLoser()
            ->get()->reduce(function($carry, $propertyType){
                $carry["property_type$propertyType->id"] = 'array';
                $carry["property_type$propertyType->id.value"] =
                    $propertyType->value_type === 'text' ?
                    'nullable|string|max:100' :
                    'nullable|image|max:2000';
                return $carry;
            }, []);
    }
}
