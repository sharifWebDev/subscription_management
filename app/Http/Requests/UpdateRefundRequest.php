<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundRequest extends FormRequest
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
            'payment_master_id' => ['sometimes', 'string', 'max:255', 'exists:payment_masters,id'],
            'payment_transaction_id' => ['sometimes', 'string', 'max:255', 'exists:payment_transactions,id'],
            'user_id' => ['sometimes', 'string', 'max:255', 'exists:users,id'],
            'refund_number' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
            'initiated_by' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'fee' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'net_amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['sometimes', 'string', 'max:255'],
            'exchange_rate' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'reason' => ['sometimes', 'string', 'max:255'],
            'reason_details' => ['nullable', 'string', 'max:65535'],
            'customer_comments' => ['nullable', 'string', 'max:65535'],
            'requested_at' => ['sometimes', 'date'],
            'approved_at' => ['nullable', 'date'],
            'approved_by' => ['nullable', 'string', 'max:255'],
            'processed_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'failed_at' => ['nullable', 'date'],
            'gateway_refund_id' => ['nullable', 'string', 'max:255'],
            'gateway_response' => ['nullable', 'json'],
            'metadata' => ['nullable', 'json'],
            'documents' => ['nullable', 'json'],
            'processed_by' => ['nullable', 'string', 'max:255'],
            'rejection_reason' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function messages()
    {
        return [
            'payment_master_id.string' => 'The payment master id must be a string.',
            'payment_master_id.max' => 'The payment master id must not exceed 255 characters.',
            'payment_master_id.exists' => 'The selected payment master id is invalid or does not exist in payment masters.',
            'payment_transaction_id.string' => 'The payment transaction id must be a string.',
            'payment_transaction_id.max' => 'The payment transaction id must not exceed 255 characters.',
            'payment_transaction_id.exists' => 'The selected payment transaction id is invalid or does not exist in payment transactions.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'refund_number.string' => 'The refund number must be a string.',
            'refund_number.max' => 'The refund number must not exceed 255 characters.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'initiated_by.string' => 'The initiated by must be a string.',
            'initiated_by.max' => 'The initiated by must not exceed 255 characters.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'fee.numeric' => 'The fee must be a number.',
            'fee.regex' => 'The fee must be a valid decimal number.',
            'net_amount.numeric' => 'The net amount must be a number.',
            'net_amount.regex' => 'The net amount must be a valid decimal number.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'exchange_rate.numeric' => 'The exchange rate must be a number.',
            'exchange_rate.regex' => 'The exchange rate must be a valid decimal number.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason must not exceed 255 characters.',
            'reason_details.string' => 'The reason details must be a string.',
            'reason_details.max' => 'The reason details must not exceed 65535 characters.',
            'customer_comments.string' => 'The customer comments must be a string.',
            'customer_comments.max' => 'The customer comments must not exceed 65535 characters.',
            'requested_at.date' => 'The requested at must be a valid date and time.',
            'approved_at.date' => 'The approved at must be a valid date and time.',
            'approved_by.string' => 'The approved by must be a string.',
            'approved_by.max' => 'The approved by must not exceed 255 characters.',
            'processed_at.date' => 'The processed at must be a valid date and time.',
            'completed_at.date' => 'The completed at must be a valid date and time.',
            'failed_at.date' => 'The failed at must be a valid date and time.',
            'gateway_refund_id.string' => 'The gateway refund id must be a string.',
            'gateway_refund_id.max' => 'The gateway refund id must not exceed 255 characters.',
            'gateway_response.json' => 'The gateway response must be a valid JSON string.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'documents.json' => 'The documents must be a valid JSON string.',
            'processed_by.string' => 'The processed by must be a string.',
            'processed_by.max' => 'The processed by must not exceed 255 characters.',
            'rejection_reason.string' => 'The rejection reason must be a string.',
            'rejection_reason.max' => 'The rejection reason must not exceed 65535 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
