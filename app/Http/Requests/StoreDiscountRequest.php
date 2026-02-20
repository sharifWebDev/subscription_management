<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['nullable', 'string', 'max:255'],
            'applies_to' => ['required', 'string', 'max:255'],
            'applies_to_ids' => ['nullable', 'json'],
            'max_redemptions' => ['nullable', 'integer', 'max:11'],
            'times_redeemed' => ['required', 'integer', 'max:11'],
            'is_active' => ['required', 'boolean', 'max:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'duration' => ['required', 'string', 'max:255'],
            'duration_in_months' => ['nullable', 'integer', 'max:11'],
            'metadata' => ['nullable', 'json'],
            'restrictions' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'applies_to.required' => 'The applies to field is required.',
            'applies_to.string' => 'The applies to must be a string.',
            'applies_to.max' => 'The applies to must not exceed 255 characters.',
            'applies_to_ids.json' => 'The applies to ids must be a valid JSON string.',
            'max_redemptions.integer' => 'The max redemptions must be an integer.',
            'max_redemptions.max' => 'The max redemptions must not exceed 11 characters.',
            'times_redeemed.required' => 'The times redeemed field is required.',
            'times_redeemed.integer' => 'The times redeemed must be an integer.',
            'times_redeemed.max' => 'The times redeemed must not exceed 11 characters.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active must be true or false.',
            'is_active.max' => 'The is active must not exceed 1 characters.',
            'starts_at.date' => 'The starts at must be a valid date and time.',
            'expires_at.date' => 'The expires at must be a valid date and time.',
            'duration.required' => 'The duration field is required.',
            'duration.string' => 'The duration must be a string.',
            'duration.max' => 'The duration must not exceed 255 characters.',
            'duration_in_months.integer' => 'The duration in months must be an integer.',
            'duration_in_months.max' => 'The duration in months must not exceed 11 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'restrictions.json' => 'The restrictions must be a valid JSON string.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
