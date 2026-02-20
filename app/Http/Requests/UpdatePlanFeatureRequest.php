<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanFeatureRequest extends FormRequest
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
            'plan_id' => ['sometimes', 'string', 'max:255', 'exists:plans,id'],
            'feature_id' => ['sometimes', 'string', 'max:255', 'exists:features,id'],
            'value' => ['sometimes', 'string', 'max:255'],
            'config' => ['nullable', 'json'],
            'sort_order' => ['sometimes', 'integer', 'max:11'],
            'is_inherited' => ['sometimes', 'boolean', 'max:1'],
            'parent_feature_id' => ['nullable', 'string', 'max:255'],
            'effective_from' => ['sometimes', 'date'],
            'effective_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'value.string' => 'The value must be a string.',
            'value.max' => 'The value must not exceed 255 characters.',
            'config.json' => 'The config must be a valid JSON string.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.max' => 'The sort order must not exceed 11 characters.',
            'is_inherited.boolean' => 'The is inherited must be true or false.',
            'is_inherited.max' => 'The is inherited must not exceed 1 characters.',
            'parent_feature_id.string' => 'The parent feature id must be a string.',
            'parent_feature_id.max' => 'The parent feature id must not exceed 255 characters.',
            'effective_from.date' => 'The effective from must be a valid date and time.',
            'effective_to.date' => 'The effective to must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
