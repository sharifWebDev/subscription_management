<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentWebhookLogRequest extends FormRequest
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
            'payment_gateway_id' => ['nullable', 'string', 'max:255', 'exists:payment_gateways,id'],
            'payment_transaction_id' => ['nullable', 'string', 'max:255', 'exists:payment_transactions,id'],
            'gateway' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:255'],
            'webhook_id' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'json'],
            'headers' => ['nullable', 'json'],
            'response_code' => ['nullable', 'integer', 'max:11'],
            'response_body' => ['nullable', 'string', 'max:65535'],
            'status' => ['required', 'string', 'max:255'],
            'processing_error' => ['nullable', 'string', 'max:65535'],
            'retry_count' => ['required', 'integer', 'max:11'],
            'next_retry_at' => ['nullable', 'date'],
            'received_at' => ['required', 'date'],
            'processed_at' => ['nullable', 'date'],
            'ip_address' => ['nullable', 'string', 'max:255', 'ip'],
            'is_verified' => ['required', 'boolean', 'max:1'],
            'verification_error' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'payment_gateway_id.string' => 'The payment gateway id must be a string.',
            'payment_gateway_id.max' => 'The payment gateway id must not exceed 255 characters.',
            'payment_gateway_id.exists' => 'The selected payment gateway id is invalid or does not exist in payment gateways.',
            'payment_transaction_id.string' => 'The payment transaction id must be a string.',
            'payment_transaction_id.max' => 'The payment transaction id must not exceed 255 characters.',
            'payment_transaction_id.exists' => 'The selected payment transaction id is invalid or does not exist in payment transactions.',
            'gateway.required' => 'The gateway field is required.',
            'gateway.string' => 'The gateway must be a string.',
            'gateway.max' => 'The gateway must not exceed 255 characters.',
            'event_type.required' => 'The event type field is required.',
            'event_type.string' => 'The event type must be a string.',
            'event_type.max' => 'The event type must not exceed 255 characters.',
            'webhook_id.string' => 'The webhook id must be a string.',
            'webhook_id.max' => 'The webhook id must not exceed 255 characters.',
            'reference_id.string' => 'The reference id must be a string.',
            'reference_id.max' => 'The reference id must not exceed 255 characters.',
            'payload.json' => 'The payload must be a valid JSON string.',
            'headers.json' => 'The headers must be a valid JSON string.',
            'response_code.integer' => 'The response code must be an integer.',
            'response_code.max' => 'The response code must not exceed 11 characters.',
            'response_body.string' => 'The response body must be a string.',
            'response_body.max' => 'The response body must not exceed 65535 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'processing_error.string' => 'The processing error must be a string.',
            'processing_error.max' => 'The processing error must not exceed 65535 characters.',
            'retry_count.required' => 'The retry count field is required.',
            'retry_count.integer' => 'The retry count must be an integer.',
            'retry_count.max' => 'The retry count must not exceed 11 characters.',
            'next_retry_at.date' => 'The next retry at must be a valid date and time.',
            'received_at.required' => 'The received at field is required.',
            'received_at.date' => 'The received at must be a valid date and time.',
            'processed_at.date' => 'The processed at must be a valid date and time.',
            'ip_address.string' => 'The ip address must be a string.',
            'ip_address.max' => 'The ip address must not exceed 255 characters.',
            'is_verified.required' => 'The is verified field is required.',
            'is_verified.boolean' => 'The is verified must be true or false.',
            'is_verified.max' => 'The is verified must not exceed 1 characters.',
            'verification_error.string' => 'The verification error must be a string.',
            'verification_error.max' => 'The verification error must not exceed 255 characters.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
