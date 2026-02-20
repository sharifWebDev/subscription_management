<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
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
            'type' => ['required', 'string', 'max:255'],
            'gateway' => ['required', 'string', 'max:255'],
            'gateway_customer_id' => ['nullable', 'string', 'max:255'],
            'gateway_payment_method_id' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'is_default' => ['required', 'boolean', 'max:1'],
            'is_verified' => ['required', 'boolean', 'max:1'],
            'card_last4' => ['nullable', 'string', 'max:255'],
            'card_brand' => ['nullable', 'string', 'max:255'],
            'card_exp_month' => ['nullable', 'integer', 'max:11'],
            'card_exp_year' => ['nullable', 'integer', 'max:11'],
            'card_country' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_last4' => ['nullable', 'string', 'max:255'],
            'bank_account_type' => ['nullable', 'string', 'max:255'],
            'bank_routing_number' => ['nullable', 'string', 'max:255'],
            'wallet_type' => ['nullable', 'string', 'max:255'],
            'wallet_number' => ['nullable', 'string', 'max:255'],
            'crypto_currency' => ['nullable', 'string', 'max:255'],
            'crypto_address' => ['nullable', 'string', 'max:255'],
            'encrypted_data' => ['nullable', 'json'],
            'fingerprint' => ['nullable', 'string', 'max:255'],
            'is_compromised' => ['required', 'boolean', 'max:1'],
            'metadata' => ['nullable', 'json'],
            'gateway_metadata' => ['nullable', 'json'],
            'verified_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'string', 'max:255'],
            'last_used_at' => ['nullable', 'date'],
            'usage_count' => ['required', 'integer', 'max:11'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user id field is required.',
            'user_id.string' => 'The user id must be a string.',
            'user_id.max' => 'The user id must not exceed 255 characters.',
            'user_id.exists' => 'The selected user id is invalid or does not exist in users.',
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'gateway.required' => 'The gateway field is required.',
            'gateway.string' => 'The gateway must be a string.',
            'gateway.max' => 'The gateway must not exceed 255 characters.',
            'gateway_customer_id.string' => 'The gateway customer id must be a string.',
            'gateway_customer_id.max' => 'The gateway customer id must not exceed 255 characters.',
            'gateway_payment_method_id.string' => 'The gateway payment method id must be a string.',
            'gateway_payment_method_id.max' => 'The gateway payment method id must not exceed 255 characters.',
            'nickname.string' => 'The nickname must be a string.',
            'nickname.max' => 'The nickname must not exceed 255 characters.',
            'is_default.required' => 'The is default field is required.',
            'is_default.boolean' => 'The is default must be true or false.',
            'is_default.max' => 'The is default must not exceed 1 characters.',
            'is_verified.required' => 'The is verified field is required.',
            'is_verified.boolean' => 'The is verified must be true or false.',
            'is_verified.max' => 'The is verified must not exceed 1 characters.',
            'card_last4.string' => 'The card last4 must be a string.',
            'card_last4.max' => 'The card last4 must not exceed 255 characters.',
            'card_brand.string' => 'The card brand must be a string.',
            'card_brand.max' => 'The card brand must not exceed 255 characters.',
            'card_exp_month.integer' => 'The card exp month must be an integer.',
            'card_exp_month.max' => 'The card exp month must not exceed 11 characters.',
            'card_exp_year.integer' => 'The card exp year must be an integer.',
            'card_exp_year.max' => 'The card exp year must not exceed 11 characters.',
            'card_country.string' => 'The card country must be a string.',
            'card_country.max' => 'The card country must not exceed 255 characters.',
            'bank_name.string' => 'The bank name must be a string.',
            'bank_name.max' => 'The bank name must not exceed 255 characters.',
            'bank_account_last4.string' => 'The bank account last4 must be a string.',
            'bank_account_last4.max' => 'The bank account last4 must not exceed 255 characters.',
            'bank_account_type.string' => 'The bank account type must be a string.',
            'bank_account_type.max' => 'The bank account type must not exceed 255 characters.',
            'bank_routing_number.string' => 'The bank routing number must be a string.',
            'bank_routing_number.max' => 'The bank routing number must not exceed 255 characters.',
            'wallet_type.string' => 'The wallet type must be a string.',
            'wallet_type.max' => 'The wallet type must not exceed 255 characters.',
            'wallet_number.string' => 'The wallet number must be a string.',
            'wallet_number.max' => 'The wallet number must not exceed 255 characters.',
            'crypto_currency.string' => 'The crypto currency must be a string.',
            'crypto_currency.max' => 'The crypto currency must not exceed 255 characters.',
            'crypto_address.string' => 'The crypto address must be a string.',
            'crypto_address.max' => 'The crypto address must not exceed 255 characters.',
            'encrypted_data.json' => 'The encrypted data must be a valid JSON string.',
            'fingerprint.string' => 'The fingerprint must be a string.',
            'fingerprint.max' => 'The fingerprint must not exceed 255 characters.',
            'is_compromised.required' => 'The is compromised field is required.',
            'is_compromised.boolean' => 'The is compromised must be true or false.',
            'is_compromised.max' => 'The is compromised must not exceed 1 characters.',
            'metadata.json' => 'The metadata must be a valid JSON string.',
            'gateway_metadata.json' => 'The gateway metadata must be a valid JSON string.',
            'verified_at.date' => 'The verified at must be a valid date and time.',
            'verified_by.string' => 'The verified by must be a string.',
            'verified_by.max' => 'The verified by must not exceed 255 characters.',
            'last_used_at.date' => 'The last used at must be a valid date and time.',
            'usage_count.required' => 'The usage count field is required.',
            'usage_count.integer' => 'The usage count must be an integer.',
            'usage_count.max' => 'The usage count must not exceed 11 characters.',
            'created_by.string' => 'The created by must be a string.',
            'created_by.max' => 'The created by must not exceed 255 characters.',
            'updated_by.string' => 'The updated by must be a string.',
            'updated_by.max' => 'The updated by must not exceed 255 characters.',
        ];
    }
}
