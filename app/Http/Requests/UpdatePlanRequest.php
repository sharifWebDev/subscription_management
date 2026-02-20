<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535', 'sometimes', 'ip'],
            'type' => ['sometimes', 'string', 'max:255'],
            'billing_period' => ['sometimes', 'string', 'max:255'],
            'billing_interval' => ['sometimes', 'integer', 'max:11'],
            'is_active' => ['sometimes', 'boolean', 'max:1'],
            'is_visible' => ['sometimes', 'boolean', 'max:1'],
            'sort_order' => ['sometimes', 'integer', 'max:11'],
            'is_featured' => ['sometimes', 'boolean', 'max:1'],
            'metadata' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'slug.string' => 'The slug must be a string.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes, and underscores.',
            'slug.unique' => 'The slug has already been taken.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 65535 characters.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'billing_period.string' => 'The billing period must be a string.',
            'billing_period.max' => 'The billing period must not exceed 255 characters.',
            'billing_interval.integer' => 'The billing interval must be an integer.',
            'billing_interval.max' => 'The billing interval must not exceed 11 characters.',
            'is_active.boolean' => 'The is active must be true or false.',
            'is_active.max' => 'The is active must not exceed 1 characters.',
            'is_visible.boolean' => 'The is visible must be true or false.',
            'is_visible.max' => 'The is visible must not exceed 1 characters.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.max' => 'The sort order must not exceed 11 characters.',
            'is_featured.boolean' => 'The is featured must be true or false.',
            'is_featured.max' => 'The is featured must not exceed 1 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
