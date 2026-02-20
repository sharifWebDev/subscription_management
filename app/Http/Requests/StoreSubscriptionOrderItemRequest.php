<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionOrderItemRequest extends FormRequest
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
            'subscription_order_id' => ['required', 'string', 'max:255', 'ip', 'exists:subscription_orders,id'],
            'plan_id' => ['required', 'string', 'max:255', 'exists:plans,id'],
            'user_id' => ['required', 'string', 'max:255', 'exists:users,id'],
            'recipient_user_id' => ['nullable', 'string', 'max:255', 'ip', 'exists:users,id'],
            'subscription_id' => ['nullable', 'string', 'max:255', 'ip', 'exists:subscriptions,id'],
            'plan_name' => ['required', 'string', 'max:255'],
            'billing_cycle' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'max:11'],
            'recipient_info' => ['nullable', 'json', 'ip'],
            'unit_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'subscription_status' => ['required', 'string', 'max:255', 'ip'],
            'processing_error' => ['nullable', 'string', 'max:65535'],
            'processed_at' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_order_id.required' => 'The subscription order id field is required.',
            'subscription_order_id.string' => 'The subscription order id must be a string.',
            'subscription_order_id.max' => 'The subscription order id must not exceed 255 characters.',
            'subscription_order_id.exists' => 'The selected subscription order id is invalid or does not exist in subscription orders.',
            'plan_id.required' => 'The plan id field is required.',
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'user_id.required' => 'The user id field is required.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'recipient_user_id.string' => 'The recipient user id must be a string.',
            'recipient_user_id.max' => 'The recipient user id must not exceed 255 characters.',
            'recipient_user_id.exists' => 'The selected recipient user id is invalid or does not exist in users.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'plan_name.required' => 'The plan name field is required.',
            'plan_name.string' => 'The plan name must be a string.',
            'plan_name.max' => 'The plan name must not exceed 255 characters.',
            'billing_cycle.required' => 'The billing cycle field is required.',
            'billing_cycle.string' => 'The billing cycle must be a string.',
            'billing_cycle.max' => 'The billing cycle must not exceed 255 characters.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.max' => 'The quantity must not exceed 11 characters.',
            'recipient_info.json' => 'The recipient info must be a valid JSON string.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.regex' => 'The unit price must be a valid decimal number.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'tax_amount.required' => 'The tax amount field is required.',
            'tax_amount.numeric' => 'The tax amount must be a number.',
            'tax_amount.regex' => 'The tax amount must be a valid decimal number.',
            'discount_amount.required' => 'The discount amount field is required.',
            'discount_amount.numeric' => 'The discount amount must be a number.',
            'discount_amount.regex' => 'The discount amount must be a valid decimal number.',
            'total_amount.required' => 'The total amount field is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.regex' => 'The total amount must be a valid decimal number.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.date' => 'The end date must be a valid date.',
            'subscription_status.required' => 'The subscription status field is required.',
            'subscription_status.string' => 'The subscription status must be a string.',
            'subscription_status.max' => 'The subscription status must not exceed 255 characters.',
            'processing_error.string' => 'The processing error must be a string.',
            'processing_error.max' => 'The processing error must not exceed 65535 characters.',
            'processed_at.date' => 'The processed at must be a valid date and time.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
