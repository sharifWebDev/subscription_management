<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionOrderRequest extends FormRequest
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
            'user_id' => ['sometimes', 'string', 'max:255', 'exists:users,id'],
            'payment_master_id' => ['nullable', 'string', 'max:255', 'exists:payment_masters,id'],
            'order_number' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'subtotal' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax_amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total_amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['sometimes', 'string', 'max:255'],
            'customer_info' => ['nullable', 'json'],
            'billing_address' => ['nullable', 'json'],
            'ordered_at' => ['nullable', 'date'],
            'processed_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'coupon_code' => ['nullable', 'string', 'max:255'],
            'applied_discounts' => ['nullable', 'json'],
            'metadata' => ['nullable', 'json'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'failure_reason' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'payment_master_id.string' => 'The payment master id must be a string.',
            'payment_master_id.max' => 'The payment master id must not exceed 255 characters.',
            'payment_master_id.exists' => 'The selected payment master id is invalid or does not exist in payment masters.',
            'order_number.string' => 'The order number must be a string.',
            'order_number.max' => 'The order number must not exceed 255 characters.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'subtotal.numeric' => 'The subtotal must be a number.',
            'subtotal.regex' => 'The subtotal must be a valid decimal number.',
            'tax_amount.numeric' => 'The tax amount must be a number.',
            'tax_amount.regex' => 'The tax amount must be a valid decimal number.',
            'discount_amount.numeric' => 'The discount amount must be a number.',
            'discount_amount.regex' => 'The discount amount must be a valid decimal number.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.regex' => 'The total amount must be a valid decimal number.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'customer_info.json' => 'The customer info must be a valid JSON string.',
            'billing_address.json' => 'The billing address must be a valid JSON string.',
            'ordered_at.date' => 'The ordered at must be a valid date and time.',
            'processed_at.date' => 'The processed at must be a valid date and time.',
            'cancelled_at.date' => 'The cancelled at must be a valid date and time.',
            'coupon_code.string' => 'The coupon code must be a string.',
            'coupon_code.max' => 'The coupon code must not exceed 255 characters.',
            'applied_discounts.json' => 'The applied discounts must be a valid JSON string.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'notes.string' => 'The notes must be a string.',
            'notes.max' => 'The notes must not exceed 65535 characters.',
            'failure_reason.string' => 'The failure reason must be a string.',
            'failure_reason.max' => 'The failure reason must not exceed 65535 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
