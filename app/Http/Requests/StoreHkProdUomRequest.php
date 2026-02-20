<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHkProdUomRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean', 'max:1'],
            'sequence' => ['nullable', 'integer', 'max:20'],
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active must be true or false.',
            'is_active.max' => 'The is active must not exceed 1 characters.',
            'sequence.integer' => 'The sequence must be an integer.',
            'sequence.max' => 'The sequence must not exceed 20 characters.',
            'created_by.integer' => 'The created by must be an integer.',
            'created_by.max' => 'The created by must not exceed 11 characters.',
            'updated_by.integer' => 'The updated by must be an integer.',
            'updated_by.max' => 'The updated by must not exceed 11 characters.',
        ];
    }
}
