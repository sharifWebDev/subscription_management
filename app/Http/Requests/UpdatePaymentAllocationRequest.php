<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentAllocationRequest extends FormRequest
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
            'payment_child_id' => ['sometimes', 'string', 'max:255', 'exists:payment_children,id'],
            'payment_transaction_id' => ['sometimes', 'string', 'max:255', 'exists:payment_transactions,id'],
            'allocatable_type' => ['sometimes', 'string', 'max:255'],
            'allocatable_id' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'base_amount' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'exchange_rate' => ['sometimes', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['sometimes', 'string', 'max:255'],
            'allocation_reference' => ['nullable', 'string', 'max:255'],
            'allocation_type' => ['sometimes', 'string', 'max:255'],
            'is_reversed' => ['sometimes', 'boolean', 'max:1'],
            'reversed_at' => ['nullable', 'date'],
            'reversal_id' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'json'],
            'notes' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function messages()
    {
        return [
            'payment_master_id.string' => 'The payment master id must be a string.',
            'payment_master_id.max' => 'The payment master id must not exceed 255 characters.',
            'payment_master_id.exists' => 'The selected payment master id is invalid or does not exist in payment masters.',
            'payment_child_id.string' => 'The payment child id must be a string.',
            'payment_child_id.max' => 'The payment child id must not exceed 255 characters.',
            'payment_child_id.exists' => 'The selected payment child id is invalid or does not exist in payment children.',
            'payment_transaction_id.string' => 'The payment transaction id must be a string.',
            'payment_transaction_id.max' => 'The payment transaction id must not exceed 255 characters.',
            'payment_transaction_id.exists' => 'The selected payment transaction id is invalid or does not exist in payment transactions.',
            'allocatable_type.string' => 'The allocatable type must be a string.',
            'allocatable_type.max' => 'The allocatable type must not exceed 255 characters.',
            'allocatable_id.string' => 'The allocatable id must be a string.',
            'allocatable_id.max' => 'The allocatable id must not exceed 255 characters.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.regex' => 'The amount must be a valid decimal number.',
            'base_amount.numeric' => 'The base amount must be a number.',
            'base_amount.regex' => 'The base amount must be a valid decimal number.',
            'exchange_rate.numeric' => 'The exchange rate must be a number.',
            'exchange_rate.regex' => 'The exchange rate must be a valid decimal number.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'allocation_reference.string' => 'The allocation reference must be a string.',
            'allocation_reference.max' => 'The allocation reference must not exceed 255 characters.',
            'allocation_type.string' => 'The allocation type must be a string.',
            'allocation_type.max' => 'The allocation type must not exceed 255 characters.',
            'is_reversed.boolean' => 'The is reversed must be true or false.',
            'is_reversed.max' => 'The is reversed must not exceed 1 characters.',
            'reversed_at.date' => 'The reversed at must be a valid date and time.',
            'reversal_id.string' => 'The reversal id must be a string.',
            'reversal_id.max' => 'The reversal id must not exceed 255 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'notes.string' => 'The notes must be a string.',
            'notes.max' => 'The notes must not exceed 65535 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
            'updated_at.date' => 'The updated at must be a valid date and time.',
        ];
    }
}
