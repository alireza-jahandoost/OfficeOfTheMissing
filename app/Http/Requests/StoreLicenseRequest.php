<?php

namespace App\Http\Requests;

use App\Enums\PropertyValueType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLicenseRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:30',
            'property_types' => 'required|array',
            'property_types.*.name' => 'required|string|max:50',
            'property_types.*.value_type' => ['required', new Enum(PropertyValueType::class)],
            'property_types.*.hint' => 'nullable|string|max:100',
            'property_types.*.show_to_finder' => 'required|boolean',
            'property_types.*.show_to_loser' => 'required|boolean',
        ];
    }
}
