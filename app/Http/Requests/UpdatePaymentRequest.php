<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'invoice_id' => ['nullable', 'string', 'max:255', 'exists:invoices,id'],
            'user_id' => ['sometimes', 'string', 'max:255', 'exists:users,id'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'fee' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'net' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['sometimes', 'string', 'max:255'],
            'gateway' => ['sometimes', 'string', 'max:255'],
            'gateway_response' => ['nullable', 'json'],
            'payment_method' => ['nullable', 'json'],
            'processed_at' => ['nullable', 'date'],
            'refunded_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'json'],
            'fraud_indicators' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'invoice_id.string' => 'The invoice id must be a string.',
            'invoice_id.max' => 'The invoice id must not exceed 255 characters.',
            'invoice_id.exists' => 'The selected invoice id is invalid or does not exist in invoices.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'external_id.string' => 'The external id must be a string.',
            'external_id.max' => 'The external id must not exceed 255 characters.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'fee.numeric' => 'The fee must be a number.',
            'fee.regex' => 'The fee must be a valid decimal number.',
            'net.numeric' => 'The net must be a number.',
            'net.regex' => 'The net must be a valid decimal number.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'gateway.string' => 'The gateway must be a string.',
            'gateway.max' => 'The gateway must not exceed 255 characters.',
            'gateway_response.json' => 'The gateway response must be a valid JSON string.',
            'payment_method.json' => 'The payment method must be a valid JSON string.',
            'processed_at.date' => 'The processed at must be a valid date and time.',
            'refunded_at.date' => 'The refunded at must be a valid date and time.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'fraud_indicators.json' => 'The fraud indicators must be a valid JSON string.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
