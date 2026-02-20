<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMasterRequest extends FormRequest
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
            'payment_number' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'subtotal' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'fee_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'net_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'paid_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'due_amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['required', 'string', 'max:255'],
            'exchange_rate' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'base_currency' => ['required', 'string', 'max:255'],
            'base_amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payment_method_details' => ['nullable', 'json'],
            'payment_gateway' => ['nullable', 'string', 'max:255'],
            'is_installment' => ['required', 'boolean', 'max:1'],
            'installment_count' => ['nullable', 'integer', 'max:11'],
            'installment_frequency' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'customer_reference' => ['nullable', 'string', 'max:255'],
            'bank_reference' => ['nullable', 'string', 'max:255'],
            'gateway_reference' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'json'],
            'custom_fields' => ['nullable', 'json'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'failure_reason' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user id field is required.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'payment_number.required' => 'The payment number field is required.',
            'payment_number.string' => 'The payment number must be a string.',
            'payment_number.max' => 'The payment number must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'total_amount.required' => 'The total amount field is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.regex' => 'The total amount must be a valid decimal number.',
            'subtotal.required' => 'The subtotal field is required.',
            'subtotal.numeric' => 'The subtotal must be a number.',
            'subtotal.regex' => 'The subtotal must be a valid decimal number.',
            'tax_amount.required' => 'The tax amount field is required.',
            'tax_amount.numeric' => 'The tax amount must be a number.',
            'tax_amount.regex' => 'The tax amount must be a valid decimal number.',
            'discount_amount.required' => 'The discount amount field is required.',
            'discount_amount.numeric' => 'The discount amount must be a number.',
            'discount_amount.regex' => 'The discount amount must be a valid decimal number.',
            'fee_amount.required' => 'The fee amount field is required.',
            'fee_amount.numeric' => 'The fee amount must be a number.',
            'fee_amount.regex' => 'The fee amount must be a valid decimal number.',
            'net_amount.required' => 'The net amount field is required.',
            'net_amount.numeric' => 'The net amount must be a number.',
            'net_amount.regex' => 'The net amount must be a valid decimal number.',
            'paid_amount.required' => 'The paid amount field is required.',
            'paid_amount.numeric' => 'The paid amount must be a number.',
            'paid_amount.regex' => 'The paid amount must be a valid decimal number.',
            'due_amount.numeric' => 'The due amount must be a number.',
            'due_amount.regex' => 'The due amount must be a valid decimal number.',
            'currency.required' => 'The currency field is required.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'exchange_rate.required' => 'The exchange rate field is required.',
            'exchange_rate.numeric' => 'The exchange rate must be a number.',
            'exchange_rate.regex' => 'The exchange rate must be a valid decimal number.',
            'base_currency.required' => 'The base currency field is required.',
            'base_currency.string' => 'The base currency must be a string.',
            'base_currency.max' => 'The base currency must not exceed 255 characters.',
            'base_amount.numeric' => 'The base amount must be a number.',
            'base_amount.regex' => 'The base amount must be a valid decimal number.',
            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method must not exceed 255 characters.',
            'payment_method_details.json' => 'The payment method details must be a valid JSON string.',
            'payment_gateway.string' => 'The payment gateway must be a string.',
            'payment_gateway.max' => 'The payment gateway must not exceed 255 characters.',
            'is_installment.required' => 'The is installment field is required.',
            'is_installment.boolean' => 'The is installment must be true or false.',
            'is_installment.max' => 'The is installment must not exceed 1 characters.',
            'installment_count.integer' => 'The installment count must be an integer.',
            'installment_count.max' => 'The installment count must not exceed 11 characters.',
            'installment_frequency.string' => 'The installment frequency must be a string.',
            'installment_frequency.max' => 'The installment frequency must not exceed 255 characters.',
            'payment_date.date' => 'The payment date must be a valid date and time.',
            'due_date.date' => 'The due date must be a valid date and time.',
            'paid_at.date' => 'The paid at must be a valid date and time.',
            'cancelled_at.date' => 'The cancelled at must be a valid date and time.',
            'expires_at.date' => 'The expires at must be a valid date and time.',
            'customer_reference.string' => 'The customer reference must be a string.',
            'customer_reference.max' => 'The customer reference must not exceed 255 characters.',
            'bank_reference.string' => 'The bank reference must be a string.',
            'bank_reference.max' => 'The bank reference must not exceed 255 characters.',
            'gateway_reference.string' => 'The gateway reference must be a string.',
            'gateway_reference.max' => 'The gateway reference must not exceed 255 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'custom_fields.json' => 'The custom fields must be a valid JSON string.',
            'notes.string' => 'The notes must be a string.',
            'notes.max' => 'The notes must not exceed 65535 characters.',
            'failure_reason.string' => 'The failure reason must be a string.',
            'failure_reason.max' => 'The failure reason must not exceed 65535 characters.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
