<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateLimitRequest extends FormRequest
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
            'subscription_id' => ['sometimes', 'string', 'max:255', 'ip', 'exists:subscriptions,id'],
            'feature_id' => ['sometimes', 'string', 'max:255', 'exists:features,id'],
            'key' => ['sometimes', 'string', 'max:255'],
            'max_attempts' => ['sometimes', 'integer', 'max:11'],
            'decay_seconds' => ['sometimes', 'integer', 'max:11'],
            'remaining' => ['sometimes', 'integer', 'max:11'],
            'resets_at' => ['sometimes', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'key.string' => 'The key must be a string.',
            'key.max' => 'The key must not exceed 255 characters.',
            'max_attempts.integer' => 'The max attempts must be an integer.',
            'max_attempts.max' => 'The max attempts must not exceed 11 characters.',
            'decay_seconds.integer' => 'The decay seconds must be an integer.',
            'decay_seconds.max' => 'The decay seconds must not exceed 11 characters.',
            'remaining.integer' => 'The remaining must be an integer.',
            'remaining.max' => 'The remaining must not exceed 11 characters.',
            'resets_at.date' => 'The resets at must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
