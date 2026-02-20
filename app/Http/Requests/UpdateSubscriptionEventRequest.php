<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionEventRequest extends FormRequest
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
            'type' => ['sometimes', 'string', 'max:255'],
            'data' => ['nullable', 'json'],
            'changes' => ['nullable', 'json'],
            'causer_id' => ['nullable', 'string', 'max:255'],
            'causer_type' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:255', 'sometimes', 'ip'],
            'user_agent' => ['nullable', 'string', 'max:65535'],
            'metadata' => ['nullable', 'json'],
            'occurred_at' => ['sometimes', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'data.json' => 'The data must be a valid JSON string.',
            'changes.json' => 'The changes must be a valid JSON string.',
            'causer_id.string' => 'The causer id must be a string.',
            'causer_id.max' => 'The causer id must not exceed 255 characters.',
            'causer_type.string' => 'The causer type must be a string.',
            'causer_type.max' => 'The causer type must not exceed 255 characters.',
            'ip_address.string' => 'The ip address must be a string.',
            'ip_address.max' => 'The ip address must not exceed 255 characters.',
            'user_agent.string' => 'The user agent must be a string.',
            'user_agent.max' => 'The user agent must not exceed 65535 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'occurred_at.date' => 'The occurred at must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
