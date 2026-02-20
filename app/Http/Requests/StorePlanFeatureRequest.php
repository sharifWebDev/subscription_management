<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanFeatureRequest extends FormRequest
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
            'plan_id' => ['required', 'string', 'max:255', 'exists:plans,id'],
            'feature_id' => ['required', 'string', 'max:255', 'exists:features,id'],
            'value' => ['required', 'string', 'max:255'],
            'config' => ['nullable', 'json'],
            'sort_order' => ['required', 'integer', 'max:11'],
            'is_inherited' => ['required', 'boolean', 'max:1'],
            'parent_feature_id' => ['nullable', 'string', 'max:255'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'plan_id.required' => 'The plan id field is required.',
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'feature_id.required' => 'The feature id field is required.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'value.required' => 'The value field is required.',
            'value.string' => 'The value must be a string.',
            'value.max' => 'The value must not exceed 255 characters.',
            'config.json' => 'The config must be a valid JSON string.',
            'sort_order.required' => 'The sort order field is required.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.max' => 'The sort order must not exceed 11 characters.',
            'is_inherited.required' => 'The is inherited field is required.',
            'is_inherited.boolean' => 'The is inherited must be true or false.',
            'is_inherited.max' => 'The is inherited must not exceed 1 characters.',
            'parent_feature_id.string' => 'The parent feature id must be a string.',
            'parent_feature_id.max' => 'The parent feature id must not exceed 255 characters.',
            'effective_from.required' => 'The effective from field is required.',
            'effective_from.date' => 'The effective from must be a valid date and time.',
            'effective_to.date' => 'The effective to must be a valid date and time.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
