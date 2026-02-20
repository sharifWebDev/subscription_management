<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionItemRequest extends FormRequest
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
            'plan_price_id' => ['required', 'string', 'max:255', 'exists:plan_prices,id'],
            'feature_id' => ['required', 'string', 'max:255', 'exists:features,id'],
            'quantity' => ['required', 'integer', 'max:11'],
            'unit_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'metadata' => ['nullable', 'json'],
            'tiers' => ['nullable', 'json'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.required' => 'The subscription id field is required.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'plan_price_id.required' => 'The plan price id field is required.',
            'plan_price_id.string' => 'The plan price id must be a string.',
            'plan_price_id.max' => 'The plan price id must not exceed 255 characters.',
            'plan_price_id.exists' => 'The selected plan price id is invalid or does not exist in plan prices.',
            'feature_id.required' => 'The feature id field is required.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.max' => 'The quantity must not exceed 11 characters.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.regex' => 'The unit price must be a valid decimal number.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'tiers.json' => 'The tiers must be a valid JSON string.',
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
