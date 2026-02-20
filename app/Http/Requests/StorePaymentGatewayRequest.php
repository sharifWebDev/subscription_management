<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentGatewayRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean', 'max:1'],
            'is_test_mode' => ['required', 'boolean', 'max:1'],
            'supports_recurring' => ['required', 'boolean', 'max:1'],
            'supports_refunds' => ['required', 'boolean', 'max:1'],
            'supports_installments' => ['required', 'boolean', 'max:1'],
            'api_key' => ['nullable', 'string', 'max:65535'],
            'api_secret' => ['nullable', 'string', 'max:65535'],
            'webhook_secret' => ['nullable', 'string', 'max:65535'],
            'merchant_id' => ['nullable', 'string', 'max:65535'],
            'store_id' => ['nullable', 'string', 'max:65535'],
            'store_password' => ['nullable', 'string', 'max:65535'],
            'base_url' => ['nullable', 'string', 'max:255'],
            'callback_url' => ['nullable', 'string', 'max:255'],
            'webhook_url' => ['nullable', 'string', 'max:255'],
            'supported_currencies' => ['nullable', 'json'],
            'supported_countries' => ['nullable', 'json'],
            'excluded_countries' => ['nullable', 'json'],
            'percentage_fee' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'fixed_fee' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'fee_currency' => ['required', 'string', 'max:255'],
            'fee_structure' => ['nullable', 'json'],
            'config' => ['nullable', 'json'],
            'metadata' => ['nullable', 'json'],
            'settlement_days' => ['required', 'integer', 'max:11'],
            'refund_days' => ['required', 'integer', 'max:11'],
            'min_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'max_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active must be true or false.',
            'is_active.max' => 'The is active must not exceed 1 characters.',
            'is_test_mode.required' => 'The is test mode field is required.',
            'is_test_mode.boolean' => 'The is test mode must be true or false.',
            'is_test_mode.max' => 'The is test mode must not exceed 1 characters.',
            'supports_recurring.required' => 'The supports recurring field is required.',
            'supports_recurring.boolean' => 'The supports recurring must be true or false.',
            'supports_recurring.max' => 'The supports recurring must not exceed 1 characters.',
            'supports_refunds.required' => 'The supports refunds field is required.',
            'supports_refunds.boolean' => 'The supports refunds must be true or false.',
            'supports_refunds.max' => 'The supports refunds must not exceed 1 characters.',
            'supports_installments.required' => 'The supports installments field is required.',
            'supports_installments.boolean' => 'The supports installments must be true or false.',
            'supports_installments.max' => 'The supports installments must not exceed 1 characters.',
            'api_key.string' => 'The api key must be a string.',
            'api_key.max' => 'The api key must not exceed 65535 characters.',
            'api_secret.string' => 'The api secret must be a string.',
            'api_secret.max' => 'The api secret must not exceed 65535 characters.',
            'webhook_secret.string' => 'The webhook secret must be a string.',
            'webhook_secret.max' => 'The webhook secret must not exceed 65535 characters.',
            'merchant_id.string' => 'The merchant id must be a string.',
            'merchant_id.max' => 'The merchant id must not exceed 65535 characters.',
            'store_id.string' => 'The store id must be a string.',
            'store_id.max' => 'The store id must not exceed 65535 characters.',
            'store_password.string' => 'The store password must be a string.',
            'store_password.max' => 'The store password must not exceed 65535 characters.',
            'base_url.string' => 'The base url must be a string.',
            'base_url.max' => 'The base url must not exceed 255 characters.',
            'base_url.url' => 'The base url must be a valid URL.',
            'base_url.active_url' => 'The base url must be a active and reachable URL.',
            'callback_url.string' => 'The callback url must be a string.',
            'callback_url.max' => 'The callback url must not exceed 255 characters.',
            'callback_url.url' => 'The callback url must be a valid URL.',
            'callback_url.active_url' => 'The callback url must be a active and reachable URL.',
            'webhook_url.string' => 'The webhook url must be a string.',
            'webhook_url.max' => 'The webhook url must not exceed 255 characters.',
            'webhook_url.url' => 'The webhook url must be a valid URL.',
            'webhook_url.active_url' => 'The webhook url must be a active and reachable URL.',
            'supported_currencies.json' => 'The supported currencies must be a valid JSON string.',
            'supported_countries.json' => 'The supported countries must be a valid JSON string.',
            'excluded_countries.json' => 'The excluded countries must be a valid JSON string.',
            'percentage_fee.required' => 'The percentage fee field is required.',
            'percentage_fee.numeric' => 'The percentage fee must be a number.',
            'percentage_fee.regex' => 'The percentage fee must be a valid decimal number.',
            'fixed_fee.required' => 'The fixed fee field is required.',
            'fixed_fee.numeric' => 'The fixed fee must be a number.',
            'fixed_fee.regex' => 'The fixed fee must be a valid decimal number.',
            'fee_currency.required' => 'The fee currency field is required.',
            'fee_currency.string' => 'The fee currency must be a string.',
            'fee_currency.max' => 'The fee currency must not exceed 255 characters.',
            'fee_structure.json' => 'The fee structure must be a valid JSON string.',
            'config.json' => 'The config must be a valid JSON string.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'settlement_days.required' => 'The settlement days field is required.',
            'settlement_days.integer' => 'The settlement days must be an integer.',
            'settlement_days.max' => 'The settlement days must not exceed 11 characters.',
            'refund_days.required' => 'The refund days field is required.',
            'refund_days.integer' => 'The refund days must be an integer.',
            'refund_days.max' => 'The refund days must not exceed 11 characters.',
            'min_amount.required' => 'The min amount field is required.',
            'min_amount.numeric' => 'The min amount must be a number.',
            'min_amount.regex' => 'The min amount must be a valid decimal number.',
            'max_amount.required' => 'The max amount field is required.',
            'max_amount.numeric' => 'The max amount must be a number.',
            'max_amount.regex' => 'The max amount must be a valid decimal number.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
