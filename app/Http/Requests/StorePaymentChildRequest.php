<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentChildRequest extends FormRequest
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
            'payment_master_id' => ['required', 'string', 'max:255', 'exists:payment_masters,id'],
            'subscription_id' => ['nullable', 'string', 'max:255', 'ip', 'exists:subscriptions,id'],
            'plan_id' => ['nullable', 'string', 'max:255', 'exists:plans,id'],
            'invoice_id' => ['nullable', 'string', 'max:255', 'exists:invoices,id'],
            'item_type' => ['required', 'string', 'max:255'],
            'item_id' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535', 'ip'],
            'item_code' => ['nullable', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer', 'max:11'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'total_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'billing_cycle' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'allocated_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'is_fully_allocated' => ['nullable', 'boolean', 'max:1'],
            'metadata' => ['nullable', 'json'],
            'tax_breakdown' => ['nullable', 'json'],
            'discount_breakdown' => ['nullable', 'json'],
        ];
    }

    public function messages()
    {
        return [
            'payment_master_id.required' => 'The payment master id field is required.',
            'payment_master_id.string' => 'The payment master id must be a string.',
            'payment_master_id.max' => 'The payment master id must not exceed 255 characters.',
            'payment_master_id.exists' => 'The selected payment master id is invalid or does not exist in payment masters.',
            'subscription_id.string' => 'The subscription id must be a string.',
            'subscription_id.max' => 'The subscription id must not exceed 255 characters.',
            'subscription_id.exists' => 'The selected subscription id is invalid or does not exist in subscriptions.',
            'plan_id.string' => 'The plan id must be a string.',
            'plan_id.max' => 'The plan id must not exceed 255 characters.',
            'plan_id.exists' => 'The selected plan id is invalid or does not exist in plans.',
            'invoice_id.string' => 'The invoice id must be a string.',
            'invoice_id.max' => 'The invoice id must not exceed 255 characters.',
            'invoice_id.exists' => 'The selected invoice id is invalid or does not exist in invoices.',
            'item_type.required' => 'The item type field is required.',
            'item_type.string' => 'The item type must be a string.',
            'item_type.max' => 'The item type must not exceed 255 characters.',
            'item_id.required' => 'The item id field is required.',
            'item_id.string' => 'The item id must be a string.',
            'item_id.max' => 'The item id must not exceed 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 65535 characters.',
            'item_code.string' => 'The item code must be a string.',
            'item_code.max' => 'The item code must not exceed 255 characters.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.regex' => 'The unit price must be a valid decimal number.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.max' => 'The quantity must not exceed 11 characters.',
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
            'period_start.date' => 'The period start must be a valid date.',
            'period_end.date' => 'The period end must be a valid date.',
            'billing_cycle.string' => 'The billing cycle must be a string.',
            'billing_cycle.max' => 'The billing cycle must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 255 characters.',
            'paid_at.date' => 'The paid at must be a valid date and time.',
            'allocated_amount.required' => 'The allocated amount field is required.',
            'allocated_amount.numeric' => 'The allocated amount must be a number.',
            'allocated_amount.regex' => 'The allocated amount must be a valid decimal number.',
            'is_fully_allocated.boolean' => 'The is fully allocated must be true or false.',
            'is_fully_allocated.max' => 'The is fully allocated must not exceed 1 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'tax_breakdown.json' => 'The tax breakdown must be a valid JSON string.',
            'discount_breakdown.json' => 'The discount breakdown must be a valid JSON string.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
