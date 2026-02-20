<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'subscription_id' => ['nullable', 'string', 'max:255', 'ip', 'exists:subscriptions,id'],
            'number' => ['required', 'string', 'max:255'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount_due' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount_paid' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'amount_remaining' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'currency' => ['required', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'finalized_at' => ['nullable', 'date'],
            'line_items' => ['nullable', 'json'],
            'tax_rates' => ['nullable', 'json'],
            'discounts' => ['nullable', 'json'],
            'metadata' => ['nullable', 'json'],
            'history' => ['nullable', 'json'],
            'pdf_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user id field is required.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'number.max' => 'The number must not exceed 255 characters.',
            'external_id.string' => 'The external id must be a string.',
            'external_id.max' => 'The external id must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'subtotal.required' => 'The subtotal field is required.',
            'subtotal.numeric' => 'The subtotal must be a number.',
            'subtotal.regex' => 'The subtotal must be a valid decimal number.',
            'tax.required' => 'The tax field is required.',
            'tax.numeric' => 'The tax must be a number.',
            'tax.regex' => 'The tax must be a valid decimal number.',
            'total.required' => 'The total field is required.',
            'total.numeric' => 'The total must be a number.',
            'total.regex' => 'The total must be a valid decimal number.',
            'amount_due.required' => 'The amount due field is required.',
            'amount_due.numeric' => 'The amount due must be a number.',
            'amount_due.regex' => 'The amount due must be a valid decimal number.',
            'amount_paid.required' => 'The amount paid field is required.',
            'amount_paid.numeric' => 'The amount paid must be a number.',
            'amount_paid.regex' => 'The amount paid must be a valid decimal number.',
            'amount_remaining.numeric' => 'The amount remaining must be a number.',
            'amount_remaining.regex' => 'The amount remaining must be a valid decimal number.',
            'currency.required' => 'The currency field is required.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 255 characters.',
            'issue_date.required' => 'The issue date field is required.',
            'issue_date.date' => 'The issue date must be a valid date and time.',
            'due_date.date' => 'The due date must be a valid date and time.',
            'paid_at.date' => 'The paid at must be a valid date and time.',
            'finalized_at.date' => 'The finalized at must be a valid date and time.',
            'line_items.json' => 'The line items must be a valid JSON string.',
            'tax_rates.json' => 'The tax rates must be a valid JSON string.',
            'discounts.json' => 'The discounts must be a valid JSON string.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'history.json' => 'The history must be a valid JSON string.',
            'pdf_url.string' => 'The pdf url must be a string.',
            'pdf_url.max' => 'The pdf url must not exceed 255 characters.',
            'pdf_url.url' => 'The pdf url must be a valid URL.',
            'pdf_url.active_url' => 'The pdf url must be a active and reachable URL.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
