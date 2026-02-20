<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'max:255', 'exists:users,id'],
            'plan_id' => ['required', 'string', 'max:255', 'exists:plans,id'],
            'plan_price_id' => ['required', 'string', 'max:255', 'exists:plan_prices,id'],
            'parent_subscription_id' => ['nullable', 'string', 'max:255', 'ip'],
            'status' => ['required', 'string', 'max:255'],
            'billing_cycle_anchor' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'max:11'],
            'unit_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['required', 'string', 'max:255'],
            'trial_starts_at' => ['nullable', 'date'],
            'trial_ends_at' => ['nullable', 'date'],
            'trial_converted' => ['required', 'boolean', 'max:1'],
            'current_period_starts_at' => ['nullable', 'date'],
            'current_period_ends_at' => ['nullable', 'date'],
            'billing_cycle_anchor_date' => ['nullable', 'date'],
            'canceled_at' => ['nullable', 'date'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
            'prorate' => ['required', 'boolean', 'max:1'],
            'proration_amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'proration_date' => ['nullable', 'date'],
            'gateway' => ['required', 'string', 'max:255'],
            'gateway_subscription_id' => ['nullable', 'string', 'max:255', 'ip'],
            'gateway_customer_id' => ['nullable', 'string', 'max:255'],
            'gateway_metadata' => ['nullable', 'json'],
            'metadata' => ['nullable', 'json'],
            'history' => ['nullable', 'json'],
            'is_active' => ['required', 'boolean', 'max:1'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user id field is required.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'plan_id.required' => 'The plan id field is required.',
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'plan_price_id.required' => 'The plan price id field is required.',
            'plan_price_id.string' => 'The plan price id must be a string.',
            'plan_price_id.max' => 'The plan price id must not exceed 255 characters.',
            'plan_price_id.exists' => 'The selected plan price id is invalid or does not exist in plan prices.',
            'parent_subscription_id.string' => 'The parent subscription id must be a string.',
            'parent_subscription_id.max' => 'The parent subscription id must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'billing_cycle_anchor.required' => 'The billing cycle anchor field is required.',
            'billing_cycle_anchor.string' => 'The billing cycle anchor must be a string.',
            'billing_cycle_anchor.max' => 'The billing cycle anchor must not exceed 255 characters.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.max' => 'The quantity must not exceed 11 characters.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.regex' => 'The unit price must be a valid decimal number.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'currency.required' => 'The currency field is required.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'trial_starts_at.date' => 'The trial starts at must be a valid date and time.',
            'trial_ends_at.date' => 'The trial ends at must be a valid date and time.',
            'trial_converted.required' => 'The trial converted field is required.',
            'trial_converted.boolean' => 'The trial converted must be true or false.',
            'trial_converted.max' => 'The trial converted must not exceed 1 characters.',
            'current_period_starts_at.date' => 'The current period starts at must be a valid date and time.',
            'current_period_ends_at.date' => 'The current period ends at must be a valid date and time.',
            'billing_cycle_anchor_date.date' => 'The billing cycle anchor date must be a valid date and time.',
            'canceled_at.date' => 'The canceled at must be a valid date and time.',
            'cancellation_reason.string' => 'The cancellation reason must be a string.',
            'cancellation_reason.max' => 'The cancellation reason must not exceed 255 characters.',
            'prorate.required' => 'The prorate field is required.',
            'prorate.boolean' => 'The prorate must be true or false.',
            'prorate.max' => 'The prorate must not exceed 1 characters.',
            'proration_amount.numeric' => 'The proration amount must be a number.',
            'proration_amount.regex' => 'The proration amount must be a valid decimal number.',
            'proration_date.date' => 'The proration date must be a valid date and time.',
            'gateway.required' => 'The gateway field is required.',
            'gateway.string' => 'The gateway must be a string.',
            'gateway.max' => 'The gateway must not exceed 255 characters.',
            'gateway_subscription_id.string' => 'The gateway subscription id must be a string.',
            'gateway_subscription_id.max' => 'The gateway subscription id must not exceed 255 characters.',
            'gateway_customer_id.string' => 'The gateway customer id must be a string.',
            'gateway_customer_id.max' => 'The gateway customer id must not exceed 255 characters.',
            'gateway_metadata.json' => 'The gateway metadata must be a valid JSON string.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'history.json' => 'The history must be a valid JSON string.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active must be true or false.',
            'is_active.max' => 'The is active must not exceed 1 characters.',
        ];
    }
}
