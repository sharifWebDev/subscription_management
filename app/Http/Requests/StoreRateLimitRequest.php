<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateLimitRequest extends FormRequest
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
            'subscription_id' => ['required', 'string', 'max:255', 'ip', 'exists:subscriptions,id'],
            'feature_id' => ['required', 'string', 'max:255', 'exists:features,id'],
            'key' => ['required', 'string', 'max:255'],
            'max_attempts' => ['required', 'integer', 'max:11'],
            'decay_seconds' => ['required', 'integer', 'max:11'],
            'remaining' => ['required', 'integer', 'max:11'],
            'resets_at' => ['required', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.required' => 'The subscription id field is required.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'feature_id.required' => 'The feature id field is required.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'key.required' => 'The key field is required.',
            'key.string' => 'The key must be a string.',
            'key.max' => 'The key must not exceed 255 characters.',
            'max_attempts.required' => 'The max attempts field is required.',
            'max_attempts.integer' => 'The max attempts must be an integer.',
            'max_attempts.max' => 'The max attempts must not exceed 11 characters.',
            'decay_seconds.required' => 'The decay seconds field is required.',
            'decay_seconds.integer' => 'The decay seconds must be an integer.',
            'decay_seconds.max' => 'The decay seconds must not exceed 11 characters.',
            'remaining.required' => 'The remaining field is required.',
            'remaining.integer' => 'The remaining must be an integer.',
            'remaining.max' => 'The remaining must not exceed 11 characters.',
            'resets_at.required' => 'The resets at field is required.',
            'resets_at.date' => 'The resets at must be a valid date and time.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
