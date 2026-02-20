<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionItemRequest extends FormRequest
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
            'plan_price_id' => ['sometimes', 'string', 'max:255', 'exists:plan_prices,id'],
            'feature_id' => ['sometimes', 'string', 'max:255', 'exists:features,id'],
            'quantity' => ['sometimes', 'integer', 'max:11'],
            'unit_price' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'metadata' => ['nullable', 'json'],
            'tiers' => ['nullable', 'json'],
            'effective_from' => ['sometimes', 'date'],
            'effective_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'plan_price_id.string' => 'The plan price id must be a string.',
            'plan_price_id.max' => 'The plan price id must not exceed 255 characters.',
            'plan_price_id.exists' => 'The selected plan price id is invalid or does not exist in plan prices.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.max' => 'The quantity must not exceed 11 characters.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.regex' => 'The unit price must be a valid decimal number.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'tiers.json' => 'The tiers must be a valid JSON string.',
            'effective_from.date' => 'The effective from must be a valid date and time.',
            'effective_to.date' => 'The effective to must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
