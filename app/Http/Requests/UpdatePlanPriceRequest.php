<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanPriceRequest extends FormRequest
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
            'currency' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'interval' => ['sometimes', 'string', 'max:255'],
            'interval_count' => ['sometimes', 'integer', 'max:11'],
            'usage_type' => ['sometimes', 'string', 'max:255'],
            'tiers' => ['nullable', 'json'],
            'transformations' => ['nullable', 'json'],
            'stripe_price_id' => ['nullable', 'string', 'max:255', 'sometimes', 'ip'],
            'active_from' => ['sometimes', 'date'],
            'active_to' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'interval.string' => 'The interval must be a string.',
            'interval.max' => 'The interval must not exceed 255 characters.',
            'interval_count.integer' => 'The interval count must be an integer.',
            'interval_count.max' => 'The interval count must not exceed 11 characters.',
            'usage_type.string' => 'The usage type must be a string.',
            'usage_type.max' => 'The usage type must not exceed 255 characters.',
            'tiers.json' => 'The tiers must be a valid JSON string.',
            'transformations.json' => 'The transformations must be a valid JSON string.',
            'stripe_price_id.string' => 'The stripe price id must be a string.',
            'stripe_price_id.max' => 'The stripe price id must not exceed 255 characters.',
            'active_from.date' => 'The active from must be a valid date and time.',
            'active_to.date' => 'The active to must be a valid date and time.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
