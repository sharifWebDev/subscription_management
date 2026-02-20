<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeteredUsageAggregateRequest extends FormRequest
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
            'aggregate_date' => ['sometimes', 'date'],
            'aggregate_period' => ['sometimes', 'string', 'max:255'],
            'total_quantity' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tier1_quantity' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tier2_quantity' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tier3_quantity' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total_amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'record_count' => ['sometimes', 'integer', 'max:11'],
            'last_calculated_at' => ['sometimes', 'date'],
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
            'aggregate_date.date' => 'The aggregate date must be a valid date.',
            'aggregate_period.string' => 'The aggregate period must be a string.',
            'aggregate_period.max' => 'The aggregate period must not exceed 255 characters.',
            'total_quantity.numeric' => 'The total quantity must be a number.',
            'total_quantity.regex' => 'The total quantity must be a valid decimal number.',
            'tier1_quantity.numeric' => 'The tier1 quantity must be a number.',
            'tier1_quantity.regex' => 'The tier1 quantity must be a valid decimal number.',
            'tier2_quantity.numeric' => 'The tier2 quantity must be a number.',
            'tier2_quantity.regex' => 'The tier2 quantity must be a valid decimal number.',
            'tier3_quantity.numeric' => 'The tier3 quantity must be a number.',
            'tier3_quantity.regex' => 'The tier3 quantity must be a valid decimal number.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.regex' => 'The total amount must be a valid decimal number.',
            'record_count.integer' => 'The record count must be an integer.',
            'record_count.max' => 'The record count must not exceed 11 characters.',
            'last_calculated_at.date' => 'The last calculated at must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
