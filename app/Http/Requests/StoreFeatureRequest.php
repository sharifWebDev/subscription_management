<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535', 'ip'],
            'type' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:255'],
            'is_resettable' => ['required', 'boolean', 'max:1'],
            'reset_period' => ['required', 'string', 'max:255'],
            'metadata' => ['nullable', 'json'],
            'validations' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 65535 characters.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'scope.required' => 'The scope field is required.',
            'scope.string' => 'The scope must be a string.',
            'scope.max' => 'The scope must not exceed 255 characters.',
            'is_resettable.required' => 'The is resettable field is required.',
            'is_resettable.boolean' => 'The is resettable must be true or false.',
            'is_resettable.max' => 'The is resettable must not exceed 1 characters.',
            'reset_period.required' => 'The reset period field is required.',
            'reset_period.string' => 'The reset period must be a string.',
            'reset_period.max' => 'The reset period must not exceed 255 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'validations.json' => 'The validations must be a valid JSON string.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
