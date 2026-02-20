<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsageRecordRequest extends FormRequest
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
            'subscription_item_id' => ['required', 'string', 'max:255', 'ip', 'exists:subscription_items,id'],
            'feature_id' => ['required', 'string', 'max:255', 'exists:features,id'],
            'quantity' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tier_quantity' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'unit' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'recorded_at' => ['required', 'date'],
            'billing_date' => ['required', 'date'],
            'metadata' => ['nullable', 'json'],
            'dimensions' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.required' => 'The subscription id field is required.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'subscription_item_id.required' => 'The subscription item id field is required.',
            'subscription_item_id.string' => 'The subscription item id must be a string.',
            'subscription_item_id.max' => 'The subscription item id must not exceed 255 characters.',
            'subscription_item_id.exists' => 'The selected subscription item id is invalid or does not exist in subscription items.',
            'feature_id.required' => 'The feature id field is required.',
            'feature_id.string' => 'The feature id must be a string.',
            'feature_id.max' => 'The feature id must not exceed 255 characters.',
            'feature_id.exists' => 'The selected feature id is invalid or does not exist in features.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.regex' => 'The quantity must be a valid decimal number.',
            'tier_quantity.numeric' => 'The tier quantity must be a number.',
            'tier_quantity.regex' => 'The tier quantity must be a valid decimal number.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'unit.required' => 'The unit field is required.',
            'unit.string' => 'The unit must be a string.',
            'unit.max' => 'The unit must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'recorded_at.required' => 'The recorded at field is required.',
            'recorded_at.date' => 'The recorded at must be a valid date and time.',
            'billing_date.required' => 'The billing date field is required.',
            'billing_date.date' => 'The billing date must be a valid date.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'dimensions.json' => 'The dimensions must be a valid JSON string.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
