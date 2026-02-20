<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanPriceRequest extends FormRequest
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
            'currency' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'interval' => ['required', 'string', 'max:255'],
            'interval_count' => ['required', 'integer', 'max:11'],
            'usage_type' => ['required', 'string', 'max:255'],
            'tiers' => ['nullable', 'json'],
            'transformations' => ['nullable', 'json'],
            'stripe_price_id' => ['nullable', 'string', 'max:255', 'ip'],
            'active_from' => ['required', 'date'],
            'active_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'plan_id.required' => 'The plan id field is required.',
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'currency.required' => 'The currency field is required.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'interval.required' => 'The interval field is required.',
            'interval.string' => 'The interval must be a string.',
            'interval.max' => 'The interval must not exceed 255 characters.',
            'interval_count.required' => 'The interval count field is required.',
            'interval_count.integer' => 'The interval count must be an integer.',
            'interval_count.max' => 'The interval count must not exceed 11 characters.',
            'usage_type.required' => 'The usage type field is required.',
            'usage_type.string' => 'The usage type must be a string.',
            'usage_type.max' => 'The usage type must not exceed 255 characters.',
            'tiers.json' => 'The tiers must be a valid JSON string.',
            'transformations.json' => 'The transformations must be a valid JSON string.',
            'stripe_price_id.string' => 'The stripe price id must be a string.',
            'stripe_price_id.max' => 'The stripe price id must not exceed 255 characters.',
            'active_from.required' => 'The active from field is required.',
            'active_from.date' => 'The active from must be a valid date and time.',
            'active_to.date' => 'The active to must be a valid date and time.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
